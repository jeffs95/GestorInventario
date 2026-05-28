<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedoresController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::withCount('costales')->orderBy('nombre')->get();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'nit'       => 'nullable|string|max:20',
            'notas'     => 'nullable|string',
        ]);

        Proveedor::create($data);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        if ($request->boolean('reactivar')) {
            $proveedor->update(['activo' => true]);
            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor activado correctamente.');
        }

        $data = $request->validate([
            'nombre'    => 'required|string|max:100',
            'telefono'  => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'nit'       => 'nullable|string|max:20',
            'notas'     => 'nullable|string',
            'activo'    => 'boolean',
        ]);

        $proveedor->update($data);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->update(['activo' => false]);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor desactivado.');
    }
}
