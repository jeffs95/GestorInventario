<?php

namespace App\Http\Controllers;

use App\Models\Apertura;
use App\Models\CategoriaZapato;
use App\Models\Lote;
use App\Models\TipoZapato;
use App\Models\Zapato;
use App\Models\ZapatoLote;
use Illuminate\Http\Request;

class AperturasController extends Controller
{
    /** Formulario: seleccionar lote + costales a abrir */
    public function create(Request $request)
    {
        $lote = null;

        if ($request->filled('lote_id')) {
            $lote = Lote::with([
                'proveedor',
                'sucursalDestino',
                'costales' => fn ($q) => $q->where('estado', 'recibido')->orderBy('numero_costal'),
            ])->findOrFail($request->lote_id);
        }

        $lotes = Lote::with('proveedor')
            ->whereHas('costales', fn ($q) => $q->where('estado', 'recibido'))
            ->orderByDesc('fecha_compra')
            ->get();

        return view('aperturas.create', compact('lote', 'lotes'));
    }

    /** Guardar apertura: crear registro + marcar costales como en_clasificacion */
    public function store(Request $request)
    {
        $request->validate([
            'lote_id'     => 'required|exists:lote,id',
            'costales'    => 'required|array|min:1',
            'costales.*'  => 'exists:costal,id',
            'fecha'       => 'required|date',
            'notas'       => 'nullable|string',
        ]);

        $apertura = Apertura::create([
            'lote_id'    => $request->lote_id,
            'fecha'      => $request->fecha,
            'notas'      => $request->notas,
            'estado'     => 'abierta',
            'usuario_id' => auth()->id(),
        ]);

        // Adjuntar costales y marcarlos en_clasificacion
        $apertura->costales()->attach($request->costales);

        \App\Models\Costal::whereIn('id', $request->costales)
            ->where('estado', 'recibido')
            ->update(['estado' => 'en_clasificacion']);

        $count = count($request->costales);

        return redirect()->route('aperturas.show', $apertura)
            ->with('success', "Apertura creada con {$count} costal" . ($count !== 1 ? 'es' : '') . ". Ahora clasifica los zapatos.");
    }

    /** Detalle de apertura + formularios de clasificación */
    public function show(Apertura $apertura)
    {
        $apertura->load([
            'lote.proveedor',
            'lote.sucursalDestino',
            'costales',
            'zapatos.categoria',
            'zapatos.tipo',
            'zapatoLotes.categoria',
            'zapatoLotes.tipo',
        ]);

        $categorias = CategoriaZapato::where('activo', true)->orderBy('nombre')->get();
        $tipos      = TipoZapato::where('activo', true)->orderBy('nombre')->get();
        $stats      = $apertura->stats;

        return view('aperturas.show', compact('apertura', 'categorias', 'tipos', 'stats'));
    }

    /** Registrar zapatos desde la apertura (batch o individual) */
    public function clasificar(Request $request, Apertura $apertura)
    {
        if ($apertura->estado === 'clasificada') {
            return back()->with('warning', 'Esta apertura ya está cerrada.');
        }

        $tipo = $request->input('tipo_clasificacion');

        // Sucursal: tomamos la del lote
        $apertura->load('lote');
        $sucursalId = $apertura->lote->sucursal_destino_id;
        $ref        = "A{$apertura->id}"; // REG-A3-0001

        if ($tipo === 'regular') {
            $data = $request->validate([
                'categoria_id' => 'required|exists:categoria_zapato,id',
                'tipo_id'      => 'required|exists:tipo_zapato,id',
                'talla'        => 'required|string|max:10',
                'cantidad'     => 'required|integer|min:1|max:1000',
                'precio_lista' => 'required|numeric|min:0.01',
                'notas'        => 'nullable|string',
            ]);

            for ($i = 0; $i < $data['cantidad']; $i++) {
                Zapato::create([
                    'codigo_unico'  => Zapato::generarCodigo($ref, 'regular'),
                    'apertura_id'   => $apertura->id,
                    'sucursal_id'   => $sucursalId,
                    'categoria_id'  => $data['categoria_id'],
                    'tipo_id'       => $data['tipo_id'],
                    'talla'         => $data['talla'],
                    'clasificacion' => 'regular',
                    'precio_lista'  => $data['precio_lista'],
                    'estado'        => 'en_inventario',
                    'notas'         => $data['notas'] ?? null,
                    'usuario_id'    => auth()->id(),
                ]);
            }

            $mensaje = "{$data['cantidad']} zapatos regulares agregados al inventario.";

        } else {
            // Primera: conteo intermedio en zapato_lote.
            // NO se crean registros individuales en zapato todavía.
            // Eso ocurre después de que el zapato esté preparado (lavado/lustre).
            $data = $request->validate([
                'categoria_id'   => 'required|exists:categoria_zapato,id',
                'tipo_id'        => 'required|exists:tipo_zapato,id',
                'talla'          => 'required|string|max:10',
                'clasificacion'  => 'required|in:primera_lavado,primera_lustre',
                'cantidad'       => 'required|integer|min:1|max:1000',
                'precio_estimado'=> 'nullable|numeric|min:0',
                'notas'          => 'nullable|string',
            ]);

            ZapatoLote::create([
                'apertura_id'         => $apertura->id,
                'categoria_id'        => $data['categoria_id'],
                'tipo_id'             => $data['tipo_id'],
                'talla'               => $data['talla'],
                'clasificacion'       => $data['clasificacion'],
                'cantidad_contada'    => $data['cantidad'],
                'cantidad_registrada' => 0,
                'precio_estimado'     => $data['precio_estimado'] ?? null,
                'estado'              => 'contado',
                'notas'               => $data['notas'] ?? null,
                'usuario_id'          => auth()->id(),
            ]);

            $label   = $data['clasificacion'] === 'primera_lavado' ? 'Primera Lavado' : 'Primera Lustre';
            $mensaje = "{$data['cantidad']} {$label} registrados como conteo. Pendientes de preparación.";
        }

        return redirect()->route('aperturas.show', $apertura)
            ->with('success', $mensaje);
    }

    /** Cerrar apertura: marcar todos los costales como clasificados */
    public function cerrar(Apertura $apertura)
    {
        if ($apertura->estado === 'clasificada') {
            return back()->with('info', 'Esta apertura ya estaba cerrada.');
        }

        $apertura->update(['estado' => 'clasificada']);

        // Marcar costales como clasificados
        $ids = $apertura->costales()->pluck('costal.id');
        \App\Models\Costal::whereIn('id', $ids)->update(['estado' => 'clasificado']);

        return redirect()->route('aperturas.show', $apertura)
            ->with('success', 'Apertura cerrada. Todos los costales quedan clasificados.');
    }
}
