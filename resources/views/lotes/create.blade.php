@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-10 col-lg-12">

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show mb-3">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <form method="POST" action="{{ route('lotes.store') }}" id="formLote">
      @csrf

      {{-- ── Card 1: Información del lote ────────────────────────────── --}}
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6 class="mb-0"><i class="fas fa-layer-group me-2 text-secondary"></i>Información del lote</h6>
          <p class="text-xs text-secondary mb-0">El identificador del lote será asignado automáticamente con el formato <strong>L-AAAAMMDD-NNN</strong>.</p>
        </div>
        <div class="card-body">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label text-xs fw-bold">Proveedor <span class="text-danger">*</span></label>
              <select name="proveedor_id" class="form-select form-select-sm" required>
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
              <select name="sucursal_destino_id" class="form-select form-select-sm" required>
                <option value="">— Seleccionar —</option>
                @foreach($sucursales as $s)
                  <option value="{{ $s->id }}" {{ old('sucursal_destino_id') == $s->id ? 'selected' : '' }}>
                    {{ $s->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label text-xs fw-bold">Fecha de compra <span class="text-danger">*</span></label>
              <input type="date" name="fecha_compra" class="form-control form-control-sm"
                     value="{{ old('fecha_compra', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label text-xs fw-bold">Precio por libra predeterminado (Q) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="0.01" name="precio_por_libra" id="precioDefault"
                     class="form-control form-control-sm"
                     value="{{ old('precio_por_libra') }}"
                     placeholder="Ej: 3.50" required>
            </div>
            <div class="col-md-4 mb-3 d-flex align-items-end">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="precioUnico" checked>
                <label class="form-check-label text-xs fw-bold" for="precioUnico">
                  Mismo precio para todos los costales
                </label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Notas</label>
            <textarea name="notas" class="form-control form-control-sm" rows="2"
                      placeholder="Observaciones generales del lote...">{{ old('notas') }}</textarea>
          </div>

        </div>
      </div>

      {{-- ── Card 2: Costales del lote ────────────────────────────────── --}}
      <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0"><i class="fas fa-shopping-bag me-2 text-secondary"></i>Costales en este lote</h6>
            <p class="text-xs text-secondary mb-0" id="resumenLote">1 costal &nbsp;|&nbsp; 0.00 lbs total &nbsp;|&nbsp; Q 0.00 total</p>
          </div>
          <button type="button" class="btn btn-outline-primary btn-sm mb-0" id="btnAgregar">
            <i class="fas fa-plus me-1"></i> Agregar costal
          </button>
        </div>
        <div class="card-body px-0 pt-0 pb-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaCostales">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width:40px">#</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Peso (libras) <span class="text-danger">*</span></th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Q por libra <span class="text-danger">*</span></th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costo</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Notas</th>
                  <th class="text-secondary opacity-7" style="width:40px"></th>
                </tr>
              </thead>
              <tbody id="cuerpoTabla">
                {{-- Filas generadas por JS --}}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="fas fa-save me-1"></i> Registrar lote
        </button>
        <a href="{{ route('lotes.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
      </div>

    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  let filaIndex = 0;

  // ── Crear una fila de costal ─────────────────────────────────────────
  function crearFila(index) {
    const precioDefault = parseFloat(document.getElementById('precioDefault').value) || '';
    const soloUnPrecio  = document.getElementById('precioUnico').checked;

    const tr = document.createElement('tr');
    tr.dataset.index = index;

    const readonlyAttr = soloUnPrecio ? 'readonly' : '';
    const bgClass      = soloUnPrecio ? 'bg-light'  : '';

    tr.innerHTML = `
      <td class="ps-3 text-xs text-secondary num-col">${index + 1}</td>
      <td class="ps-2">
        <input type="number" step="0.01" min="0.01"
               name="costales[${index}][peso_libras]"
               class="form-control form-control-sm campo-peso"
               placeholder="0.00" required>
      </td>
      <td class="ps-2">
        <input type="number" step="0.01" min="0.01"
               name="costales[${index}][precio_por_libra]"
               class="form-control form-control-sm campo-precio ${bgClass}"
               value="${precioDefault}"
               placeholder="0.00"
               ${readonlyAttr} required>
      </td>
      <td class="ps-2">
        <div class="form-control form-control-sm bg-white text-primary fw-bold campo-costo" style="min-width:80px">Q 0.00</div>
      </td>
      <td class="ps-2">
        <input type="text"
               name="costales[${index}][notas]"
               class="form-control form-control-sm"
               placeholder="Observación (opcional)">
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-link p-0 text-danger btn-eliminar" title="Eliminar fila">
          <i class="fas fa-times"></i>
        </button>
      </td>
    `;

    // Calcular costo al escribir
    tr.querySelector('.campo-peso').addEventListener('input', () => actualizarFila(tr));
    tr.querySelector('.campo-precio').addEventListener('input', () => actualizarFila(tr));

    // Eliminar fila
    tr.querySelector('.btn-eliminar').addEventListener('click', function () {
      const tbody = document.getElementById('cuerpoTabla');
      if (tbody.querySelectorAll('tr').length > 1) {
        tr.remove();
        renumerarFilas();
        actualizarResumen();
        toggleBotonesEliminar();
      }
    });

    return tr;
  }

  // ── Actualizar costo de una fila ─────────────────────────────────────
  function actualizarFila(tr) {
    const peso   = parseFloat(tr.querySelector('.campo-peso').value)   || 0;
    const precio = parseFloat(tr.querySelector('.campo-precio').value) || 0;
    tr.querySelector('.campo-costo').textContent = 'Q ' + (peso * precio).toFixed(2);
    actualizarResumen();
  }

  // ── Renumerar columna "#" ────────────────────────────────────────────
  function renumerarFilas() {
    const filas = document.querySelectorAll('#cuerpoTabla tr');
    filas.forEach((tr, i) => {
      tr.querySelector('.num-col').textContent = i + 1;
    });
  }

  // ── Resumen en el header ─────────────────────────────────────────────
  function actualizarResumen() {
    const filas = document.querySelectorAll('#cuerpoTabla tr');
    let totalPeso = 0, totalCosto = 0;

    filas.forEach(tr => {
      const peso   = parseFloat(tr.querySelector('.campo-peso').value)   || 0;
      const precio = parseFloat(tr.querySelector('.campo-precio').value) || 0;
      totalPeso  += peso;
      totalCosto += peso * precio;
    });

    document.getElementById('resumenLote').innerHTML =
      `${filas.length} costal${filas.length !== 1 ? 'es' : ''} &nbsp;|&nbsp; `
      + `${totalPeso.toFixed(2)} lbs total &nbsp;|&nbsp; `
      + `Q ${totalCosto.toFixed(2)} total`;
  }

  // ── Mostrar/ocultar botones eliminar ─────────────────────────────────
  function toggleBotonesEliminar() {
    const filas = document.querySelectorAll('#cuerpoTabla tr');
    const mostrar = filas.length > 1;
    filas.forEach(tr => {
      tr.querySelector('.btn-eliminar').style.display = mostrar ? '' : 'none';
    });
  }

  // ── Sincronizar precio en todas las filas (cuando precioUnico está on) ──
  function sincronizarPrecios() {
    const valor = document.getElementById('precioDefault').value;
    const soloUnPrecio = document.getElementById('precioUnico').checked;

    document.querySelectorAll('#cuerpoTabla .campo-precio').forEach(input => {
      if (soloUnPrecio) {
        input.value = valor;
        input.setAttribute('readonly', '');
        input.classList.add('bg-light');
      } else {
        input.removeAttribute('readonly');
        input.classList.remove('bg-light');
      }
    });

    actualizarResumen();
    // Recalcular costos de cada fila
    document.querySelectorAll('#cuerpoTabla tr').forEach(tr => actualizarFila(tr));
  }

  // ── Agregar primera fila al cargar ───────────────────────────────────
  (function inicializar() {
    const tbody = document.getElementById('cuerpoTabla');
    tbody.appendChild(crearFila(filaIndex++));
    toggleBotonesEliminar();
    actualizarResumen();
  })();

  // ── Botón "+ Agregar costal" ─────────────────────────────────────────
  document.getElementById('btnAgregar').addEventListener('click', function () {
    const tbody = document.getElementById('cuerpoTabla');
    tbody.appendChild(crearFila(filaIndex++));
    toggleBotonesEliminar();
    actualizarResumen();
  });

  // ── Precio predeterminado cambia ─────────────────────────────────────
  document.getElementById('precioDefault').addEventListener('input', function () {
    if (document.getElementById('precioUnico').checked) {
      sincronizarPrecios();
    }
  });

  // ── Checkbox "Mismo precio" cambia ───────────────────────────────────
  document.getElementById('precioUnico').addEventListener('change', function () {
    sincronizarPrecios();
  });

})();
</script>
@endpush
