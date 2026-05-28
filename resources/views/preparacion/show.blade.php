@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-xl-11 col-lg-12">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="card mb-4">
      <div class="card-body py-3">
        <div class="row align-items-center g-3">

          <div class="col-auto">
            <div class="icon icon-shape icon-lg bg-gradient-warning shadow text-center border-radius-md">
              <i class="fas fa-tags text-white opacity-10 text-xl"></i>
            </div>
          </div>

          <div class="col">
            <h5 class="mb-0">Asignar precios — {{ $zapato_lote->clasificacion_label }}</h5>
            <p class="mb-0 text-sm text-secondary">
              <strong>Lote:</strong>
              <a href="{{ route('lotes.show', $zapato_lote->apertura->lote) }}" class="text-info">
                {{ $zapato_lote->apertura->lote->numero_lote }}
              </a>
              &nbsp;|&nbsp;
              <strong>Apertura:</strong>
              <a href="{{ route('aperturas.show', $zapato_lote->apertura) }}" class="text-info">
                #{{ $zapato_lote->apertura_id }}
              </a>
              &nbsp;|&nbsp;
              <strong>Categoría:</strong> {{ $zapato_lote->categoria->nombre }}
              &nbsp;|&nbsp;
              <strong>Tipo:</strong> {{ $zapato_lote->tipo->nombre }}
            </p>
          </div>

          {{-- Progreso --}}
          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Con precio</p>
            <h4 class="mb-0">
              <span class="text-success">{{ $zapato_lote->cantidad_registrada }}</span>
              <span class="text-secondary text-sm"> / {{ $zapato_lote->cantidad_contada }}</span>
            </h4>
          </div>

          <div class="col-auto">
            @if($zapato_lote->estado === 'completado')
              <span class="badge badge-sm bg-gradient-success fs-6">Completado</span>
            @elseif($zapato_lote->estado === 'en_preparacion')
              <span class="badge badge-sm bg-gradient-warning fs-6">En preparación</span>
            @else
              <span class="badge badge-sm bg-gradient-secondary fs-6">{{ $zapato_lote->estado }}</span>
            @endif
          </div>

        </div>

        {{-- Barra de progreso --}}
        <div class="mt-3">
          @php
            $pct = $zapato_lote->cantidad_contada > 0
              ? round(($zapato_lote->cantidad_registrada / $zapato_lote->cantidad_contada) * 100)
              : 0;
          @endphp
          <div class="d-flex justify-content-between mb-1">
            <span class="text-xs text-secondary">{{ $pct }}% con precio</span>
            <span class="text-xs text-secondary">Faltan {{ $zapato_lote->cantidad_contada - $zapato_lote->cantidad_registrada }}</span>
          </div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-gradient-{{ $zapato_lote->estado === 'completado' ? 'success' : 'warning' }}"
                 role="progressbar" style="width: {{ $pct }}%"></div>
          </div>
        </div>

      </div>
    </div>

    <div class="row g-4">

      {{-- ══ Columna izquierda: scanner + formulario precio ════════════════ --}}
      @if($zapato_lote->estado !== 'completado')
      <div class="col-lg-5">

        {{-- Botón para reimprimir barcodes --}}
        <div class="d-flex gap-2 mb-3">
          <a href="{{ route('preparacion.barcodes', $zapato_lote) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-barcode me-1"></i>Ver / reimprimir barcodes
          </a>
        </div>

        {{-- Alertas --}}
        @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3">
          <i class="fas fa-times-circle flex-shrink-0"></i>
          <span class="text-sm">{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3">
          @foreach($errors->all() as $e)
            <p class="text-xs mb-0">{{ $e }}</p>
          @endforeach
        </div>
        @endif

        {{-- Formulario de precio ─────────────────────────────────────────── --}}
        <div class="card">
          <div class="card-header pb-0">
            <h6 class="mb-0">
              <i class="fas fa-barcode me-2 text-primary"></i>Escanear y asignar precio
            </h6>
            <p class="text-xs text-secondary mb-0 mt-1">
              Escanea el barcode del zapato o escribe el código, luego ingresa el precio
            </p>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('preparacion.store', $zapato_lote) }}"
                  id="formPrecio" enctype="multipart/form-data">
              @csrf

              {{-- Código (escaneado o por cámara) --}}
              <div class="mb-3">
                <label class="form-label text-xs fw-bold">
                  Código del zapato <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input type="text"
                         name="codigo_unico"
                         id="inputCodigo"
                         class="form-control font-monospace"
                         placeholder="Ej: PL-ZL3-0001"
                         value="{{ old('codigo_unico') }}"
                         autocomplete="off"
                         autofocus
                         required
                         style="font-size:.95rem;letter-spacing:.05em">
                  <button type="button"
                          id="btnScanCamara"
                          class="btn btn-outline-primary px-3"
                          title="Escanear con cámara">
                    <i class="fas fa-camera" style="color:#1e6091;font-size:.9rem"></i>
                  </button>
                </div>
                <div class="text-xxs text-secondary mt-1">
                  <i class="fas fa-bolt me-1 text-warning"></i>
                  Escribe, escanea con lector físico o usa la <strong>cámara</strong>
                </div>
              </div>

              {{-- ── Fotos del zapato (obligatorio) ─────────────────────────── --}}
              <div class="mb-3">
                <label class="form-label text-xs fw-bold">
                  Fotos del zapato <span class="text-danger">*</span>
                  <span class="text-secondary fw-normal">(JPG/PNG, máx. 8 MB c/u)</span>
                </label>

                {{-- Contenedor de items de foto --}}
                <div id="fotosContainer">
                  {{-- Item #0 (obligatorio) --}}
                  <div class="foto-item mb-2" id="foto-item-0">
                    <div class="input-group input-group-sm">
                      <input type="file"
                             name="fotos[]"
                             accept="image/*"
                             capture="environment"
                             class="form-control form-control-sm foto-input"
                             data-idx="0"
                             required>
                    </div>
                    <div class="foto-preview-wrap mt-1 d-none">
                      <div class="d-flex align-items-start gap-2">
                        <img class="foto-thumb rounded"
                             src="#" alt="Vista previa"
                             style="height:72px;width:72px;object-fit:cover;border:1px solid #d2d6da;border-radius:.5rem">
                        <div>
                          <p class="foto-name text-xxs text-secondary mb-0"></p>
                          <p class="foto-size text-xxs text-secondary mb-0"></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Botón agregar otra foto --}}
                <button type="button" id="btnAgregarFoto"
                        class="btn btn-outline-secondary btn-sm mt-1 d-flex align-items-center gap-1">
                  <i class="fas fa-plus" style="font-size:.75rem"></i>
                  <span>Agregar otra foto</span>
                </button>
              </div>

              {{-- Precio --}}
              <div class="mb-3">
                <label class="form-label text-xs fw-bold">
                  Precio lista (Q) <span class="text-danger">*</span>
                </label>
                <input type="number"
                       step="any"
                       min="0.01"
                       name="precio_lista"
                       id="inputPrecio"
                       class="form-control form-control-lg text-center font-weight-bolder"
                       placeholder="0.00"
                       value="{{ old('precio_lista') }}"
                       required>
              </div>

              {{-- Campos opcionales colapsables --}}
              <div class="mb-3">
                <button type="button" class="btn btn-link text-xs text-secondary p-0"
                        data-bs-toggle="collapse" data-bs-target="#camposExtra">
                  <i class="fas fa-chevron-down me-1"></i>Talla, color, marca... (opcional)
                </button>
              </div>

              <div class="collapse" id="camposExtra">
                {{-- Talla --}}
                <div class="mb-2">
                  <label class="form-label text-xs fw-bold">Talla</label>
                  <select name="talla_id" class="form-select form-select-sm">
                    <option value="">— Sin especificar —</option>
                    @foreach($tallas as $t)
                      <option value="{{ $t->id }}" {{ old('talla_id') == $t->id ? 'selected' : '' }}>
                        {{ $t->nombre }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- Color + Marca --}}
                <div class="row">
                  <div class="col-6 mb-2">
                    <label class="form-label text-xs fw-bold">Color</label>
                    <input type="text" name="color" class="form-control form-control-sm"
                           value="{{ old('color') }}" placeholder="Negro">
                  </div>
                  <div class="col-6 mb-2">
                    <label class="form-label text-xs fw-bold">Marca</label>
                    <input type="text" name="marca" class="form-control form-control-sm"
                           value="{{ old('marca') }}" placeholder="Nike">
                  </div>
                </div>

                {{-- Género + Condición --}}
                <div class="row">
                  <div class="col-6 mb-2">
                    <label class="form-label text-xs fw-bold">Género</label>
                    <select name="genero" class="form-select form-select-sm">
                      <option value="">— —</option>
                      <option value="hombre"  {{ old('genero') === 'hombre'  ? 'selected' : '' }}>Hombre</option>
                      <option value="mujer"   {{ old('genero') === 'mujer'   ? 'selected' : '' }}>Mujer</option>
                      <option value="nino"    {{ old('genero') === 'nino'    ? 'selected' : '' }}>Niño</option>
                      <option value="nina"    {{ old('genero') === 'nina'    ? 'selected' : '' }}>Niña</option>
                      <option value="unisex"  {{ old('genero') === 'unisex'  ? 'selected' : '' }}>Unisex</option>
                    </select>
                  </div>
                  <div class="col-6 mb-2">
                    <label class="form-label text-xs fw-bold">Condición</label>
                    <select name="condicion" class="form-select form-select-sm">
                      <option value="">— —</option>
                      <option value="muy_bueno" {{ old('condicion') === 'muy_bueno' ? 'selected' : '' }}>Muy bueno</option>
                      <option value="bueno"     {{ old('condicion') === 'bueno'     ? 'selected' : '' }}>Bueno</option>
                      <option value="regular"   {{ old('condicion') === 'regular'   ? 'selected' : '' }}>Regular</option>
                    </select>
                  </div>
                </div>

                {{-- Notas --}}
                <div class="mb-3">
                  <label class="form-label text-xs fw-bold">Notas</label>
                  <input type="text" name="notas" class="form-control form-control-sm"
                         value="{{ old('notas') }}" placeholder="Observaciones...">
                </div>
              </div>

              <button type="submit" class="btn btn-success w-100 btn-lg" id="btnAsignar">
                <i class="fas fa-check me-2"></i>Asignar precio
                <span class="badge bg-white text-dark ms-1">
                  {{ $zapato_lote->cantidad_contada - $zapato_lote->cantidad_registrada }} restantes
                </span>
              </button>

            </form>
          </div>
        </div>

      </div>
      @endif

      {{-- ══ Columna derecha: tabla del lote ═══════════════════════════════ --}}
      <div class="col-lg-{{ $zapato_lote->estado !== 'completado' ? '7' : '12' }}">
        <div class="card h-100">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0">
              <i class="fas fa-list me-2 text-secondary"></i>
              Zapatos del lote
            </h6>
            <div class="d-flex gap-2 align-items-center">
              @if($zapato_lote->estado === 'completado')
                <span class="badge bg-gradient-success">
                  <i class="fas fa-check me-1"></i>Lote completado
                </span>
              @else
                <a href="{{ route('preparacion.barcodes', $zapato_lote) }}"
                   class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-barcode me-1"></i>Barcodes
                </a>
              @endif
            </div>
          </div>

          <div class="card-body px-0 pt-0 pb-0" style="overflow-y:auto;max-height:580px">
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="sticky-top bg-white" style="top:0;z-index:1">
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">#</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Precio</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Talla</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Barcode</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($zapato_lote->zapatos as $i => $z)
                  <tr class="{{ $z->estado === 'pendiente_precio' ? '' : 'table-active opacity-75' }}">
                    <td class="ps-3">
                      <span class="text-xs text-secondary">{{ $i + 1 }}</span>
                    </td>
                    <td class="ps-2">
                      <span class="text-xs font-weight-bold font-monospace {{ $z->estado === 'pendiente_precio' ? 'text-primary' : 'text-secondary' }}">
                        {{ $z->codigo_unico }}
                      </span>
                    </td>
                    <td class="ps-2">
                      @if($z->estado === 'pendiente_precio')
                        <span class="badge badge-sm bg-gradient-warning">Sin precio</span>
                      @else
                        <span class="badge badge-sm bg-gradient-success">
                          <i class="fas fa-check me-1"></i>Listo
                        </span>
                      @endif
                    </td>
                    <td class="ps-2">
                      @if($z->estado === 'pendiente_precio')
                        <span class="text-xs text-secondary">—</span>
                      @else
                        <span class="text-xs font-weight-bold text-success">
                          Q{{ number_format($z->precio_lista, 2) }}
                        </span>
                      @endif
                    </td>
                    <td class="ps-2">
                      <span class="text-xs">{{ $z->talla->nombre ?? '—' }}</span>
                    </td>
                    <td class="ps-2">
                      <button type="button"
                              class="btn btn-sm btn-outline-primary px-2 py-1 btn-barcode"
                              title="Ver barcode"
                              data-codigo="{{ $z->codigo_unico }}"
                              data-precio="{{ $z->estado !== 'pendiente_precio' ? 'Q'.number_format($z->precio_lista, 2) : '' }}">
                        <i class="fas fa-barcode"></i>
                      </button>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-secondary text-sm">
                      No hay zapatos en este lote todavía.
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          {{-- Resumen pie --}}
          @if($zapato_lote->zapatos->count() > 0)
          <div class="card-footer py-2 px-3 d-flex gap-4">
            <span class="text-xs text-secondary">
              <i class="fas fa-check-circle text-success me-1"></i>
              Con precio: <strong class="text-success">{{ $zapato_lote->zapatos->where('estado', 'en_inventario')->count() }}</strong>
            </span>
            <span class="text-xs text-secondary">
              <i class="fas fa-clock text-warning me-1"></i>
              Sin precio: <strong class="text-warning">{{ $zapato_lote->zapatos->where('estado', 'pendiente_precio')->count() }}</strong>
            </span>
          </div>
          @endif

        </div>
      </div>

    </div>

    <div class="d-flex gap-2 mt-4 mb-4">
      @if($zapato_lote->estado !== 'completado')
      <a href="{{ route('preparacion.barcodes', $zapato_lote) }}"
         class="btn btn-outline-primary btn-sm">
        <i class="fas fa-barcode me-1"></i>Ver / reimprimir barcodes
      </a>
      @endif
      <a href="{{ route('aperturas.show', $zapato_lote->apertura) }}"
         class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Volver a la apertura
      </a>
    </div>

  </div>
</div>

{{-- ══ Modal Scanner de cámara ════════════════════════════════════════════ --}}
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

        {{-- Selector de cámara (aparece si hay más de una) --}}
        <div id="wrapSelectCamara" class="mb-2 d-none">
          <select id="selectCamara" class="form-select form-select-sm">
            <option value="">Cargando cámaras...</option>
          </select>
        </div>

        {{-- Área de video --}}
        <div id="scannerContainer"
             style="width:100%;border-radius:.75rem;overflow:hidden;background:#000;min-height:200px">
        </div>

        {{-- Estado / feedback --}}
        <div id="scannerStatus" class="text-center mt-2 text-xs text-secondary">
          <i class="fas fa-spinner fa-spin me-1"></i>Iniciando cámara...
        </div>

        {{-- Resultado detectado --}}
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

{{-- ══ Modal Barcode individual ════════════════════════════════════════════ --}}
<div class="modal fade" id="modalBarcode" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
    <div class="modal-content border-0 shadow-lg" style="border-radius:1.25rem">

      <div class="modal-header border-0 pb-0 px-4 pt-3">
        <div>
          <h6 class="mb-0 text-primary"><i class="fas fa-barcode me-2"></i>Código de barras</h6>
          <p class="text-xs text-secondary mb-0">Imprime la etiqueta y pégala en el zapato</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body px-4 pt-3 pb-2 text-center">
        <div class="bg-white rounded p-3 d-inline-block border mb-2" style="min-width:280px">
          <svg id="barcodeModal"></svg>
          <p class="font-monospace fw-bold text-dark mb-0 mt-1"
             id="barcodeCodigoModal" style="font-size:.85rem;letter-spacing:.06em"></p>
          <p id="barcodePrecioModal" class="text-success fw-bold mb-0" style="font-size:1rem"></p>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0 px-4 pb-3 d-flex gap-2">
        <button type="button" class="btn btn-primary flex-grow-1" onclick="imprimirEtiqueta()">
          <i class="fas fa-print me-1"></i>Imprimir etiqueta
        </button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Cerrar
        </button>
      </div>

    </div>
  </div>
</div>

{{-- ══ Etiqueta de impresión individual ════════════════════════════════════ --}}
<div id="etiquetaImpresion">
  <div class="etiqueta-wrap">
    <p class="etiqueta-marca">PacaManager</p>
    <svg id="barcodeEtiqueta"></svg>
    <p id="etiquetaCodigo" class="etiqueta-codigo"></p>
    <p id="etiquetaDetalle" class="etiqueta-detalle"></p>
    <p id="etiquetaPrecio" class="etiqueta-precio">Q—</p>
  </div>
</div>

@endsection

@push('styles')
<style>
  .form-control {
    border: 1px solid #d2d6da !important;
    border-radius: 0.5rem !important;
  }
  .form-control:focus {
    border-color: #0ea5e9 !important;
    box-shadow: 0 0 0 2px rgba(14,165,233,.2) !important;
  }

  /* ── Fotos múltiples ── */
  .foto-item .input-group-sm .form-control { font-size: .78rem; }
  .foto-thumb { transition: opacity .2s; }
  #btnAgregarFoto { font-size: .78rem; color: #67748e; border-color: #d2d6da; }
  #btnAgregarFoto:hover { background: #f8f9fa; color: #344767; }

  /* ── Scanner de cámara ── */
  #scannerContainer video       { width: 100% !important; border-radius: .5rem; }
  #scannerContainer canvas      { border-radius: .5rem; }
  /* El área de mira (qrbox) que html5-qrcode dibuja */
  #scannerContainer #qr-shaded-region { border: 3px solid #0ea5e9 !important; border-radius: 4px; }

  /* ── Etiqueta: oculta en pantalla, visible solo al imprimir ── */
  #etiquetaImpresion { display: none; }

  @media print {
    body * { visibility: hidden; }
    #etiquetaImpresion,
    #etiquetaImpresion * { visibility: visible; }
    #etiquetaImpresion {
      display: block !important;
      position: fixed;
      top: 0; left: 0;
    }
    .etiqueta-wrap {
      width: 62mm;
      padding: 3mm 4mm;
      font-family: Arial, sans-serif;
      text-align: center;
    }
    .etiqueta-marca  { font-size: 7pt; color: #555; margin: 0 0 1.5mm; }
    .etiqueta-codigo { font-size: 8.5pt; font-weight: 700; font-family: monospace;
                       letter-spacing: .04em; margin: 1mm 0 .5mm; }
    .etiqueta-detalle{ font-size: 7pt; color: #333; margin: 0 0 1mm; }
    .etiqueta-precio { font-size: 14pt; font-weight: 900; margin: 0; }
    #barcodeEtiqueta { width: 100% !important; height: auto !important; }
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
// ══ Módulo de múltiples fotos ═══════════════════════════════════════════════
let fotoCount = 1; // empezamos con 1 input (índice 0)
const MAX_FOTOS = 6;

/**
 * Activa el preview de miniatura para un input de foto.
 */
function activarPreviewFoto(inputEl) {
  inputEl.addEventListener('change', function () {
    const wrap  = this.closest('.foto-item').querySelector('.foto-preview-wrap');
    const thumb = wrap.querySelector('.foto-thumb');
    const name  = wrap.querySelector('.foto-name');
    const size  = wrap.querySelector('.foto-size');
    const file  = this.files[0];

    if (!file) { wrap.classList.add('d-none'); return; }

    name.textContent = file.name.length > 30 ? file.name.substring(0, 28) + '…' : file.name;
    size.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';

    const reader = new FileReader();
    reader.onload = e => {
      thumb.src = e.target.result;
      wrap.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  });
}

// Activar el primer input ya presente en el DOM
document.querySelectorAll('.foto-input').forEach(activarPreviewFoto);

// ── Agregar foto extra ───────────────────────────────────────────────────────
document.getElementById('btnAgregarFoto').addEventListener('click', function () {
  if (fotoCount >= MAX_FOTOS) {
    Toastify({
      text: `Máximo ${MAX_FOTOS} fotos por zapato.`,
      duration: 3000,
      gravity: 'top', position: 'right',
      style: { background: '#f5365c', borderRadius: '.5rem', fontSize: '.82rem' },
    }).showToast();
    return;
  }

  const idx = fotoCount++;
  const item = document.createElement('div');
  item.className = 'foto-item mb-2';
  item.id = `foto-item-${idx}`;
  item.innerHTML = `
    <div class="input-group input-group-sm">
      <input type="file"
             name="fotos[]"
             accept="image/*"
             capture="environment"
             class="form-control form-control-sm foto-input"
             data-idx="${idx}">
      <button type="button"
              class="btn btn-outline-danger btn-sm px-2 btn-quitar-foto"
              title="Quitar foto"
              data-item="foto-item-${idx}">
        <i class="fas fa-times" style="font-size:.75rem"></i>
      </button>
    </div>
    <div class="foto-preview-wrap mt-1 d-none">
      <div class="d-flex align-items-start gap-2">
        <img class="foto-thumb rounded"
             src="#" alt="Vista previa"
             style="height:72px;width:72px;object-fit:cover;border:1px solid #d2d6da;border-radius:.5rem">
        <div>
          <p class="foto-name text-xxs text-secondary mb-0"></p>
          <p class="foto-size text-xxs text-secondary mb-0"></p>
        </div>
      </div>
    </div>`;

  document.getElementById('fotosContainer').appendChild(item);
  activarPreviewFoto(item.querySelector('.foto-input'));

  // Ocultar botón si llegamos al máximo
  if (fotoCount >= MAX_FOTOS) {
    this.classList.add('d-none');
  }
});

// ── Quitar foto extra (delegado) ─────────────────────────────────────────────
document.getElementById('fotosContainer').addEventListener('click', function (e) {
  const btn = e.target.closest('.btn-quitar-foto');
  if (!btn) return;
  const itemId = btn.dataset.item;
  document.getElementById(itemId)?.remove();
  fotoCount--;
  document.getElementById('btnAgregarFoto').classList.remove('d-none');
});

// ── Barcode modal ────────────────────────────────────────────────────────────
let _codigoActivo = null;
const _modalEl    = document.getElementById('modalBarcode');
const _modal      = new bootstrap.Modal(_modalEl);

function mostrarBarcode(codigo, precio) {
  _codigoActivo = codigo;

  JsBarcode('#barcodeModal', codigo, {
    format:       'CODE128',
    width:        2.2,
    height:       60,
    displayValue: false,
    margin:       4,
    lineColor:    '#1a3c5e',
    background:   '#ffffff',
  });

  document.getElementById('barcodeCodigoModal').textContent = codigo;
  document.getElementById('barcodePrecioModal').textContent = precio || '';
  _modal.show();
}

function imprimirEtiqueta() {
  if (!_codigoActivo) return;

  JsBarcode('#barcodeEtiqueta', _codigoActivo, {
    format:       'CODE128',
    width:        2,
    height:       50,
    displayValue: false,
    margin:       2,
    lineColor:    '#000000',
    background:   '#ffffff',
  });

  document.getElementById('etiquetaCodigo').textContent = _codigoActivo;
  document.getElementById('etiquetaPrecio').textContent =
    document.getElementById('barcodePrecioModal').textContent || 'Q—';
  window.print();
}

// ── Botones de barcode en la tabla ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-barcode').forEach(btn => {
    btn.addEventListener('click', () => {
      mostrarBarcode(btn.dataset.codigo, btn.dataset.precio || '');
    });
  });
});

// ── Scanner de código: auto-focus a precio cuando llega Enter ───────────────
const inputCodigo = document.getElementById('inputCodigo');
const inputPrecio = document.getElementById('inputPrecio');

if (inputCodigo && inputPrecio) {
  let lastKeyTime = 0;
  let charCount   = 0;

  inputCodigo.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      if (inputCodigo.value.trim().length > 0) {
        // Saltar al campo de precio para que el usuario lo ingrese de inmediato
        inputPrecio.focus();
        inputPrecio.select();
      }
      return;
    }
    const now  = Date.now();
    const diff = now - lastKeyTime;
    lastKeyTime = now;
    if (diff < 50) charCount++;
    else charCount = 1;
  });

  // Scanner rápido (pegado o scanner HID): saltar al precio automáticamente
  inputCodigo.addEventListener('input', () => {
    clearTimeout(window._scanTimer);
    window._scanTimer = setTimeout(() => {
      if (charCount >= 5 && inputCodigo.value.trim().length > 0) {
        inputPrecio.focus();
        inputPrecio.select();
      }
    }, 80);
  });

  // En el campo de precio, Enter → submit
  inputPrecio.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      document.getElementById('formPrecio').requestSubmit();
    }
  });
}

// ── Auto-focus inicial ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('inputCodigo')?.focus();
});

// ── Último código procesado: resaltar fila ───────────────────────────────────
@if(session('ultimo_codigo'))
document.addEventListener('DOMContentLoaded', () => {
  const ultimo = @json(session('ultimo_codigo'));
  document.querySelectorAll('.font-monospace').forEach(el => {
    if (el.textContent.trim() === ultimo) {
      el.closest('tr')?.classList.add('table-success');
    }
  });
});
@endif

// ══ Scanner de cámara con html5-qrcode ══════════════════════════════════════
let scanner        = null;
let scannerActivo  = false;

const modalScannerEl  = document.getElementById('modalScanner');
const modalScanner    = new bootstrap.Modal(modalScannerEl);
const scannerStatus   = document.getElementById('scannerStatus');
const scannerResultEl = document.getElementById('scannerResultado');
const scannerCodigo   = document.getElementById('scannerCodigoDetectado');
const wrapSelect      = document.getElementById('wrapSelectCamara');
const selectCamara    = document.getElementById('selectCamara');

// ── Abrir modal y arrancar scanner ───────────────────────────────────────────
document.getElementById('btnScanCamara').addEventListener('click', () => {
  scannerResultEl.classList.add('d-none');
  scannerStatus.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Iniciando cámara...';
  modalScanner.show();
});

// ── Al mostrar el modal: inicializar scanner ─────────────────────────────────
modalScannerEl.addEventListener('shown.bs.modal', async () => {
  try {
    // Listar cámaras disponibles
    const camaras = await Html5Qrcode.getCameras();

    if (!camaras || camaras.length === 0) {
      scannerStatus.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i>No se encontraron cámaras.';
      return;
    }

    // Si hay más de una cámara, mostrar el selector
    if (camaras.length > 1) {
      selectCamara.innerHTML = camaras
        .map(c => `<option value="${c.id}">${c.label || 'Cámara ' + c.id}</option>`)
        .join('');
      wrapSelect.classList.remove('d-none');
    }

    // Preferir cámara trasera en móviles
    const camaraId = camaras.length > 1
      ? (camaras.find(c => /back|rear|trasera|environment/i.test(c.label))?.id ?? camaras[0].id)
      : camaras[0].id;

    selectCamara.value = camaraId;
    await iniciarScanner(camaraId);

  } catch (err) {
    scannerStatus.innerHTML = `<i class="fas fa-exclamation-triangle text-danger me-1"></i>${err.message || 'Error al acceder a la cámara.'}`;
  }
});

// ── Cambiar cámara desde el selector ────────────────────────────────────────
selectCamara.addEventListener('change', async () => {
  await detenerScanner();
  await iniciarScanner(selectCamara.value);
});

// ── Iniciar el scanner ───────────────────────────────────────────────────────
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
      {
        fps: 12,
        qrbox: { width: 300, height: 100 }, // rectángulo horizontal para barcodes
        aspectRatio: 1.6,
      },
      onScanExito,
      () => {} // errores de frame (normales mientras busca) → ignorar
    );

    scannerActivo = true;
    scannerStatus.innerHTML = '<i class="fas fa-search me-1 text-primary"></i>Buscando código...';

  } catch (err) {
    scannerStatus.innerHTML = `<i class="fas fa-exclamation-triangle text-danger me-1"></i>${err.message}`;
  }
}

// ── Callback cuando detecta un código ───────────────────────────────────────
async function onScanExito(decodedText) {
  // Detener inmediatamente para no disparar múltiples veces
  await detenerScanner();

  // Mostrar resultado en el modal
  scannerCodigo.textContent = decodedText;
  scannerResultEl.classList.remove('d-none');
  scannerStatus.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>¡Código detectado!';

  // Pequeña pausa visual para que el usuario lo vea
  await new Promise(r => setTimeout(r, 700));

  // Llenar el campo de código y cerrar modal
  document.getElementById('inputCodigo').value = decodedText;
  modalScanner.hide();

  // Enfocar precio
  setTimeout(() => {
    const ip = document.getElementById('inputPrecio');
    ip?.focus();
    ip?.select();
  }, 350); // dar tiempo al modal de cerrarse
}

// ── Detener scanner ──────────────────────────────────────────────────────────
async function detenerScanner() {
  if (scanner && scannerActivo) {
    try {
      await scanner.stop();
    } catch (_) {}
    scannerActivo = false;
  }
}

// ── Cerrar modal (botones X y Cancelar) ─────────────────────────────────────
['btnCerrarScanner', 'btnCerrarScanner2'].forEach(id => {
  document.getElementById(id)?.addEventListener('click', async () => {
    await detenerScanner();
    modalScanner.hide();
    document.getElementById('inputCodigo')?.focus();
  });
});

// ── Limpiar si el modal se cierra por cualquier motivo ───────────────────────
modalScannerEl.addEventListener('hidden.bs.modal', async () => {
  await detenerScanner();
  // Resetear el div del scanner para re-uso
  scanner = null;
  document.getElementById('scannerContainer').innerHTML = '';
  scannerResultEl.classList.add('d-none');
  wrapSelect.classList.add('d-none');
});
</script>
@endpush
