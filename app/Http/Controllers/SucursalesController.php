<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;

class SucursalesController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::with('encargado')->orderBy('nombre')->get();
        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        $encargados = User::whereHas('roles', fn ($q) => $q->whereIn('slug', ['encargado', 'dueno']))
            ->orderBy('name')->get();
        return view('sucursales.create', compact('encargados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:100',
            'direccion'    => 'nullable|string|max:255',
            'telefono'     => 'nullable|string|max:20',
            'encargado_id' => 'nullable|exists:usuario,id',
        ]);

        Sucursal::create($data);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal creada correctamente.');
    }

    public function edit(Sucursal $sucursal)
    {
        $encargados = User::whereHas('roles', fn ($q) => $q->whereIn('slug', ['encargado', 'dueno']))
            ->orderBy('name')->get();
        return view('sucursales.edit', compact('sucursal', 'encargados'));
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        if ($request->boolean('reactivar')) {
            $sucursal->update(['activo' => true]);
            return redirect()->route('sucursales.index')
                ->with('success', 'Sucursal activada correctamente.');
        }

        $data = $request->validate([
            'nombre'       => 'required|string|max:100',
            'direccion'    => 'nullable|string|max:255',
            'telefono'     => 'nullable|string|max:20',
            'encargado_id' => 'nullable|exists:usuario,id',
            'activo'       => 'boolean',
        ]);

        $sucursal->update($data);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal actualizada correctamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->update(['activo' => false]);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal desactivada.');
    }
}
