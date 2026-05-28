<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Zapato;
use App\Traits\FuncionesDigitalizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VentasController extends Controller
{
    use FuncionesDigitalizacion;

    /**
     * Pantalla principal del punto de venta.
     * Si viene ?codigo=XXX busca el zapato y lo muestra listo para registrar.
     */
    public function index(Request $request)
    {
        $user    = auth()->user();
        $zapato  = null;
        $error   = null;

        if ($request->filled('codigo')) {
            $codigo = strtoupper(trim($request->codigo));

            $zapato = Zapato::with(['categoria', 'tipo', 'talla', 'sucursal'])
                ->where('codigo_unico', $codigo)
                ->first();

            if (! $zapato) {
                $error = "No se encontró ningún zapato con el código «{$codigo}».";
            } elseif ($zapato->estado === 'vendido') {
                $error   = "El zapato {$codigo} ya fue vendido anteriormente.";
                $zapato  = null;
            } elseif ($zapato->estado !== 'en_inventario') {
                $error   = "El zapato {$codigo} no está disponible (estado: {$zapato->estado}).";
                $zapato  = null;
            } elseif ($user->isEncargado() && $user->sucursal_id && $zapato->sucursal_id !== $user->sucursal_id) {
                $error   = "Este zapato pertenece a otra sucursal.";
                $zapato  = null;
            }
        }

        // Ventas del día (filtradas por sucursal si es encargado)
        $ventasHoy = Venta::with(['zapato.categoria', 'zapato.tipo', 'zapato.talla', 'usuario'])
            ->when($user->isEncargado() && $user->sucursal_id,
                fn ($q) => $q->where('sucursal_id', $user->sucursal_id)
            )
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $statsHoy = [
            'cantidad' => $ventasHoy->count(),
            'ingresos' => $ventasHoy->sum('precio_venta'),
            'rebajado' => $ventasHoy->sum(fn ($v) => max(0, $v->precio_lista - $v->precio_venta)),
        ];

        return view('ventas.index', compact('zapato', 'error', 'ventasHoy', 'statsHoy'));
    }

    /**
     * Registrar la venta: marcar zapato como vendido y crear el registro.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'zapato_id'    => 'required|exists:zapato,id',
            'precio_venta' => 'required|numeric|min:0.01',
            'notas'        => 'nullable|string|max:255',
        ]);

        $zapato = Zapato::findOrFail($data['zapato_id']);
        $user   = auth()->user();

        // Validaciones de seguridad
        if ($zapato->estado !== 'en_inventario') {
            return back()->with('warning', "El zapato {$zapato->codigo_unico} ya no está disponible.");
        }

        if ($user->isEncargado() && $user->sucursal_id && $zapato->sucursal_id !== $user->sucursal_id) {
            return back()->with('warning', 'No puedes vender zapatos de otra sucursal.');
        }

        try {
            DB::transaction(function () use ($zapato, $data, $user) {
                // 1. Crear el registro de venta
                Venta::create([
                    'zapato_id'    => $zapato->id,
                    'sucursal_id'  => $zapato->sucursal_id,
                    'usuario_id'   => $user->id,
                    'precio_lista' => $zapato->precio_lista,
                    'precio_venta' => $data['precio_venta'],
                    'notas'        => $data['notas'] ?? null,
                ]);

                // 2. Marcar zapato como vendido
                $zapato->update(['estado' => 'vendido']);
            });
        } catch (\Exception $e) {
            Log::error('VentasController@store — error al registrar venta', [
                'zapato_id' => $zapato->id,
                'error'     => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocurrió un error al registrar la venta. Intenta de nuevo.');
        }

        $diferencia = (float) $data['precio_venta'] - (float) $zapato->precio_lista;
        $msg = $diferencia >= 0
            ? "Venta registrada. {$zapato->codigo_unico} — Q" . number_format($data['precio_venta'], 2)
            : "Venta registrada con rebaja de Q" . number_format(abs($diferencia), 2) . ". {$zapato->codigo_unico}";

        return redirect()->route('ventas.index')->with('success', $msg);
    }
}
