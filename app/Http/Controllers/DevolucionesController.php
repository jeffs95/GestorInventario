<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Sucursal;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevolucionesController extends Controller
{
    /**
     * Formulario para registrar una devolución.
     *
     * Acepta tres formas de entrada:
     *   1. ?venta_id=X      → desde el historial de ventas
     *   2. ?codigo=REG-…    → escaneando el barcode del recibo
     *   3. Sin parámetros   → muestra formulario de búsqueda por código
     */
    public function create(Request $request)
    {
        $user  = auth()->user();
        $venta = null;
        $error = null;

        // ── Resolver por código de zapato ─────────────────────────────────
        if ($request->filled('codigo') && ! $request->filled('venta_id')) {
            $codigo = strtoupper(trim($request->codigo));
            $zapato = \App\Models\Zapato::with(['venta.devolucion', 'venta.sucursal', 'venta.usuario'])
                ->where('codigo_unico', $codigo)
                ->first();

            if (! $zapato) {
                $error = "No se encontró ningún zapato con el código «{$codigo}».";
            } elseif (! $zapato->venta) {
                $error = "El zapato {$codigo} no tiene ninguna venta registrada.";
            } else {
                // Redirigir a esta misma ruta con venta_id para uniformizar el flujo
                return redirect()->route('devoluciones.create', ['venta_id' => $zapato->venta->id]);
            }

            // Mostrar el buscador con el error
            return view('devoluciones.create', ['venta' => null, 'error' => $error]);
        }

        // ── Sin parámetros → mostrar buscador ────────────────────────────
        if (! $request->filled('venta_id')) {
            return view('devoluciones.create', ['venta' => null, 'error' => null]);
        }

        // ── Resolver por venta_id ─────────────────────────────────────────
        $request->validate(['venta_id' => 'required|exists:venta,id']);

        $venta = Venta::with([
            'zapato.categoria',
            'zapato.tipo',
            'zapato.talla',
            'zapato.sucursal',
            'zapato.fotos',
            'sucursal',
            'usuario',
            'devolucion',
        ])->findOrFail($request->venta_id);

        // Validaciones de acceso
        if ($venta->devolucion) {
            return redirect()->route('ventas.historial')
                ->with('warning', "La venta {$venta->zapato->codigo_unico} ya tiene una devolución registrada.");
        }

        if ($user->isEncargado() && $user->sucursal_id && $venta->sucursal_id !== $user->sucursal_id) {
            return redirect()->route('ventas.historial')
                ->with('warning', 'No puedes registrar devoluciones de otra sucursal.');
        }

        return view('devoluciones.create', compact('venta'));
    }

    /**
     * Guardar la devolución.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'venta_id'           => 'required|exists:venta,id',
            'motivo'             => 'required|in:no_sirve,talla_incorrecta,cambio_opinion,otro',
            'notas'              => 'nullable|string|max:500',
            'monto_devuelto'     => 'required|numeric|min:0',
            'regresa_inventario' => 'required|boolean',
        ]);

        $venta = Venta::with(['zapato', 'devolucion'])->findOrFail($data['venta_id']);
        $user  = auth()->user();

        if ($venta->devolucion) {
            return back()->with('warning', 'Esta venta ya tiene una devolución registrada.');
        }

        if ($user->isEncargado() && $user->sucursal_id && $venta->sucursal_id !== $user->sucursal_id) {
            return back()->with('warning', 'No puedes registrar devoluciones de otra sucursal.');
        }

        try {
            DB::transaction(function () use ($venta, $data, $user) {
                // 1. Registrar la devolución
                Devolucion::create([
                    'venta_id'           => $venta->id,
                    'zapato_id'          => $venta->zapato_id,
                    'sucursal_id'        => $venta->sucursal_id,
                    'usuario_id'         => $user->id,
                    'motivo'             => $data['motivo'],
                    'notas'              => $data['notas'] ?? null,
                    'monto_devuelto'     => $data['monto_devuelto'],
                    'regresa_inventario' => (bool) $data['regresa_inventario'],
                ]);

                // 2. Actualizar estado del zapato
                $nuevoEstado = (bool) $data['regresa_inventario'] ? 'en_inventario' : 'devuelto';
                $venta->zapato->update(['estado' => $nuevoEstado]);
            });
        } catch (\Exception $e) {
            Log::error('DevolucionesController@store — error al registrar devolución', [
                'venta_id' => $venta->id,
                'error'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocurrió un error al registrar la devolución. Intenta de nuevo.');
        }

        $zapato = $venta->zapato;
        $estado = (bool) $data['regresa_inventario'] ? 'volvió al inventario' : 'marcado como devuelto';
        $msg = "Devolución registrada. {$zapato->codigo_unico} — {$estado}.";
        if ((float) $data['monto_devuelto'] > 0) {
            $msg .= " Reembolso: Q" . number_format($data['monto_devuelto'], 2) . ".";
        }

        return redirect()->route('ventas.historial')->with('success', $msg);
    }

    /**
     * Historial de devoluciones.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $desdeDefault = now()->startOfMonth()->toDateString();
        $hastaDefault = now()->toDateString();

        $desde = $request->filled('fecha_desde') ? $request->fecha_desde : $desdeDefault;
        $hasta = $request->filled('fecha_hasta') ? $request->fecha_hasta : $hastaDefault;

        $query = Devolucion::with([
            'venta',
            'zapato.categoria',
            'zapato.tipo',
            'zapato.talla',
            'sucursal',
            'usuario',
        ])
        ->whereDate('created_at', '>=', $desde)
        ->whereDate('created_at', '<=', $hasta);

        if ($user->isEncargado() && $user->sucursal_id) {
            $query->where('sucursal_id', $user->sucursal_id);
        }
        if ($user->isDueno() && $request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }
        if ($request->filled('motivo')) {
            $query->where('motivo', $request->motivo);
        }

        $devoluciones = $query->orderByDesc('created_at')->get();

        $stats = [
            'total'           => $devoluciones->count(),
            'monto_devuelto'  => $devoluciones->sum('monto_devuelto'),
            'a_inventario'    => $devoluciones->where('regresa_inventario', true)->count(),
            'fuera'           => $devoluciones->where('regresa_inventario', false)->count(),
        ];

        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();

        return view('devoluciones.index', compact(
            'devoluciones', 'stats',
            'sucursales',
            'desde', 'hasta'
        ));
    }
}
