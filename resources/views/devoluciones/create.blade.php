@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">

@if(!isset($venta) || $venta === null)

  {{-- ══ BUSCADOR: no hay venta_id, el vendedor escanea el recibo ══════════ --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-body py-3">
        <div class="row align-items-center g-3">
          <div class="col-auto">
            <div class="icon icon-shape icon-lg bg-gradient-danger shadow text-center border-radius-md">
              <i class="fas fa-undo text-white opacity-10 text-xl"></i>
            </div>
          </div>
          <div class="col">
            <h5 class="mb-0">Registrar Devolución</h5>
            <p class="mb-0 text-sm text-secondary">Escanea o ingresa el código del recibo de venta</p>
          </div>
          <div class="col-auto">
            <a href="{{ route('devoluciones.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
              <i class="fas fa-arrow-left me-1"></i> Historial
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(isset($error) && $error)
  <div class="alert alert-warning d-flex align-items-center gap-2 py-2 px-3 mb-3">
    <i class="fas fa-exclamation-triangle"></i>
    <span class="text-sm">{{ $error }}</span>
  </div>
  @endif

  <div class="card">
    <div class="card-header pb-0">
      <h6 class="mb-0"><i class="fas fa-barcode me-2 text-danger"></i>Buscar venta por código del recibo</h6>
    </div>
    <div class="card-body">
      <p class="text-sm text-secondary mb-3">
        Pide al cliente el recibo de compra e ingresa el código que aparece en el barcode,
        o escanéalo con la cámara.
      </p>
      <form method="GET" action="{{ route('devoluciones.create') }}" id="formBuscarDevolucion">
        <div class="input-group input-group-lg">
          <input type="text"
                 name="codigo"
                 id="inputCodigoDevolucion"
                 class="form-control"
                 placeholder="Ej: PL-A3-0001"
                 value="{{ request('codigo') }}"
                 autocomplete="off"
                 autofocus
                 style="font-family:monospace;font-size:1rem;letter-spacing:.05em">
          <button type="button" id="btnScanCamaraDevolucion"
                  class="btn btn-outline-danger px-3" title="Escanear con cámara">
            <i class="fas fa-camera"></i>
          </button>
          <button type="submit" class="btn bg-gradient-danger text-white px-4">
            <i class="fas fa-search me-1"></i> Buscar
          </button>
        </div>
      </form>

      <div class="mt-4 p-3 border-radius-lg text-center" style="background:#fff8f8;border:1px dashed #f5365c">
        <i class="fas fa-receipt fa-2x text-danger opacity-6 mb-2 d-block"></i>
        <p class="text-sm text-secondary mb-0">
          El código está en el barcode del recibo impreso<br>
          o en la foto que se tomó el cliente al momento de la compra.
        </p>
      </div>
    </div>
  </div>

  {{-- Modal scanner cámara (para devoluciones) --}}
  <div class="modal fade" id="modalScannerDev" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
      <div class="modal-content border-0 shadow-lg" style="border-radius:1.25rem">
        <div class="modal-header border-0 pb-0 px-4 pt-3">
          <div>
            <h6 class="mb-0 text-danger"><i class="fas fa-camera me-2"></i>Escanear código del recibo</h6>
            <p class="text-xs text-secondary mb-0">Apunta al barcode del comprobante de venta</p>
          </div>
          <button type="button" class="btn-close" id="btnCerrarScannerDev"></button>
        </div>
        <div class="modal-body px-3 pt-2 pb-3">
          <div id="wrapSelectCamaraDev" class="mb-2 d-none">
            <select id="selectCamaraDev" class="form-select form-select-sm"></select>
          </div>
          <div id="scannerContainerDev"
               style="width:100%;border-radius:.75rem;overflow:hidden;background:#000;min-height:200px"></div>
          <div id="scannerStatusDev" class="text-center mt-2 text-xs text-secondary">
            <i class="fas fa-spinner fa-spin me-1"></i>Iniciando cámara...
          </div>
          <div id="scannerResultadoDev" class="d-none mt-2 alert alert-success py-2 px-3 text-sm text-center">
            <i class="fas fa-check-circle me-1"></i>
            <strong id="scannerCodigoDetectadoDev"></strong>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0 px-4 pb-3">
          <button type="button" class="btn btn-outline-secondary w-100" id="btnCerrarScannerDev2">
            <i class="fas fa-times me-1"></i>Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>

@else
  {{-- Encabezado --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-body py-3">
        <div class="row align-items-center g-3">
          <div class="col-auto">
            <div class="icon icon-shape icon-lg bg-gradient-danger shadow text-center border-radius-md">
              <i class="fas fa-undo text-white opacity-10 text-xl"></i>
            </div>
          </div>
          <div class="col">
            <h5 class="mb-0">Registrar Devolución</h5>
            <p class="mb-0 text-sm text-secondary">
              Venta del {{ $venta->created_at->format('d/m/Y H:i') }}
              &nbsp;·&nbsp; Vendido por <strong>{{ $venta->usuario->name ?? '—' }}</strong>
            </p>
          </div>
          <div class="col-auto">
            <a href="{{ route('ventas.historial') }}" class="btn btn-outline-secondary btn-sm mb-0">
              <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Datos del zapato --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0"><i class="fas fa-shoe-prints me-2 text-primary"></i>Zapato a devolver</h6>
      </div>
      <div class="card-body pt-3">
        <div class="row align-items-center g-3">

          {{-- Foto --}}
          <div class="col-auto">
            @php
              $fotoSrc = $venta->zapato->fotos->first()?->foto_path
                      ?? $venta->zapato->foto_path;
            @endphp
            @if($fotoSrc)
              <img src="{{ $fotoSrc }}" alt="foto" class="border-radius-lg shadow-sm"
                   style="width:80px;height:80px;object-fit:cover">
            @else
              <div class="icon icon-shape icon-lg bg-gradient-light border-radius-lg shadow-sm d-flex align-items-center justify-content-center">
                <i class="fas fa-shoe-prints text-secondary fa-lg"></i>
              </div>
            @endif
          </div>

          {{-- Info --}}
          <div class="col">
            <h6 class="mb-1 font-monospace">{{ $venta->zapato->codigo_unico }}</h6>
            <p class="text-sm mb-1">
              {{ $venta->zapato->categoria->nombre ?? '—' }}
              @if($venta->zapato->tipo) · {{ $venta->zapato->tipo->nombre }} @endif
              @if($venta->zapato->talla) · Talla {{ $venta->zapato->talla->nombre ?? $venta->zapato->talla }} @endif
            </p>
            <div class="d-flex gap-2 flex-wrap">
              <span class="badge bg-gradient-secondary text-xs">{{ $venta->zapato->clasificacion_label }}</span>
              @if($venta->zapato->color)
                <span class="badge bg-gradient-light text-dark text-xs">{{ $venta->zapato->color }}</span>
              @endif
            </div>
          </div>

          {{-- Precio --}}
          <div class="col-auto text-end">
            <p class="text-xs text-secondary mb-0">Precio vendido</p>
            <h5 class="mb-0 text-success">Q{{ number_format($venta->precio_venta, 2) }}</h5>
            @if((float)$venta->precio_lista !== (float)$venta->precio_venta)
              <small class="text-secondary">Lista: Q{{ number_format($venta->precio_lista, 2) }}</small>
            @endif
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- Formulario --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0"><i class="fas fa-clipboard-list me-2 text-danger"></i>Detalles de la devolución</h6>
      </div>
      <div class="card-body">

        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show text-sm" role="alert">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <form action="{{ route('devoluciones.store') }}" method="POST" id="formDevolucion">
          @csrf
          <input type="hidden" name="venta_id" value="{{ $venta->id }}">

          <div class="row g-3">

            {{-- Motivo --}}
            <div class="col-12">
              <label class="form-label text-sm font-weight-bold">Motivo de la devolución <span class="text-danger">*</span></label>
              <div class="row g-2 mt-0">
                @foreach([
                  'no_sirve'         => ['fas fa-times-circle', 'danger',  'No sirve / Defectuoso'],
                  'talla_incorrecta' => ['fas fa-ruler',        'warning', 'Talla incorrecta'],
                  'cambio_opinion'   => ['fas fa-sync-alt',     'info',    'Cambio de opinión'],
                  'otro'             => ['fas fa-ellipsis-h',   'secondary','Otro'],
                ] as $val => [$icon, $color, $label])
                <div class="col-6 col-md-3">
                  <input type="radio" name="motivo" id="motivo_{{ $val }}" value="{{ $val }}"
                         class="btn-check" {{ old('motivo') == $val ? 'checked' : '' }}>
                  <label for="motivo_{{ $val }}"
                         class="btn btn-outline-{{ $color }} w-100 py-3 d-flex flex-column align-items-center gap-1">
                    <i class="{{ $icon }} fa-lg"></i>
                    <span class="text-xs">{{ $label }}</span>
                  </label>
                </div>
                @endforeach
              </div>
            </div>

            {{-- ¿Regresa al inventario? --}}
            <div class="col-12">
              <label class="form-label text-sm font-weight-bold">Estado del zapato tras la devolución <span class="text-danger">*</span></label>
              <div class="row g-2">
                <div class="col-6">
                  <input type="radio" name="regresa_inventario" id="regresa_si" value="1"
                         class="btn-check" {{ old('regresa_inventario', '1') == '1' ? 'checked' : '' }}>
                  <label for="regresa_si"
                         class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-1">
                    <i class="fas fa-redo fa-lg"></i>
                    <span class="text-xs font-weight-bold">Vuelve al inventario</span>
                    <span class="text-xxs opacity-7">Queda disponible para vender</span>
                  </label>
                </div>
                <div class="col-6">
                  <input type="radio" name="regresa_inventario" id="regresa_no" value="0"
                         class="btn-check" {{ old('regresa_inventario') == '0' ? 'checked' : '' }}>
                  <label for="regresa_no"
                         class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center gap-1">
                    <i class="fas fa-ban fa-lg"></i>
                    <span class="text-xs font-weight-bold">No vuelve</span>
                    <span class="text-xxs opacity-7">Se marca como devuelto</span>
                  </label>
                </div>
              </div>
            </div>

            {{-- Monto devuelto --}}
            <div class="col-12 col-md-6">
              <label for="monto_devuelto" class="form-label text-sm font-weight-bold">
                Monto reembolsado al cliente
              </label>
              <div class="input-group">
                <span class="input-group-text bg-light text-sm">Q</span>
                <input type="number" id="monto_devuelto" name="monto_devuelto"
                       class="form-control @error('monto_devuelto') is-invalid @enderror"
                       min="0" step="0.01" max="{{ $venta->precio_venta }}"
                       value="{{ old('monto_devuelto', number_format($venta->precio_venta, 2, '.', '')) }}"
                       placeholder="0.00">
              </div>
              <div class="d-flex gap-2 mt-1">
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 text-xs"
                        onclick="document.getElementById('monto_devuelto').value='0.00'">Sin reembolso</button>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 text-xs"
                        onclick="document.getElementById('monto_devuelto').value='{{ number_format($venta->precio_venta, 2, '.', '') }}'">Reembolso total</button>
              </div>
              @error('monto_devuelto')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Notas --}}
            <div class="col-12 col-md-6">
              <label for="notas" class="form-label text-sm font-weight-bold">Notas adicionales</label>
              <textarea id="notas" name="notas" rows="3"
                        class="form-control @error('notas') is-invalid @enderror"
                        placeholder="Ej: Cliente insiste que no le quedó bien el color...">{{ old('notas') }}</textarea>
              @error('notas')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            {{-- Botones --}}
            <div class="col-12 pt-2 d-flex gap-2 justify-content-end">
              <a href="{{ route('ventas.historial') }}" class="btn btn-outline-secondary mb-0">
                <i class="fas fa-times me-1"></i> Cancelar
              </a>
              <button type="button" id="btnConfirmar" class="btn bg-gradient-danger text-white mb-0">
                <i class="fas fa-undo me-1"></i> Registrar devolución
              </button>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>

@endif {{-- fin @else (tiene venta) --}}

</div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// ══ Buscador de código (sección sin venta) ═══════════════════════════════════
@if(!isset($venta) || $venta === null)
(function () {
  const modalEl    = document.getElementById('modalScannerDev');
  if (!modalEl) return;
  const modal      = new bootstrap.Modal(modalEl);
  const status     = document.getElementById('scannerStatusDev');
  const resultEl   = document.getElementById('scannerResultadoDev');
  const codigoEl   = document.getElementById('scannerCodigoDetectadoDev');
  const wrapSel    = document.getElementById('wrapSelectCamaraDev');
  const selectCam  = document.getElementById('selectCamaraDev');
  let scanner = null, activo = false;

  document.getElementById('btnScanCamaraDevolucion')?.addEventListener('click', () => {
    resultEl.classList.add('d-none');
    status.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Iniciando cámara...';
    modal.show();
  });

  modalEl.addEventListener('shown.bs.modal', async () => {
    try {
      const cams = await Html5Qrcode.getCameras();
      if (!cams?.length) { status.innerHTML = 'No se encontraron cámaras.'; return; }
      if (cams.length > 1) {
        selectCam.innerHTML = cams.map(c => `<option value="${c.id}">${c.label || c.id}</option>`).join('');
        wrapSel.classList.remove('d-none');
      }
      const id = cams.find(c => /back|rear|trasera|environment/i.test(c.label))?.id ?? cams[0].id;
      selectCam.value = id;
      await iniciar(id);
    } catch (e) { status.innerHTML = e.message; }
  });

  selectCam?.addEventListener('change', async () => { await detener(); await iniciar(selectCam.value); });

  async function iniciar(id) {
    if (!scanner) scanner = new Html5Qrcode('scannerContainerDev', {
      formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.CODE_39, Html5QrcodeSupportedFormats.QR_CODE],
      verbose: false,
    });
    await scanner.start(id, { fps: 12, qrbox: { width: 300, height: 100 }, aspectRatio: 1.6 },
      async (txt) => {
        await detener();
        codigoEl.textContent = txt;
        resultEl.classList.remove('d-none');
        status.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>¡Código detectado!';
        await new Promise(r => setTimeout(r, 600));
        document.getElementById('inputCodigoDevolucion').value = txt;
        modal.hide();
        setTimeout(() => document.getElementById('formBuscarDevolucion').submit(), 300);
      }, () => {}
    );
    activo = true;
    status.innerHTML = '<i class="fas fa-search me-1 text-danger"></i>Buscando código...';
  }

  async function detener() {
    if (scanner && activo) { try { await scanner.stop(); } catch(_) {} activo = false; }
  }

  ['btnCerrarScannerDev','btnCerrarScannerDev2'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', async () => { await detener(); modal.hide(); });
  });
  modalEl.addEventListener('hidden.bs.modal', async () => {
    await detener(); scanner = null;
    document.getElementById('scannerContainerDev').innerHTML = '';
    resultEl.classList.add('d-none'); wrapSel.classList.add('d-none');
  });

  // Auto-submit cuando el scanner HID llena el campo y pulsa Enter
  const inp = document.getElementById('inputCodigoDevolucion');
  if (inp) {
    inp.addEventListener('keydown', e => {
      if (e.key === 'Enter') { e.preventDefault(); if (inp.value.trim()) document.getElementById('formBuscarDevolucion').submit(); }
    });
  }
})();
@else
// ── Confirmación SweetAlert2 para el formulario de devolución ────────────────
document.getElementById('btnConfirmar')?.addEventListener('click', function () {
  const motivo  = document.querySelector('input[name="motivo"]:checked');
  const regresa = document.querySelector('input[name="regresa_inventario"]:checked');

  if (!motivo) {
    toast.warning('Selecciona el motivo de la devolución.');
    return;
  }
  if (!regresa) {
    toast.warning('Indica si el zapato vuelve al inventario.');
    return;
  }

  const regresamsg = regresa.value === '1' ? 'volverá al inventario' : 'quedará como devuelto';
  const monto      = parseFloat(document.getElementById('monto_devuelto').value || 0);
  const montoMsg   = monto > 0 ? ` Se reembolsarán Q${monto.toFixed(2)}.` : ' Sin reembolso.';

  Swal.fire({
    title: '¿Confirmar devolución?',
    html: `El zapato <strong>{{ $venta->zapato->codigo_unico }}</strong> ${regresamsg}.${montoMsg}`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, registrar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#f5365c',
  }).then(result => {
    if (result.isConfirmed) document.getElementById('formDevolucion').submit();
  });
});
@endif
</script>
@endpush
