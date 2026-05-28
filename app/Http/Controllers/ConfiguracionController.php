<?php

namespace App\Http\Controllers;

use App\Models\CategoriaZapato;
use App\Models\Talla;
use App\Models\TipoZapato;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $categorias = CategoriaZapato::orderBy('nombre')->get();
        $tipos      = TipoZapato::orderBy('nombre')->get();
        $tallas     = Talla::orderBy('nombre')->get();

        return view('configuracion.index', compact('categorias', 'tipos', 'tallas'));
    }

    // ── Categorías ───────────────────────────────────────────────────────────

    public function storeCategoria(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:50|unique:categoria_zapato,nombre']);
        CategoriaZapato::create(['nombre' => $request->nombre]);
        return back()->with('success', 'Categoría agregada.');
    }

    public function destroyCategoria(CategoriaZapato $categoria)
    {
        if ($categoria->zapatos()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay zapatos con esta categoría.');
        }
        $categoria->delete();
        return back()->with('success', 'Categoría eliminada.');
    }

    // ── Tipos ────────────────────────────────────────────────────────────────

    public function storeTipo(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:50|unique:tipo_zapato,nombre']);
        TipoZapato::create(['nombre' => $request->nombre]);
        return back()->with('success', 'Tipo de zapato agregado.');
    }

    public function destroyTipo(TipoZapato $tipo)
    {
        if ($tipo->zapatos()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay zapatos con este tipo.');
        }
        $tipo->delete();
        return back()->with('success', 'Tipo de zapato eliminado.');
    }

    // ── Tallas ───────────────────────────────────────────────────────────────

    public function storeTalla(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:20|unique:talla,nombre']);
        Talla::create(['nombre' => $request->nombre]);
        return back()->with('success', "Talla \"{$request->nombre}\" agregada.");
    }

    public function destroyTalla(Talla $talla)
    {
        if ($talla->zapatos()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay zapatos con esta talla.');
        }
        $talla->delete();
        return back()->with('success', 'Talla eliminada.');
    }
}
