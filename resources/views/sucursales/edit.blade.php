@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">Editar sucursal: {{ $sucursal->nombre }}</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('sucursales.update', $sucursal) }}">
          @csrf @method('PUT')
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $sucursal->nombre) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $sucursal->direccion) }}">
          </div>
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $sucursal->telefono) }}">
          </div>
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Encargado</label>
            <select name="encargado_id" class="form-select">
              <option value="">— Sin asignar —</option>
              @foreach($encargados as $u)
                <option value="{{ $u->id }}"
                  {{ old('encargado_id', $sucursal->encargado_id) == $u->id ? 'selected' : '' }}>
                  {{ $u->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Estado</label>
            <select name="activo" class="form-select">
              <option value="1" {{ old('activo', $sucursal->activo) ? 'selected' : '' }}>Activa</option>
              <option value="0" {{ !old('activo', $sucursal->activo) ? 'selected' : '' }}>Inactiva</option>
            </select>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
            <a href="{{ route('sucursales.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
