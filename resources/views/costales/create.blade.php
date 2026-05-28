@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">Registrar compra de costal</h6>
        <p class="text-sm text-secondary mb-0">El costo total se calcula automáticamente.</p>
      </div>
      <div class="card-body">
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          </div>
        @endif

        <form method="POST" action="{{ route('costales.store') }}" id="formCostal">
          @csrf
          <div class="alert alert-info d-flex align-items-center gap-2 py-2 mb-3" role="alert">
            <i class="fas fa-tag text-info"></i>
            <span class="text-xs">El identificador del costal será asignado automáticamente por el sistema con el formato <strong>C-AAAAMMDD-NNN</strong>.</span>
          </div>
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label text-xs fw-bold">Fecha de compra <span class="text-danger">*</span></label>
              <input type="date" name="fecha_compra" class="form-control"
                     value="{{ old('fecha_compra', date('Y-m-d')) }}" required>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Proveedor <span class="text-danger">*</span></label>
              <select name="proveedor_id" class="form-select" required>
                <option value="">— Seleccionar —</option>
                @foreach($proveedores as $p)
                  <option value="{{ $p->id }}" {{ old('proveedor_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Sucursal destino <span class="text-danger">*</span></label>
              <select name="sucursal_destino_id" class="form-select" required>
                <option value="">— Seleccionar —</option>
                @foreach($sucursales as $s)
                  <option value="{{ $s->id }}" {{ old('sucursal_destino_id') == $s->id ? 'selected' : '' }}>
                    {{ $s->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="card bg-gray-100 mb-3">
            <div class="card-body py-3">
              <h6 class="text-sm mb-3">Cálculo de costo</h6>
              <div class="row">
                <div class="col-md-4 mb-2">
                  <label class="form-label text-xs fw-bold">Peso (libras) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0.01" name="peso_libras" id="peso_libras"
                         class="form-control" value="{{ old('peso_libras') }}" required>
                </div>
                <div class="col-md-4 mb-2">
                  <label class="form-label text-xs fw-bold">Precio por libra (Q) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0.01" name="precio_por_libra" id="precio_por_libra"
                         class="form-control" value="{{ old('precio_por_libra') }}" required>
                </div>
                <div class="col-md-4 mb-2">
                  <label class="form-label text-xs fw-bold">Costo total</label>
                  <div class="form-control bg-white fw-bold text-primary" id="costo_preview">Q 0.00</div>
                </div>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Notas</label>
            <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones del costal...">{{ old('notas') }}</textarea>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-sm">Registrar y clasificar</button>
            <a href="{{ route('costales.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function calcularCosto() {
    const peso  = parseFloat(document.getElementById('peso_libras').value) || 0;
    const precio = parseFloat(document.getElementById('precio_por_libra').value) || 0;
    document.getElementById('costo_preview').textContent = 'Q ' + (peso * precio).toFixed(2);
  }
  document.getElementById('peso_libras').addEventListener('input', calcularCosto);
  document.getElementById('precio_por_libra').addEventListener('input', calcularCosto);
</script>
@endsection
