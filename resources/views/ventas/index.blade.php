@extends('layouts.user_type.auth')

@section('content')

{{-- ══ Stats del día ════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  <div class="col-sm-4">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-success shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-shopping-bag text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Ventas hoy</p>
          <h4 class="mb-0 font-weight-bolder">{{ $statsHoy['cantidad'] }}</h4>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-primary shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-dollar-sign text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Ingresos hoy</p>
          <h4 class="mb-0 font-weight-bolder">Q{{ number_format($statsHoy['ingresos'], 2) }}</h4>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-warning shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-tags text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Total rebajado hoy</p>
          <h4 class="mb-0 font-weight-bolder">Q{{ number_format($statsHoy['rebajado'], 2) }}</h4>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">

  {{-- ══ Columna izquierda: buscar + registrar ════════════════════════════ --}}
  <div class="col-lg-5">

    {{-- Buscador de código --}}
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-start">
        <div>
          <h6 class="mb-0">
            <i class="fas fa-barcode me-2 text-primary"></i>Buscar zapato
          </h6>
          <p class="text-xs text-secondary mb-0 mt-1">Ingresa el código único del zapato a vender</p>
        </div>
        <a href="{{ route('ventas.historial') }}" class="btn btn-outline-primary btn-sm py-1 px-2 flex-shrink-0"
           style="font-size:.75rem">
          <i class="fas fa-book me-1"></i>Historial
        </a>
      </div>
      <div class="card-body">
        <form method="GET" action="{{ route('ventas.index') }}" id="formBuscar">
          <div class="input-group">
            <input type="text"
                   name="codigo"
                   id="inputCodigo"
                   class="form-control form-control-lg"
                   placeholder="Ej: PL-ZL3-0001"
                   value="{{ request('codigo') }}"
                   autocomplete="off"
                   autofocus
                   style="font-family:monospace;font-size:1rem;letter-spacing:.05em">
            {{-- Botón cámara --}}
            <button type="button" id="btnScanCamara"
                    class="btn btn-outline-primary px-3"
                    title="Escanear con cámara">
              <i class="fas fa-camera" style="font-size:.95rem"></i>
            </button>
            <button type="submit" class="btn btn-primary px-4">
              <i class="fas fa-search" style="font-size:.95rem"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Error de búsqueda --}}
    @if($error)
    <div class="alert alert-warning d-flex align-items-center gap-2 py-2 px-3 mb-4">
      <i class="fas fa-exclamation-triangle"></i>
      <span class="text-sm">{{ $error }}</span>
    </div>
    @endif

    {{-- Tarjeta del zapato encontrado --}}
    @if($zapato)
    <div class="card border-2 mb-4" style="border-color:#0ea5e9 !important">
      <div class="card-header pb-0 d-flex justify-content-between align-items-start">
        <div>
          <span class="badge bg-gradient-success mb-1">En inventario</span>
          <h6 class="mb-0 font-monospace">{{ $zapato->codigo_unico }}</h6>
        </div>
        @if($zapato->foto_path)
          <img src="{{ route('inventario.foto', $zapato) }}"
               alt="foto"
               style="width:72px;height:72px;object-fit:cover;border-radius:.75rem;border:2px solid #e0f2fe">
        @else
          <div class="d-flex align-items-center justify-content-center bg-light"
               style="width:72px;height:72px;border-radius:.75rem">
            <i class="fas fa-shoe-prints text-secondary opacity-5 fs-4"></i>
          </div>
        @endif
      </div>
      <div class="card-body pt-2">
        {{-- Detalles del zapato --}}
        <div class="row g-2 mb-3">
          <div class="col-6">
            <p class="text-xxs text-secondary mb-0">Categoría</p>
            <p class="text-sm font-weight-bold mb-0">{{ $zapato->categoria->nombre ?? '—' }}</p>
          </div>
          <div class="col-6">
            <p class="text-xxs text-secondary mb-0">Tipo</p>
            <p class="text-sm font-weight-bold mb-0">{{ $zapato->tipo->nombre ?? '—' }}</p>
          </div>
          <div class="col-6">
            <p class="text-xxs text-secondary mb-0">Talla</p>
            <p class="text-sm font-weight-bold mb-0">{{ $zapato->talla->nombre ?? $zapato->talla ?? '—' }}</p>
          </div>
          <div class="col-6">
            <p class="text-xxs text-secondary mb-0">Clasificación</p>
            @php $cls=['regular'=>'secondary','primera_lavado'=>'warning','primera_lustre'=>'info']; @endphp
            <span class="badge badge-sm bg-gradient-{{ $cls[$zapato->clasificacion] ?? 'secondary' }}">
              {{ $zapato->clasificacion_label }}
            </span>
          </div>
          @if($zapato->color || $zapato->marca)
          <div class="col-6">
            <p class="text-xxs text-secondary mb-0">Color</p>
            <p class="text-sm font-weight-bold mb-0">{{ $zapato->color ?? '—' }}</p>
          </div>
          <div class="col-6">
            <p class="text-xxs text-secondary mb-0">Marca</p>
            <p class="text-sm font-weight-bold mb-0">{{ $zapato->marca ?? '—' }}</p>
          </div>
          @endif
          <div class="col-6">
            <p class="text-xxs text-secondary mb-0">Sucursal</p>
            <p class="text-sm font-weight-bold mb-0">{{ $zapato->sucursal->nombre ?? '—' }}</p>
          </div>
          <div class="col-6">
            <p class="text-xxs text-secondary mb-0">Precio lista</p>
            <p class="text-lg font-weight-bolder mb-0 text-primary">Q{{ number_format($zapato->precio_lista, 2) }}</p>
          </div>
        </div>

        {{-- Formulario de registro de venta --}}
        <form method="POST" action="{{ route('ventas.store') }}" id="formVenta">
          @csrf
          <input type="hidden" name="zapato_id" value="{{ $zapato->id }}">

          <div class="mb-3">
            <label class="form-label fw-bold">
              Precio de venta (Q) <span class="text-danger">*</span>
            </label>
            <input type="number"
                   name="precio_venta"
                   id="inputPrecioVenta"
                   class="form-control form-control-lg text-center"
                   step="any"
                   min="0.01"
                   value="{{ old('precio_venta', $zapato->precio_lista) }}"
                   required
                   autofocus>
            {{-- Indicador de diferencia en tiempo real --}}
            <div id="indicadorDiferencia" class="text-center mt-1 text-xs"></div>
          </div>

          <div class="mb-3">
            <label class="form-label text-xs fw-bold">Notas <span class="text-secondary fw-normal">(opcional)</span></label>
            <input type="text" name="notas" class="form-control form-control-sm"
                   placeholder="Descuento, cliente, observación..."
                   value="{{ old('notas') }}" maxlength="255">
          </div>

          <button type="submit" class="btn btn-success w-100 btn-lg" id="btnVender">
            <i class="fas fa-check-circle me-2"></i>Registrar venta
          </button>
        </form>
      </div>
    </div>
    @endif

    {{-- Estado vacío cuando no hay búsqueda --}}
    @if(!$zapato && !$error)
    <div class="card border-dashed text-center py-5">
      <div class="card-body">
        <i class="fas fa-barcode fa-3x text-secondary opacity-3 mb-3 d-block"></i>
        <p class="text-secondary text-sm mb-0">Ingresa el código del zapato para comenzar</p>
      </div>
    </div>
    @endif

  </div>

  {{-- ══ Columna derecha: ventas del día ═════════════════════════════════ --}}
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h6 class="mb-0">
            <i class="fas fa-receipt me-2 text-success"></i>Ventas de hoy
          </h6>
          <p class="text-xs text-secondary mb-0 mt-1" id="fechaHoyLabel">
            {{-- Guatemala: UTC-6, sin horario de verano --}}
            @php
              $hoyGT = now(); // ya con timezone America/Guatemala desde config
              $dias  = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
              $meses = ['','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
            @endphp
            {{ ucfirst($dias[$hoyGT->dayOfWeek]) }},
            {{ $hoyGT->day }} de {{ $meses[$hoyGT->month] }} {{ $hoyGT->year }}
          </p>
        </div>
        <div class="d-flex gap-2 align-items-center">
          <span class="badge bg-gradient-success">{{ $statsHoy['cantidad'] }} registros</span>
          <a href="{{ route('ventas.historial') }}" class="btn btn-outline-primary btn-sm py-1 px-2"
             style="font-size:.75rem">
            <i class="fas fa-book me-1"></i>Ver historial
          </a>
        </div>
      </div>
      {{-- Aviso de nuevo día (oculto por defecto, JS lo muestra) --}}
      <div id="alertaNuevoDia" class="d-none mx-3 mt-2 alert alert-info py-2 px-3 text-sm d-flex align-items-center gap-2">
        <i class="fas fa-sun text-warning"></i>
        <span>¡Nuevo día! Actualizando la lista de ventas...</span>
      </div>
      <div class="card-body px-0 pt-0 pb-0" style="overflow-y:auto;max-height:620px">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="sticky-top bg-white" style="top:0;z-index:1">
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Hora</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Detalle</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Lista</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Vendido</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dif.</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($ventasHoy as $venta)
              <tr>
                <td class="ps-3">
                  <span class="text-xs text-secondary">{{ $venta->created_at->format('H:i') }}</span>
                </td>
                <td class="ps-2">
                  <span class="text-xs font-monospace font-weight-bold text-primary">
                    {{ $venta->zapato->codigo_unico ?? '—' }}
                  </span>
                </td>
                <td class="ps-2">
                  <p class="text-xs font-weight-bold mb-0">
                    {{ $venta->zapato->categoria->nombre ?? '—' }}
                    {{ $venta->zapato->talla ? '/ ' . ($venta->zapato->talla->nombre ?? $venta->zapato->talla) : '' }}
                  </p>
                  <p class="text-xxs text-secondary mb-0">{{ $venta->usuario->name ?? '—' }}</p>
                </td>
                <td class="ps-2">
                  <span class="text-xs text-secondary">Q{{ number_format($venta->precio_lista, 2) }}</span>
                </td>
                <td class="ps-2">
                  <span class="text-xs font-weight-bolder text-success">Q{{ number_format($venta->precio_venta, 2) }}</span>
                </td>
                <td class="ps-2">
                  @php $dif = (float)$venta->precio_venta - (float)$venta->precio_lista; @endphp
                  <span class="text-xs font-weight-bold {{ $dif >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $dif >= 0 ? '+' : '' }}Q{{ number_format($dif, 2) }}
                  </span>
                </td>
                <td class="pe-3 text-end">
                  <a href="{{ route('ventas.recibo', $venta) }}" target="_blank"
                     class="btn btn-link p-0 text-primary text-xs" title="Ver / imprimir recibo">
                    <i class="fas fa-print"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-secondary text-sm">
                  <i class="fas fa-moon mb-2 d-block opacity-5 fa-2x"></i>
                  Aún no hay ventas registradas hoy.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

@endsection

{{-- ══ Modal Scanner de cámara ═══════════════════════════════════════════ --}}
<div class="modal fade" id="modalScanner" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
    <div class="modal-content border-0 shadow-lg" style="border-radius:1.25rem">

      <div class="modal-header border-0 pb-0 px-4 pt-3">
        <div>
          <h6 class="mb-0 text-primary">
            <i class="fas fa-camera me-2"></i>Escanear código de barras
          </h6>
          <p class="text-xs text-secondary mb-0">Apunta la cámara al código del zapato</p>
        </div>
        <button type="button" class="btn-close" id="btnCerrarScanner"></button>
      </div>

      <div class="modal-body px-3 pt-2 pb-3">
        <div id="wrapSelectCamara" class="mb-2 d-none">
          <select id="selectCamara" class="form-select form-select-sm"></select>
        </div>
        <div id="scannerContainer"
             style="width:100%;border-radius:.75rem;overflow:hidden;background:#000;min-height:200px"></div>
        <div id="scannerStatus" class="text-center mt-2 text-xs text-secondary">
          <i class="fas fa-spinner fa-spin me-1"></i>Iniciando cámara...
        </div>
        <div id="scannerResultado" class="d-none mt-2 alert alert-success py-2 px-3 text-sm text-center">
          <i class="fas fa-check-circle me-1"></i>
          <strong id="scannerCodigoDetectado"></strong>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0 px-4 pb-3">
        <button type="button" class="btn btn-outline-secondary w-100" id="btnCerrarScanner2">
          <i class="fas fa-times me-1"></i>Cancelar
        </button>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// ── Indicador de diferencia precio lista vs venta ────────────────────────────
const precioLista = {{ $zapato ? $zapato->precio_lista : 0 }};
const inputPrecio = document.getElementById('inputPrecioVenta');
const indicador   = document.getElementById('indicadorDiferencia');

if (inputPrecio && indicador && precioLista > 0) {
  function actualizarIndicador() {
    const pv  = parseFloat(inputPrecio.value) || 0;
    const dif = pv - precioLista;

    if (Math.abs(dif) < 0.01) {
      indicador.innerHTML = '<span class="text-secondary">Precio igual al de lista</span>';
    } else if (dif > 0) {
      indicador.innerHTML = `<span class="text-success"><i class="fas fa-arrow-up me-1"></i>Q${dif.toFixed(2)} sobre el precio lista</span>`;
    } else {
      indicador.innerHTML = `<span class="text-danger"><i class="fas fa-arrow-down me-1"></i>Rebaja de Q${Math.abs(dif).toFixed(2)}</span>`;
    }
  }
  inputPrecio.addEventListener('input', actualizarIndicador);
  actualizarIndicador();
}

// ── Confirmar venta con SweetAlert2 ─────────────────────────────────────────
const formVenta = document.getElementById('formVenta');
if (formVenta) {
  formVenta.addEventListener('submit', function (e) {
    e.preventDefault();
    const pv     = parseFloat(inputPrecio?.value || 0);
    const codigo = '{{ $zapato?->codigo_unico ?? "" }}';

    Swal.fire({
      ..._swalBase,
      title: '¿Confirmar venta?',
      html: `Zapato <strong>${codigo}</strong><br>Precio cobrado: <strong class="text-success">Q${pv.toFixed(2)}</strong>`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, registrar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (result.isConfirmed) formVenta.submit();
    });
  });
}

// ── Auto-focus en el input de código al cargar ───────────────────────────────
document.getElementById('inputCodigo')?.focus();

// ══ Scanner de cámara ════════════════════════════════════════════════════════
let scanner       = null;
let scannerActivo = false;

const modalScannerEl = document.getElementById('modalScanner');
const modalScanner   = new bootstrap.Modal(modalScannerEl);
const scannerStatus  = document.getElementById('scannerStatus');
const scannerResultEl= document.getElementById('scannerResultado');
const scannerCodigo  = document.getElementById('scannerCodigoDetectado');
const wrapSelect     = document.getElementById('wrapSelectCamara');
const selectCamara   = document.getElementById('selectCamara');

document.getElementById('btnScanCamara').addEventListener('click', () => {
  scannerResultEl.classList.add('d-none');
  scannerStatus.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Iniciando cámara...';
  modalScanner.show();
});

modalScannerEl.addEventListener('shown.bs.modal', async () => {
  try {
    const camaras = await Html5Qrcode.getCameras();
    if (!camaras || camaras.length === 0) {
      scannerStatus.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i>No se encontraron cámaras.';
      return;
    }
    if (camaras.length > 1) {
      selectCamara.innerHTML = camaras.map(c => `<option value="${c.id}">${c.label || 'Cámara ' + c.id}</option>`).join('');
      wrapSelect.classList.remove('d-none');
    }
    const camaraId = camaras.length > 1
      ? (camaras.find(c => /back|rear|trasera|environment/i.test(c.label))?.id ?? camaras[0].id)
      : camaras[0].id;
    selectCamara.value = camaraId;
    await iniciarScanner(camaraId);
  } catch (err) {
    scannerStatus.innerHTML = `<i class="fas fa-exclamation-triangle text-danger me-1"></i>${err.message || 'Error al acceder a la cámara.'}`;
  }
});

selectCamara?.addEventListener('change', async () => {
  await detenerScanner();
  await iniciarScanner(selectCamara.value);
});

async function iniciarScanner(camaraId) {
  if (!scanner) {
    scanner = new Html5Qrcode('scannerContainer', {
      formatsToSupport: [
        Html5QrcodeSupportedFormats.CODE_128,
        Html5QrcodeSupportedFormats.CODE_39,
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.QR_CODE,
      ],
      verbose: false,
    });
  }
  try {
    await scanner.start(
      camaraId,
      { fps: 12, qrbox: { width: 300, height: 100 }, aspectRatio: 1.6 },
      onScanExito,
      () => {}
    );
    scannerActivo = true;
    scannerStatus.innerHTML = '<i class="fas fa-search me-1 text-primary"></i>Buscando código...';
  } catch (err) {
    scannerStatus.innerHTML = `<i class="fas fa-exclamation-triangle text-danger me-1"></i>${err.message}`;
  }
}

async function onScanExito(decodedText) {
  await detenerScanner();
  scannerCodigo.textContent = decodedText;
  scannerResultEl.classList.remove('d-none');
  scannerStatus.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>¡Código detectado!';

  await new Promise(r => setTimeout(r, 700));

  // Llenar el campo de código y buscar automáticamente
  document.getElementById('inputCodigo').value = decodedText;
  modalScanner.hide();

  // Auto-submit para cargar el zapato
  setTimeout(() => document.getElementById('formBuscar').submit(), 350);
}

async function detenerScanner() {
  if (scanner && scannerActivo) {
    try { await scanner.stop(); } catch (_) {}
    scannerActivo = false;
  }
}

['btnCerrarScanner', 'btnCerrarScanner2'].forEach(id => {
  document.getElementById(id)?.addEventListener('click', async () => {
    await detenerScanner();
    modalScanner.hide();
    document.getElementById('inputCodigo')?.focus();
  });
});

modalScannerEl.addEventListener('hidden.bs.modal', async () => {
  await detenerScanner();
  scanner = null;
  document.getElementById('scannerContainer').innerHTML = '';
  scannerResultEl.classList.add('d-none');
  wrapSelect.classList.add('d-none');
});

// ── Auto-submit al escanear QR (el lector envía Enter automáticamente) ────────
// También detecta pegado rápido de texto (>3 chars en <80ms = scanner)
const inputCodigo = document.getElementById('inputCodigo');
if (inputCodigo) {
  let lastKeyTime = 0;
  let charCount   = 0;

  inputCodigo.addEventListener('keydown', (e) => {
    // Enter manual o de scanner → submit inmediato
    if (e.key === 'Enter') {
      e.preventDefault();
      if (inputCodigo.value.trim().length > 0) {
        document.getElementById('formBuscar').submit();
      }
      return;
    }
    // Detectar velocidad de escritura (scanner es muy rápido)
    const now  = Date.now();
    const diff = now - lastKeyTime;
    lastKeyTime = now;
    if (diff < 50) charCount++;  // teclas muy seguidas = scanner
    else charCount = 1;
  });

  // Si se pegó texto o scanner llenó el campo, auto-submit tras 80ms de silencio
  inputCodigo.addEventListener('input', () => {
    clearTimeout(window._scanTimer);
    window._scanTimer = setTimeout(() => {
      if (charCount >= 5 && inputCodigo.value.trim().length > 0) {
        document.getElementById('formBuscar').submit();
      }
    }, 80);
  });
}

// ══ Auto-refresh al cambiar de día (zona horaria Guatemala = UTC-6 fija) ══════
(function () {
  // Fecha de hoy según el servidor (ya en hora de Guatemala gracias a config/app.php)
  const HOY_SERVIDOR = '{{ now()->toDateString() }}'; // ej. "2026-05-28"

  /**
   * Calcula la fecha actual en Guatemala (UTC-6, sin horario de verano)
   * directamente desde el navegador, sin confiar en el timezone local del device.
   */
  function fechaGuatemala() {
    const ahora   = new Date();
    // Offset Guatemala es -360 minutos respecto UTC
    const offsetMs = -6 * 60 * 60 * 1000;
    const gt       = new Date(ahora.getTime() + ahora.getTimezoneOffset() * 60 * 1000 + offsetMs);
    const yy = gt.getFullYear();
    const mm = String(gt.getMonth() + 1).padStart(2, '0');
    const dd = String(gt.getDate()).padStart(2, '0');
    return `${yy}-${mm}-${dd}`;
  }

  function verificarCambioDia() {
    const hoyBrowser = fechaGuatemala();
    if (hoyBrowser !== HOY_SERVIDOR) {
      // Nuevo día detectado — mostrar aviso y recargar tras 2 s
      const alerta = document.getElementById('alertaNuevoDia');
      if (alerta) alerta.classList.remove('d-none');
      setTimeout(() => {
        // Recarga limpia (sin el parámetro ?codigo= si estaba presente)
        window.location.href = '{{ route('ventas.index') }}';
      }, 2000);
    }
  }

  // Revisar cada 60 segundos
  setInterval(verificarCambioDia, 60_000);
})();
</script>
@endpush
