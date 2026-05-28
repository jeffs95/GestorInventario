@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">Nuevo proveedor</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('proveedores.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Teléfono</label>
              <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">NIT</label>
              <input type="text" name="nit" class="form-control" value="{{ old('nit') }}">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}">
          </div>
          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Notas</label>
            <textarea name="notas" class="form-control" rows="3">{{ old('notas') }}</textarea>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
            <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
