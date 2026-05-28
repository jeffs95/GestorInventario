@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">Editar usuario: {{ $usuario->name }}</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
          @csrf @method('PUT')
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Nombre <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Correo electrónico <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Nueva contraseña <span class="text-secondary">(dejar vacío para no cambiar)</span></label>
              <input type="password" name="password" class="form-control" minlength="6">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Confirmar contraseña</label>
              <input type="password" name="password_confirmation" class="form-control">
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Roles <span class="text-danger">*</span></label>
              <div class="border rounded p-2">
                @foreach($roles as $rol)
                <div class="form-check mb-1">
                  <input type="checkbox" name="roles[]" value="{{ $rol->id }}"
                         class="form-check-input" id="rol_edit_{{ $rol->id }}"
                         {{ in_array($rol->id, old('roles', $rolesActivos)) ? 'checked' : '' }}>
                  <label class="form-check-label text-sm" for="rol_edit_{{ $rol->id }}">
                    {{ $rol->nombre }}
                    @if($rol->descripcion)
                      <small class="text-secondary d-block">{{ $rol->descripcion }}</small>
                    @endif
                  </label>
                </div>
                @endforeach
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Sucursal asignada</label>
              <select name="sucursal_id" class="form-select">
                <option value="">— Sin asignar —</option>
                @foreach($sucursales as $s)
                  <option value="{{ $s->id }}" {{ old('sucursal_id', $usuario->sucursal_id) == $s->id ? 'selected' : '' }}>
                    {{ $s->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Teléfono</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $usuario->phone) }}">
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
