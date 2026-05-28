@extends('layouts.user_type.auth')

@push('styles')
<style>
  /* ── Selects e inputs con borde visible ── */
  .form-select,
  .form-control {
    border: 1px solid #d2d6da !important;
    border-radius: 0.5rem !important;
  }
  .form-select:focus,
  .form-control:focus {
    border-color: #e91e8c !important;
    box-shadow: 0 0 0 2px rgba(233,30,140,.15) !important;
  }

  /* ── Checkboxes visibles (el tema los deja border:0 + appearance:none) ── */
  #tablaCostales .form-check-input,
  #chkAll {
    width: 20px !important;
    height: 20px !important;
    min-width: 20px !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    border: 2px solid #344767 !important;
    border-radius: 5px !important;
    background-color: #fff !important;
    background-image: none !important;
    cursor: pointer;
    display: inline-block !important;
    vertical-align: middle;
    transition: background .15s ease, border-color .15s ease;
  }
  #tablaCostales .form-check-input:checked,
  #chkAll:checked {
    background-color: #7928ca !important;
    border-color: #7928ca !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3E%3C/svg%3E") !important;
    background-size: 14px !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
  }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-10 col-lg-12">

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show mb-3">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <form method="POST" action="{{ route('aperturas.store') }}" id="formApertura">
      @csrf

      {{-- ── Card 1: Seleccionar lote ──────────────────────────────────── --}}
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6 class="mb-0">
            <i class="fas fa-box-open me-2 text-secondary"></i>Nueva apertura de costales
          </h6>
          <p class="text-xs text-secondary mb-0">
            Selecciona el lote y los costales que vas a abrir para clasificar juntos.
          </p>
        </div>
        <div class="card-body">

          <div class="row">
            {{-- Selector de lote --}}
            <div class="col-md-5 mb-3">
              <label class="form-label text-xs fw-bold">Lote de compra <span class="text-danger">*</span></label>
              <select name="lote_id" id="selectLote" class="form-select form-select-sm" required>
                <option value="">— Seleccionar lote —</option>
                @foreach($lotes as $l)
                  <option value="{{ $l->id }}"
                    {{ (old('lote_id', $lote?->id) == $l->id) ? 'selected' : '' }}>
                    {{ $l->numero_lote }} — {{ $l->proveedor->nombre }}
                    ({{ $l->costales_count ?? $l->costales()->where('estado','recibido')->count() }} disponibles)
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Fecha --}}
            <div class="col-md-4 mb-3">
              <label class="form-label text-xs fw-bold">Fecha de apertura <span class="text-danger">*</span></label>
              <input type="date" name="fecha" class="form-control form-control-sm"
                     value="{{ old('fecha', date('Y-m-d')) }}" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Notas</label>
            <textarea name="notas" class="form-control form-control-sm" rows="2"
                      placeholder="Observaciones de esta apertura...">{{ old('notas') }}</textarea>
          </div>

        </div>
      </div>

      {{-- ── Card 2: Seleccionar costales ──────────────────────────────── --}}
      <div class="card mb-4" id="cardCostales" style="{{ $lote ? '' : 'display:none' }}">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0"><i class="fas fa-shopping-bag me-2 text-secondary"></i>Costales disponibles</h6>
            <p class="text-xs text-secondary mb-0">
              Solo se muestran los costales en estado <strong>Recibido</strong>.
              Marca los que vas a abrir en esta sesión.
            </p>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm mb-0" id="btnSelAll">Todos</button>
            <button type="button" class="btn btn-outline-secondary btn-sm mb-0" id="btnDeselAll">Ninguno</button>
          </div>
        </div>
        <div class="card-body px-0 pt-0 pb-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaCostales">
              <thead>
                <tr>
                  <th class="text-center ps-3" style="width:44px">
                    <input type="checkbox" id="chkAll" class="form-check-input">
                  </th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Costal</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Peso (lbs)</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Q/lb</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costo</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Notas</th>
                </tr>
              </thead>
              <tbody id="cuerpoTabla">
                @if($lote)
                  @forelse($lote->costales as $costal)
                  <tr>
                    <td class="text-center ps-3">
                      <input type="checkbox" class="form-check-input chk-costal"
                             name="costales[]" value="{{ $costal->id }}"
                             {{ in_array($costal->id, (array) old('costales', [])) ? 'checked' : '' }}>
                    </td>
                    <td>
                      <span class="text-xs font-weight-bold font-monospace">
                        {{ $costal->numero_costal ?? '#'.$costal->id }}
                      </span>
                    </td>
                    <td class="ps-2">
                      <span class="text-xs">{{ number_format($costal->peso_libras, 2) }}</span>
                    </td>
                    <td class="ps-2">
                      <span class="text-xs">Q{{ number_format($costal->precio_por_libra, 2) }}</span>
                    </td>
                    <td class="ps-2">
                      <span class="text-xs font-weight-bold text-primary">Q{{ number_format($costal->costo_total, 2) }}</span>
                    </td>
                    <td class="ps-2">
                      <span class="text-xs text-secondary">{{ $costal->notas ?? '—' }}</span>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-secondary text-sm">
                      Este lote no tiene costales disponibles (estado: Recibido).
                    </td>
                  </tr>
                  @endforelse
                @endif
              </tbody>
            </table>
          </div>
        </div>
        @if($lote && $lote->costales->count())
        <div class="card-footer pt-2 pb-2">
          <p class="text-xs text-secondary mb-0" id="resumenSeleccion">
            0 costales seleccionados
          </p>
        </div>
        @endif
      </div>

      <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary btn-sm" id="btnGuardar">
          <i class="fas fa-box-open me-1"></i> Abrir costales seleccionados
        </button>
        <a href="{{ $lote ? route('lotes.show', $lote) : route('lotes.index') }}"
           class="btn btn-outline-secondary btn-sm">Cancelar</a>
      </div>

    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  const selectLote = document.getElementById('selectLote');

  // ── Recargar al cambiar de lote ──────────────────────────────────────
  selectLote.addEventListener('change', function () {
    const loteId = this.value;
    if (loteId) {
      window.location.href = '{{ route('aperturas.create') }}?lote_id=' + loteId;
    } else {
      document.getElementById('cardCostales').style.display = 'none';
    }
  });

  // ── Checkboxes ───────────────────────────────────────────────────────
  const chkAll   = document.getElementById('chkAll');
  const btnAll   = document.getElementById('btnSelAll');
  const btnNone  = document.getElementById('btnDeselAll');
  const resumen  = document.getElementById('resumenSeleccion');

  function actualizarResumen() {
    const total = document.querySelectorAll('.chk-costal').length;
    const sel   = document.querySelectorAll('.chk-costal:checked').length;
    if (resumen) resumen.textContent = `${sel} de ${total} costales seleccionados`;
    if (chkAll)  chkAll.checked = (sel === total && total > 0);
  }

  if (chkAll) {
    chkAll.addEventListener('change', function () {
      document.querySelectorAll('.chk-costal').forEach(c => c.checked = this.checked);
      actualizarResumen();
    });
  }

  document.querySelectorAll('.chk-costal').forEach(c =>
    c.addEventListener('change', actualizarResumen)
  );

  if (btnAll)  btnAll.addEventListener('click',  () => { document.querySelectorAll('.chk-costal').forEach(c => c.checked = true);  actualizarResumen(); });
  if (btnNone) btnNone.addEventListener('click', () => { document.querySelectorAll('.chk-costal').forEach(c => c.checked = false); actualizarResumen(); });

  actualizarResumen();

  // ── Validar al enviar ────────────────────────────────────────────────
  document.getElementById('formApertura').addEventListener('submit', function (e) {
    const sel = document.querySelectorAll('.chk-costal:checked').length;
    if (sel === 0) {
      e.preventDefault();
      Swal.fire({
        ..._swalBase,
        title: 'Sin costales seleccionados',
        text: 'Debes marcar al menos un costal para abrir.',
        icon: 'warning',
        showCancelButton: false,
        confirmButtonText: 'Entendido',
        customClass: {
          ..._swalBase.customClass,
          confirmButton: 'btn btn-sm px-4 btn-warning',
        },
      });
    }
  });

})();
</script>
@endpush
