<?php

namespace App\Http\Controllers;

use App\Models\CategoriaZapato;
use App\Models\Costal;
use App\Models\Proveedor;
use App\Models\Sucursal;
use App\Models\TipoZapato;
use App\Models\Zapato;
use Illuminate\Http\Request;

class CostalesController extends Controller
{
    public function index()
    {
        $costales = Costal::with(['proveedor', 'sucursalDestino'])
            ->withCount('zapatos')
            ->orderByDesc('fecha_compra')
            ->paginate(15);

        return view('costales.index', compact('costales'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $sucursales  = Sucursal::where('activo', true)->orderBy('nombre')->get();

        return view('costales.create', compact('proveedores', 'sucursales'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proveedor_id'       => 'required|exists:proveedor,id',
            'sucursal_destino_id'=> 'required|exists:sucursal,id',
            'peso_libras'        => 'required|numeric|min:0.01',
            'precio_por_libra'   => 'required|numeric|min:0.01',
            'fecha_compra'       => 'required|date',
            'notas'              => 'nullable|string',
        ]);

        $data['usuario_id'] = auth()->id();
        $data['costo_total'] = $data['peso_libras'] * $data['precio_por_libra'];

        $costal = Costal::create($data);

        return redirect()->route('costales.show', $costal)
            ->with('success', 'Costal registrado. Ahora clasifica los zapatos.');
    }

    public function show(Costal $costal)
    {
        $costal->load(['proveedor', 'sucursalDestino', 'zapatos.categoria', 'zapatos.tipo']);
        $categorias = CategoriaZapato::where('activo', true)->orderBy('nombre')->get();
        $tipos      = TipoZapato::where('activo', true)->orderBy('nombre')->get();

        $stats = [
            'total'           => $costal->zapatos->count(),
            'regulares'       => $costal->zapatos->where('clasificacion', 'regular')->count(),
            'primera_lavado'  => $costal->zapatos->where('clasificacion', 'primera_lavado')->count(),
            'primera_lustre'  => $costal->zapatos->where('clasificacion', 'primera_lustre')->count(),
        ];

        return view('costales.show', compact('costal', 'categorias', 'tipos', 'stats'));
    }

    public function clasificar(Request $request, Costal $costal)
    {
        $tipo_clasificacion = $request->input('tipo_clasificacion');

        if ($tipo_clasificacion === 'regular') {
            $data = $request->validate([
                'categoria_id'  => 'required|exists:categoria_zapato,id',
                'tipo_id'       => 'required|exists:tipo_zapato,id',
                'talla'         => 'required|string|max:10',
                'cantidad'      => 'required|integer|min:1|max:500',
                'precio_lista'  => 'required|numeric|min:0.01',
                'notas'         => 'nullable|string',
            ]);

            $creados = 0;
            for ($i = 0; $i < $data['cantidad']; $i++) {
                Zapato::create([
                    'codigo_unico'  => Zapato::generarCodigo((string) $costal->id, 'regular'),
                    'costal_id'     => $costal->id,
                    'sucursal_id'   => $costal->sucursal_destino_id,
                    'categoria_id'  => $data['categoria_id'],
                    'tipo_id'       => $data['tipo_id'],
                    'talla'         => $data['talla'],
                    'clasificacion' => 'regular',
                    'precio_lista'  => $data['precio_lista'],
                    'estado'        => 'en_inventario',
                    'notas'         => $data['notas'] ?? null,
                    'usuario_id'    => auth()->id(),
                ]);
                $creados++;
            }

            $mensaje = "{$creados} zapatos regulares agregados al inventario.";

        } else {
            $data = $request->validate([
                'categoria_id'      => 'required|exists:categoria_zapato,id',
                'tipo_id'           => 'required|exists:tipo_zapato,id',
                'talla'             => 'required|string|max:10',
                'clasificacion'     => 'required|in:primera_lavado,primera_lustre',
                'precio_lista'      => 'required|numeric|min:0.01',
                'notas'             => 'nullable|string',
            ]);

            $estadoInicial = 'en_proceso';

            Zapato::create([
                'codigo_unico'  => Zapato::generarCodigo((string) $costal->id, $data['clasificacion']),
                'costal_id'     => $costal->id,
                'sucursal_id'   => $costal->sucursal_destino_id,
                'categoria_id'  => $data['categoria_id'],
                'tipo_id'       => $data['tipo_id'],
                'talla'         => $data['talla'],
                'clasificacion' => $data['clasificacion'],
                'precio_lista'  => $data['precio_lista'],
                'estado'        => $estadoInicial,
                'notas'         => $data['notas'] ?? null,
                'usuario_id'    => auth()->id(),
            ]);

            $mensaje = 'Zapato de primera registrado en proceso de preparación.';
        }

        if ($costal->estado === 'recibido') {
            $costal->update(['estado' => 'en_clasificacion']);
        }

        return redirect()->route('costales.show', $costal)
            ->with('success', $mensaje);
    }

    public function cerrarClasificacion(Costal $costal)
    {
        $costal->update(['estado' => 'clasificado']);

        return redirect()->route('costales.show', $costal)
            ->with('success', 'Clasificación cerrada. El costal queda marcado como clasificado.');
    }
}
