<?php

namespace App\Http\Controllers;

use App\Models\Costal;
use App\Models\Lote;
use App\Models\Proveedor;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class LotesController extends Controller
{
    public function index()
    {
        $lotes = Lote::with(['proveedor', 'sucursalDestino'])
            ->withCount('costales')
            ->orderByDesc('fecha_compra')
            ->paginate(15);

        return view('lotes.index', compact('lotes'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $sucursales  = Sucursal::where('activo', true)->orderBy('nombre')->get();

        return view('lotes.create', compact('proveedores', 'sucursales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id'                    => 'required|exists:proveedor,id',
            'sucursal_destino_id'              => 'required|exists:sucursal,id',
            'fecha_compra'                     => 'required|date',
            'precio_por_libra'                 => 'required|numeric|min:0.01',
            'notas'                            => 'nullable|string',
            'costales'                         => 'required|array|min:1',
            'costales.*.peso_libras'           => 'required|numeric|min:0.01',
            'costales.*.precio_por_libra'      => 'required|numeric|min:0.01',
            'costales.*.notas'                 => 'nullable|string',
        ]);

        $lote = Lote::create([
            'proveedor_id'        => $request->proveedor_id,
            'sucursal_destino_id' => $request->sucursal_destino_id,
            'fecha_compra'        => $request->fecha_compra,
            'precio_por_libra'    => $request->precio_por_libra,
            'notas'               => $request->notas,
            'usuario_id'          => auth()->id(),
        ]);

        foreach ($request->costales as $costalData) {
            Costal::create([
                'lote_id'             => $lote->id,
                'proveedor_id'        => $lote->proveedor_id,
                'sucursal_destino_id' => $lote->sucursal_destino_id,
                'fecha_compra'        => $lote->fecha_compra,
                'peso_libras'         => $costalData['peso_libras'],
                'precio_por_libra'    => $costalData['precio_por_libra'],
                'notas'               => $costalData['notas'] ?? null,
                'estado'              => 'recibido',
                'usuario_id'          => auth()->id(),
            ]);
        }

        return redirect()->route('lotes.show', $lote)
            ->with('success', "Lote {$lote->numero_lote} registrado con " . count($request->costales) . " costales.");
    }

    public function show(Lote $lote)
    {
        $lote->load([
            'proveedor',
            'sucursalDestino',
            'costales.zapatos.venta',
            'aperturas.costales',
            'aperturas.zapatos.venta',
        ]);

        // ── Rentabilidad ─────────────────────────────────────────────────────
        // Recopilar todos los zapatos únicos del lote (regular + primera)
        $todosZapatos = collect();
        foreach ($lote->costales as $costal) {
            $todosZapatos = $todosZapatos->merge($costal->zapatos);
        }
        foreach ($lote->aperturas as $apertura) {
            $todosZapatos = $todosZapatos->merge($apertura->zapatos);
        }
        $todosZapatos = $todosZapatos->unique('id');

        $costoTotal      = (float) $lote->costo_total;
        $zapVendidos     = $todosZapatos->where('estado', 'vendido');
        $zapInventario   = $todosZapatos->where('estado', 'en_inventario');
        $zapSinPrecio    = $todosZapatos->where('estado', 'pendiente_precio');

        $ingresosVentas  = $zapVendidos->sum(fn ($z) => (float) ($z->venta?->precio_venta ?? 0));
        $valorInventario = $zapInventario->sum(fn ($z) => (float) $z->precio_lista);

        $gananciaReal      = $ingresosVentas - $costoTotal;
        $gananciaPotencial = ($ingresosVentas + $valorInventario) - $costoTotal;
        $pctRecuperado     = $costoTotal > 0 ? ($ingresosVentas / $costoTotal) * 100 : 0;

        $rentabilidad = [
            'costo_total'        => $costoTotal,
            'ingresos_ventas'    => $ingresosVentas,
            'valor_inventario'   => $valorInventario,
            'ganancia_real'      => $gananciaReal,
            'ganancia_potencial' => $gananciaPotencial,
            'pct_recuperado'     => $pctRecuperado,
            'total_zapatos'      => $todosZapatos->count(),
            'vendidos'           => $zapVendidos->count(),
            'en_inventario'      => $zapInventario->count(),
            'sin_precio'         => $zapSinPrecio->count(),
        ];

        return view('lotes.show', compact('lote', 'rentabilidad'));
    }
}
