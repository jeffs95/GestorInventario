<?php

namespace App\Http\Controllers;

use App\Models\CategoriaZapato;
use App\Models\Sucursal;
use App\Models\Talla;
use App\Models\TipoZapato;
use App\Models\Zapato;
use App\Models\ZapatoLote;
use App\Traits\FuncionesDigitalizacion;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    use FuncionesDigitalizacion;

    public function index(Request $request)
    {
        $user = auth()->user();

        // ── Closure de filtros reutilizable en stats, whereHas y eager load ──
        $filtrar = function ($q) use ($request, $user) {
            if ($user->isEncargado() && $user->sucursal_id) {
                $q->where('sucursal_id', $user->sucursal_id);
            }
            if ($request->filled('buscar')) {
                $b = $request->buscar;
                $q->where(fn($q2) => $q2
                    ->where('codigo_unico', 'like', "%{$b}%")
                    ->orWhere('marca',      'like', "%{$b}%")
                    ->orWhere('color',      'like', "%{$b}%")
                    ->orWhere('notas',      'like', "%{$b}%")
                );
            }
            if ($request->filled('sucursal_id'))   $q->where('sucursal_id',   $request->sucursal_id);
            if ($request->filled('categoria_id'))  $q->where('categoria_id',  $request->categoria_id);
            if ($request->filled('tipo_id'))       $q->where('tipo_id',       $request->tipo_id);
            if ($request->filled('clasificacion')) $q->where('clasificacion', $request->clasificacion);
            if ($request->filled('estado'))        $q->where('estado',        $request->estado);
            if ($request->filled('talla_id'))      $q->where('talla_id',      $request->talla_id);
            if ($request->filled('genero'))        $q->where('genero',        $request->genero);
            if ($request->filled('condicion'))     $q->where('condicion',     $request->condicion);
            if ($request->filled('con_foto')) {
                $request->con_foto === '1'
                    ? $q->whereNotNull('foto_path')
                    : $q->whereNull('foto_path');
            }
        };

        // ── Exportar CSV ──────────────────────────────────────────────────────
        if ($request->filled('exportar') && $request->exportar === 'csv') {
            $csvQuery = Zapato::with(['categoria', 'tipo', 'talla', 'sucursal']);
            $filtrar($csvQuery);
            return $this->exportarCsv($csvQuery->orderByDesc('created_at')->get());
        }

        // ── Estadísticas (sobre todos los zapatos filtrados) ──────────────────
        $statsQuery = Zapato::query();
        $filtrar($statsQuery);
        $stats = [
            'total'         => (clone $statsQuery)->count(),
            'en_inventario' => (clone $statsQuery)->where('estado', 'en_inventario')->count(),
            'vendido'       => (clone $statsQuery)->where('estado', 'vendido')->count(),
            'valor_total'   => (clone $statsQuery)->where('estado', 'en_inventario')->sum('precio_lista'),
        ];

        // ── Lotes que tienen al menos un zapato que pasa el filtro ───────────
        $lotes = ZapatoLote::with([
            'apertura.lote.proveedor',
            'apertura.lote.sucursalDestino',
            'categoria',
            'tipo',
            'zapatos' => function ($q) use ($filtrar) {
                $filtrar($q);
                $q->with(['talla', 'sucursal', 'categoria', 'tipo'])->orderBy('id');
            },
        ])
        ->whereHas('zapatos', $filtrar)
        ->orderByDesc('id')
        ->paginate($request->integer('per_page', 8))
        ->withQueryString();

        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();
        $categorias = CategoriaZapato::where('activo', true)->orderBy('nombre')->get();
        $tipos      = TipoZapato::where('activo', true)->orderBy('nombre')->get();
        $tallas     = Talla::where('activo', true)->orderBy('nombre')->get();

        return view('inventario.index', compact(
            'lotes', 'sucursales', 'categorias', 'tipos', 'tallas', 'stats'
        ));
    }

    /** Sirve la foto del zapato desde el FTP */
    public function foto(Zapato $zapato)
    {
        if (! $zapato->foto_path) {
            abort(404);
        }

        return $this->leer_ftp($zapato->foto_path, basename($zapato->foto_path), 'jpg');
    }

    // ── Exportar a CSV ───────────────────────────────────────────────────────
    private function exportarCsv($zapatos)
    {
        $filename = 'inventario_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($zapatos) {
            $handle = fopen('php://output', 'w');
            // BOM para Excel en Windows
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Código', 'Clasificación', 'Categoría', 'Tipo', 'Talla',
                'Color', 'Marca', 'Género', 'Condición', 'Precio Lista (Q)',
                'Estado', 'Sucursal', 'Fecha ingreso', 'Notas',
            ]);

            foreach ($zapatos as $z) {
                fputcsv($handle, [
                    $z->codigo_unico,
                    $z->clasificacion_label,
                    $z->categoria->nombre     ?? '—',
                    $z->tipo->nombre          ?? '—',
                    $z->talla->nombre         ?? $z->talla ?? '—',
                    $z->color                 ?? '—',
                    $z->marca                 ?? '—',
                    $z->genero                ?? '—',
                    $z->condicion             ?? '—',
                    number_format($z->precio_lista, 2),
                    $z->estado,
                    $z->sucursal->nombre      ?? '—',
                    $z->created_at?->format('d/m/Y'),
                    $z->notas                 ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
