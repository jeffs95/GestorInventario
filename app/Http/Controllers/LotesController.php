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
            'costales.zapatos',
            'aperturas.costales',
            'aperturas.zapatos',
        ]);

        return view('lotes.show', compact('lote'));
    }
}
