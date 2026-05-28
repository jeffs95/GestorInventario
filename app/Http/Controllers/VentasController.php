<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\User;
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

    // =========================================================================
    // RECIBO DE VENTA
    // =========================================================================

    /**
     * Pantalla imprimible del recibo de venta (con barcode).
     */
    public function recibo(Venta $venta)
    {
        $user = auth()->user();

        if ($user->isEncargado() && $user->sucursal_id && $venta->sucursal_id !== $user->sucursal_id) {
            abort(403, 'No tienes acceso a esta venta.');
        }

        $venta->load([
            'zapato.categoria',
            'zapato.tipo',
            'zapato.talla',
            'zapato.fotos',
            'sucursal',
            'usuario',
        ]);

        return view('ventas.recibo', compact('venta'));
    }

    // =========================================================================
    // HISTORIAL DE VENTAS — cuaderno digital día a día
    // =========================================================================

    /**
     * Historial completo de ventas agrupadas por día (estilo cuaderno).
     * Por defecto muestra el mes en curso.
     */
    public function historial(Request $request)
    {
        $user = auth()->user();

        // ── Rango de fechas (default: mes en curso) ───────────────────────────
        $desdeDefault = now()->startOfMonth()->toDateString();
        $hastaDefault = now()->toDateString();

        $desde = $request->filled('fecha_desde') ? $request->fecha_desde : $desdeDefault;
        $hasta = $request->filled('fecha_hasta') ? $request->fecha_hasta : $hastaDefault;

        // ── Query base ────────────────────────────────────────────────────────
        $query = Venta::with([
            'zapato.categoria',
            'zapato.tipo',
            'zapato.talla',
            'sucursal',
            'usuario',
            'devolucion',
        ])
        ->whereDate('created_at', '>=', $desde)
        ->whereDate('created_at', '<=', $hasta);

        // Encargado solo ve su sucursal
        if ($user->isEncargado() && $user->sucursal_id) {
            $query->where('sucursal_id', $user->sucursal_id);
        }

        // Filtros opcionales (solo dueño puede cambiar sucursal)
        if ($user->isDueno() && $request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }
        if ($request->filled('clasificacion')) {
            $query->whereHas('zapato', fn ($q) => $q->where('clasificacion', $request->clasificacion));
        }

        // ── Exportar CSV ──────────────────────────────────────────────────────
        if ($request->filled('exportar') && $request->exportar === 'csv') {
            return $this->exportarVentasCsv($query->orderByDesc('created_at')->get());
        }

        $ventas = $query->orderByDesc('created_at')->get();

        // ── Agrupar por día (newest first) ────────────────────────────────────
        $ventasPorDia = $ventas->groupBy(fn ($v) => $v->created_at->toDateString());

        // ── Stats del período ─────────────────────────────────────────────────
        $stats = [
            'total'    => $ventas->count(),
            'ingresos' => $ventas->sum('precio_venta'),
            'rebajado' => $ventas->sum(fn ($v) => max(0, (float)$v->precio_lista - (float)$v->precio_venta)),
            'promedio' => $ventas->count() > 0
                ? $ventas->avg('precio_venta')
                : 0,
        ];

        // ── Catálogos para filtros ────────────────────────────────────────────
        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();
        $usuarios   = User::orderBy('name')->get();

        return view('ventas.historial', compact(
            'ventasPorDia', 'stats',
            'sucursales', 'usuarios',
            'desde', 'hasta'
        ));
    }

    // ── Exportar ventas a CSV ─────────────────────────────────────────────────
    private function exportarVentasCsv($ventas)
    {
        $filename = 'ventas_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($ventas) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM Excel

            fputcsv($handle, [
                'Fecha', 'Hora', 'Código', 'Categoría', 'Tipo', 'Talla',
                'Clasificación', 'Precio Lista (Q)', 'Precio Venta (Q)',
                'Diferencia (Q)', 'Sucursal', 'Vendido por', 'Notas',
            ]);

            foreach ($ventas as $v) {
                $dif = (float)$v->precio_venta - (float)$v->precio_lista;
                fputcsv($handle, [
                    $v->created_at->format('d/m/Y'),
                    $v->created_at->format('H:i'),
                    $v->zapato->codigo_unico       ?? '—',
                    $v->zapato->categoria->nombre  ?? '—',
                    $v->zapato->tipo->nombre       ?? '—',
                    $v->zapato->talla->nombre      ?? $v->zapato->talla ?? '—',
                    $v->zapato->clasificacion_label ?? '—',
                    number_format($v->precio_lista, 2),
                    number_format($v->precio_venta, 2),
                    ($dif >= 0 ? '+' : '') . number_format($dif, 2),
                    $v->sucursal->nombre           ?? '—',
                    $v->usuario->name              ?? '—',
                    $v->notas                      ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
