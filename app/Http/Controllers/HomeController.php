<?php

namespace App\Http\Controllers;

use App\Models\Apertura;
use App\Models\Costal;
use App\Models\Lote;
use App\Models\Zapato;
use App\Models\ZapatoLote;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        // ── Estadísticas generales ───────────────────────────────────────────
        $stats = [
            'zapatos_inventario'  => Zapato::where('estado', 'en_inventario')->count(),
            'lotes_total'         => Lote::count(),
            'costales_pendientes' => Costal::where('estado', 'recibido')->count(),
            'en_preparacion'      => ZapatoLote::where('estado', 'en_preparacion')->count(),
        ];

        // ── Últimos 5 lotes ──────────────────────────────────────────────────
        $ultimosLotes = Lote::with('proveedor', 'sucursalDestino', 'costales')
            ->latest()
            ->limit(5)
            ->get();

        // ── Últimas 5 aperturas ──────────────────────────────────────────────
        $ultimasAperturas = Apertura::with('lote.proveedor')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'ultimosLotes', 'ultimasAperturas'));
    }
}
