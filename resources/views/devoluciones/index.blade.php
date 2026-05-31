@extends('layouts.user_type.auth')

@section('content')

{{-- Alertas flash — manejadas por <x-toasts /> en el layout --}}

<div class="row">

  {{-- ══ Nueva devolución — escáner de recibo ══════════════════════════════ --}}
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body py-3">
        <div class="row align-items-center g-3">

          {{-- Ícono --}}
          <div class="col-auto">
            <div class="icon icon-shape icon-lg bg-gradient-danger shadow border-radius-md
                        d-flex align-items-center justify-content-center">
              <i class="fas fa-undo text-white opacity-10" style="font-size:1rem"></i>
            </div>
          </div>

          {{-- Texto --}}
          <div class="col">
            <h6 class="mb-0">Registrar devolución</h6>
            <p class="text-xs text-secondary mb-0">
              Escanea el barcode del recibo o ingresa el código manualmente
            </p>
          </div>

          {{-- Campo + botones --}}
          <div class="col-xl-5 col-lg-6 col-12">
            <form method="GET" action="{{ route('devoluciones.create') }}" id="formScanDevolucion">
              <div class="input-group">
                <input type="text"
                       name="codigo"
                       id="inputCodigoDevIndex"
                       class="form-control"
                       placeholder="Código del recibo…"
                       autocomplete="off"
                       autofocus
                       style="font-family:monospace;font-size:.875rem;letter-spacing:.04em">
                <button type="button"
                        id="btnAbrirScannerIndex"
                        class="btn btn-outline-danger px-3"
                        title="Escanear con cámara">
                  <i class="fas fa-camera"></i>
                </button>
                <button type="submit" class="btn bg-gradient-danger text-white px-3">
                  <i class="fas fa-search"></i>
                </button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- ── 4 Tarjetas de resumen ─────────────────────────────────────────── --}}
  <div class="col-sm-6 col-md-3 mb-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-md bg-gradient-danger shadow border-radius-md text-center d-flex align-items-center justify-content-center">
          <i class="fas fa-undo text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Devoluciones</p>
          <h5 class="mb-0">{{ $stats['total'] }}</h5>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-md-3 mb-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-md bg-gradient-warning shadow border-radius-md text-center d-flex align-items-center justify-content-center">
          <i class="fas fa-coins text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Monto reembolsado</p>
          <h5 class="mb-0">Q{{ number_format($stats['monto_devuelto'], 2) }}</h5>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-md-3 mb-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-md bg-gradient-success shadow border-radius-md text-center d-flex align-items-center justify-content-center">
          <i class="fas fa-redo text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Vuelven al inventario</p>
          <h5 class="mb-0">{{ $stats['a_inventario'] }}</h5>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-md-3 mb-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-md bg-gradient-secondary shadow border-radius-md text-center d-flex align-items-center justify-content-center">
          <i class="fas fa-ban text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">No regresan</p>
          <h5 class="mb-0">{{ $stats['fuera'] }}</h5>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Filtros ────────────────────────────────────────────────────────── --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-body py-3">
        <form method="GET" action="{{ route('devoluciones.index') }}" class="row g-2 align-items-end">

          <div class="col-6 col-md-2">
            <label class="form-label text-xs mb-1">Desde</label>
            <input type="date" name="fecha_desde" class="form-control form-control-sm" value="{{ $desde }}">
          </div>
          <div class="col-6 col-md-2">
            <label class="form-label text-xs mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" class="form-control form-control-sm" value="{{ $hasta }}">
          </div>

          @if(auth()->user()->isDueno())
          <div class="col-6 col-md-2">
            <label class="form-label text-xs mb-1">Sucursal</label>
            <select name="sucursal_id" class="form-select form-select-sm">
              <option value="">Todas</option>
              @foreach($sucursales as $s)
                <option value="{{ $s->id }}" {{ request('sucursal_id') == $s->id ? 'selected' : '' }}>
                  {{ $s->nombre }}
                </option>
              @endforeach
            </select>
          </div>
          @endif

          <div class="col-6 col-md-2">
            <label class="form-label text-xs mb-1">Motivo</label>
            <select name="motivo" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="no_sirve"         {{ request('motivo') == 'no_sirve'         ? 'selected' : '' }}>No sirve</option>
              <option value="talla_incorrecta" {{ request('motivo') == 'talla_incorrecta' ? 'selected' : '' }}>Talla incorrecta</option>
              <option value="cambio_opinion"   {{ request('motivo') == 'cambio_opinion'   ? 'selected' : '' }}>Cambio de opinión</option>
              <option value="otro"             {{ request('motivo') == 'otro'             ? 'selected' : '' }}>Otro</option>
            </select>
          </div>

          <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-sm bg-gradient-primary text-white mb-0">
              <i class="fas fa-search me-1"></i> Filtrar
            </button>
            <a href="{{ route('devoluciones.index') }}" class="btn btn-sm btn-outline-secondary mb-0">
              <i class="fas fa-times"></i>
            </a>
          </div>

          {{-- Accesos rápidos --}}
          <div class="col-12 d-flex flex-wrap gap-2 pt-1">
            @php
              $hoy   = now()->toDateString();
              $ayer  = now()->subDay()->toDateString();
              $lunEs = now()->startOfWeek()->toDateString();
            @endphp
            <a href="{{ route('devoluciones.index', ['fecha_desde' => $hoy,  'fecha_hasta' => $hoy]) }}"
               class="btn btn-sm btn-outline-primary mb-0 py-1 text-xs">Hoy</a>
            <a href="{{ route('devoluciones.index', ['fecha_desde' => $ayer, 'fecha_hasta' => $ayer]) }}"
               class="btn btn-sm btn-outline-secondary mb-0 py-1 text-xs">Ayer</a>
            <a href="{{ route('devoluciones.index', ['fecha_desde' => $lunEs,'fecha_hasta' => $hoy]) }}"
               class="btn btn-sm btn-outline-secondary mb-0 py-1 text-xs">Esta semana</a>
          </div>

        </form>
      </div>
    </div>
  </div>

  {{-- ── Tabla de devoluciones ─────────────────────────────────────────── --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
          <i class="fas fa-undo me-2 text-danger"></i>
          Devoluciones
          <span class="text-secondary text-sm ms-1">{{ $desde }} — {{ $hasta }}</span>
        </h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        @if($devoluciones->isEmpty())
          <div class="text-center py-5 text-secondary">
            <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-6"></i>
            <p class="mb-0">No hay devoluciones en este período.</p>
          </div>
        @else
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha / Hora</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Zapato</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Motivo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Venta Q</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Reembolso Q</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado zapato</th>
                @if(auth()->user()->isDueno())
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Registrado por</th>
                @endif
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Notas</th>
              </tr>
            </thead>
            <tbody>
              @foreach($devoluciones as $dev)
              <tr>
                <td class="ps-3">
                  <p class="text-xs font-weight-bold mb-0">{{ $dev->created_at->format('d/m/Y') }}</p>
                  <p class="text-xs text-secondary mb-0">{{ $dev->created_at->format('H:i') }}</p>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0 font-monospace ps-2">{{ $dev->zapato->codigo_unico ?? '—' }}</p>
                  <p class="text-xs text-secondary mb-0 ps-2">
                    {{ $dev->zapato->categoria->nombre ?? '' }}
                    @if($dev->zapato->talla) · T.{{ $dev->zapato->talla->nombre ?? $dev->zapato->talla }} @endif
                  </p>
                </td>
                <td>
                  <span class="badge badge-sm ps-2
                    {{ match($dev->motivo) {
                        'no_sirve'         => 'bg-gradient-danger',
                        'talla_incorrecta' => 'bg-gradient-warning',
                        'cambio_opinion'   => 'bg-gradient-info',
                        default            => 'bg-gradient-secondary',
                      } }}">
                    {{ $dev->motivo_label }}
                  </span>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0 ps-2">
                    Q{{ number_format($dev->venta->precio_venta ?? 0, 2) }}
                  </p>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0 ps-2
                     {{ (float)$dev->monto_devuelto > 0 ? 'text-danger' : 'text-secondary' }}">
                    {{ (float)$dev->monto_devuelto > 0 ? 'Q'.number_format($dev->monto_devuelto, 2) : '—' }}
                  </p>
                </td>
                <td>
                  @if($dev->regresa_inventario)
                    <span class="badge badge-sm bg-gradient-success ps-2">
                      <i class="fas fa-redo me-1"></i>En inventario
                    </span>
                  @else
                    <span class="badge badge-sm bg-gradient-secondary ps-2">
                      <i class="fas fa-ban me-1"></i>Devuelto
                    </span>
                  @endif
                </td>
                @if(auth()->user()->isDueno())
                <td>
                  <p class="text-xs mb-0 ps-2">{{ $dev->usuario->name ?? '—' }}</p>
                  @if($dev->sucursal)
                    <p class="text-xxs text-secondary mb-0 ps-2">{{ $dev->sucursal->nombre }}</p>
                  @endif
                </td>
                @endif
                <td>
                  <p class="text-xs text-secondary mb-0 ps-2" style="max-width:160px;white-space:normal">
                    {{ $dev->notas ?? '—' }}
                  </p>
                </td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="bg-gray-100">
                <td colspan="3" class="ps-3 py-2">
                  <p class="text-xs font-weight-bolder mb-0">{{ $stats['total'] }} devoluciones</p>
                </td>
                <td class="py-2 ps-2">
                  <p class="text-xs font-weight-bolder mb-0 text-secondary">—</p>
                </td>
                <td class="py-2 ps-2">
                  <p class="text-xs font-weight-bolder mb-0 text-danger">
                    Q{{ number_format($stats['monto_devuelto'], 2) }}
                  </p>
                </td>
                <td colspan="{{ auth()->user()->isDueno() ? 3 : 2 }}"></td>
              </tr>
            </tfoot>
          </table>
        </div>
        @endif
      </div>
    </div>
  </div>

</div>

{{-- ══ Modal Scanner ══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalScannerIndex" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
    <div class="modal-content border-0 shadow-lg" style="border-radius:1.25rem">

      <div class="modal-header border-0 pb-0 px-4 pt-3">
        <div>
          <h6 class="mb-0 text-danger">
            <i class="fas fa-camera me-2"></i>Escanear recibo de venta
          </h6>
          <p class="text-xs text-secondary mb-0">
            Apunta la cámara al barcode del comprobante del cliente
          </p>
        </div>
        <button type="button" class="btn-close" id="btnCerrarScanIdx"></button>
      </div>

      <div class="modal-body px-3 pt-2 pb-3">
        <div id="wrapSelCamIdx" class="mb-2 d-none">
          <select id="selCamIdx" class="form-select form-select-sm"></select>
        </div>
        <div id="scannerIdx"
             style="width:100%;border-radius:.75rem;overflow:hidden;background:#000;min-height:200px"></div>
        <div id="scanStatusIdx" class="text-center mt-2 text-xs text-secondary">
          <i class="fas fa-spinner fa-spin me-1"></i>Iniciando cámara…
        </div>
        <div id="scanResultIdx" class="d-none mt-2 alert alert-success py-2 px-3 text-sm text-center">
          <i class="fas fa-check-circle me-1"></i>
          <strong id="scanCodigoIdx"></strong>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0 px-4 pb-3">
        <button type="button" class="btn btn-outline-secondary w-100" id="btnCerrarScanIdx2">
          <i class="fas fa-times me-1"></i>Cancelar
        </button>
      </div>

    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function () {
  const modalEl   = document.getElementById('modalScannerIndex');
  const modal     = new bootstrap.Modal(modalEl);
  const statusEl  = document.getElementById('scanStatusIdx');
  const resultEl  = document.getElementById('scanResultIdx');
  const codigoEl  = document.getElementById('scanCodigoIdx');
  const wrapSel   = document.getElementById('wrapSelCamIdx');
  const selCam    = document.getElementById('selCamIdx');
  const inputCod  = document.getElementById('inputCodigoDevIndex');
  let scanner = null, activo = false;

  // Abrir modal al pulsar el botón de cámara
  document.getElementById('btnAbrirScannerIndex').addEventListener('click', () => {
    resultEl.classList.add('d-none');
    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Iniciando cámara…';
    modal.show();
  });

  // Inicializar cámara cuando el modal está visible
  modalEl.addEventListener('shown.bs.modal', async () => {
    try {
      const cams = await Html5Qrcode.getCameras();
      if (!cams?.length) {
        statusEl.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i>No se encontraron cámaras.';
        return;
      }
      if (cams.length > 1) {
        selCam.innerHTML = cams.map(c => `<option value="${c.id}">${c.label || 'Cámara ' + c.id}</option>`).join('');
        wrapSel.classList.remove('d-none');
      }
      // Priorizar cámara trasera
      const camId = cams.find(c => /back|rear|trasera|environment/i.test(c.label))?.id ?? cams[0].id;
      selCam.value = camId;
      await iniciar(camId);
    } catch (err) {
      statusEl.innerHTML = `<i class="fas fa-exclamation-triangle text-danger me-1"></i>${err.message}`;
    }
  });

  selCam?.addEventListener('change', async () => {
    await detener();
    await iniciar(selCam.value);
  });

  async function iniciar(camId) {
    if (!scanner) {
      scanner = new Html5Qrcode('scannerIdx', {
        formatsToSupport: [
          Html5QrcodeSupportedFormats.CODE_128,
          Html5QrcodeSupportedFormats.CODE_39,
          Html5QrcodeSupportedFormats.QR_CODE,
        ],
        verbose: false,
      });
    }
    try {
      await scanner.start(
        camId,
        { fps: 12, qrbox: { width: 300, height: 100 }, aspectRatio: 1.6 },
        async (decoded) => {
          await detener();

          // Mostrar resultado
          codigoEl.textContent = decoded;
          resultEl.classList.remove('d-none');
          statusEl.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>¡Código detectado!';

          // Breve pausa para que el usuario vea el resultado
          await new Promise(r => setTimeout(r, 700));

          // Cerrar modal, llenar campo y enviar
          modal.hide();
          inputCod.value = decoded;
          setTimeout(() => document.getElementById('formScanDevolucion').submit(), 300);
        },
        () => {} // frame sin código — ignorar
      );
      activo = true;
      statusEl.innerHTML = '<i class="fas fa-search me-1 text-danger"></i>Buscando código en el recibo…';
    } catch (err) {
      statusEl.innerHTML = `<i class="fas fa-exclamation-triangle text-danger me-1"></i>${err.message}`;
    }
  }

  async function detener() {
    if (scanner && activo) {
      try { await scanner.stop(); } catch (_) {}
      activo = false;
    }
  }

  // Botones de cerrar
  ['btnCerrarScanIdx', 'btnCerrarScanIdx2'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', async () => {
      await detener();
      modal.hide();
    });
  });

  // Limpiar al cerrar el modal
  modalEl.addEventListener('hidden.bs.modal', async () => {
    await detener();
    scanner = null;
    document.getElementById('scannerIdx').innerHTML = '';
    resultEl.classList.add('d-none');
    wrapSel.classList.add('d-none');
  });

  // Auto-submit cuando scanner HID llena el campo (velocidad de tecleo rápida)
  if (inputCod) {
    let lastKey = 0, chars = 0;
    inputCod.addEventListener('keydown', e => {
      if (e.key === 'Enter') {
        e.preventDefault();
        if (inputCod.value.trim()) document.getElementById('formScanDevolucion').submit();
        return;
      }
      const now = Date.now();
      chars = (now - lastKey < 50) ? chars + 1 : 1;
      lastKey = now;
    });
    inputCod.addEventListener('input', () => {
      clearTimeout(window._scanDevTimer);
      window._scanDevTimer = setTimeout(() => {
        if (chars >= 5 && inputCod.value.trim())
          document.getElementById('formScanDevolucion').submit();
      }, 80);
    });
  }
})();
</script>
@endpush
