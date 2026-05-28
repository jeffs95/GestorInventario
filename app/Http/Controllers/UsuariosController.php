<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['sucursal', 'roles'])->orderBy('name')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();
        $roles      = Rol::orderBy('nombre')->get();
        return view('usuarios.create', compact('sucursales', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:usuario,email',
            'password'    => 'required|min:6|confirmed',
            'roles'       => 'required|array|min:1',
            'roles.*'     => 'exists:rol,id',
            'sucursal_id' => 'nullable|exists:sucursal,id',
            'phone'       => 'nullable|string|max:20',
        ]);

        $roles = $data['roles'];
        unset($data['roles']);
        $data['password'] = Hash::make($data['password']);

        $usuario = User::create($data);
        $usuario->roles()->sync($roles);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        $sucursales   = Sucursal::where('activo', true)->orderBy('nombre')->get();
        $roles        = Rol::orderBy('nombre')->get();
        $rolesActivos = $usuario->roles->pluck('id')->toArray();
        return view('usuarios.edit', compact('usuario', 'sucursales', 'roles', 'rolesActivos'));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|unique:usuario,email,' . $usuario->id,
            'roles'       => 'required|array|min:1',
            'roles.*'     => 'exists:rol,id',
            'sucursal_id' => 'nullable|exists:sucursal,id',
            'phone'       => 'nullable|string|max:20',
        ]);

        $roles = $data['roles'];
        unset($data['roles']);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);
        $usuario->roles()->sync($roles);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }
        $usuario->delete();
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}
