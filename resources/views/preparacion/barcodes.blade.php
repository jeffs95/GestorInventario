@extends('layouts.user_type.auth')

@section('content')

<div class="row justify-content-center">
  <div class="col-xl-11 col-lg-12">

    {{-- ── Cabecera ──────────────────────────────────────────────────────────── --}}
    <div class="card mb-4 no-print">
      <div class="card-body py-3">
        <div class="row align-items-center g-3">

          <div class="col-auto">
            <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-md">
              <i class="fas fa-barcode text-white opacity-10 text-xl"></i>
            </div>
          </div>

          <div class="col">
            <h5 class="mb-0">
              Hoja de barcodes — {{ $zapato_lote->clasificacion_label }}
            </h5>
            <p class="mb-0 text-sm text-secondary">
              <strong>Lote:</strong>
              <a href="{{ route('lotes.show', $zapato_lote->apertura->lote) }}" class="text-info">
                {{ $zapato_lote->apertura->lote->numero_lote }}
              </a>
              &nbsp;|&nbsp;
              <strong>Categoría:</strong> {{ $zapato_lote->categoria->nombre }}
              &nbsp;|&nbsp;
              <strong>Tipo:</strong> {{ $zapato_lote->tipo->nombre }}
              &nbsp;|&nbsp;
              <strong>Total:</strong> {{ $zapato_lote->zapatos->count() }} etiquetas
            </p>
          </div>

          <div class="col-auto d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary">
              <i class="fas fa-print me-2"></i>Imprimir hoja
            </button>
            <a href="{{ route('preparacion.show', $zapato_lote) }}" class="btn btn-outline-secondary">
              <i class="fas fa-tags me-2"></i>Asignar precios
            </a>
          </div>

        </div>
      </div>
    </div>

    {{-- ── Instrucciones (pantalla) ──────────────────────────────────────────── --}}
    <div class="alert alert-info d-flex gap-3 align-items-start py-3 px-4 mb-4 no-print">
      <i class="fas fa-info-circle mt-1 flex-shrink-0"></i>
      <div class="text-sm">
        <strong>Flujo de preparación rápido:</strong>
        <ol class="mb-0 mt-1 ps-3">
          <li>Imprime esta hoja y <strong>pega una etiqueta en cada zapato</strong>.</li>
          <li>Ve a <strong>Asignar precios</strong>, escanea el barcode de cada zapato y escribe el precio.</li>
          <li>El zapato pasa automáticamente al inventario.</li>
        </ol>
      </div>
    </div>

    {{-- ── Grid de etiquetas ────────────────────────────────────────────────── --}}
    <div class="card no-print-border">
      <div class="card-body p-3" id="hoja-barcodes">
        <div class="row g-2" id="grid-etiquetas">
          @foreach($zapato_lote->zapatos as $zapato)
          <div class="col-6 col-sm-4 col-md-3 etiqueta-col">
            <div class="etiqueta-card text-center border rounded p-2"
                 data-codigo="{{ $zapato->codigo_unico }}">
              <p class="etiqueta-marca mb-1">PacaManager</p>
              <svg class="barcode-svg" style="width:100%;max-height:50px"></svg>
              <p class="etiqueta-codigo mb-0">{{ $zapato->codigo_unico }}</p>
              <p class="etiqueta-cat mb-0">
                {{ $zapato_lote->categoria->nombre }} / {{ $zapato_lote->tipo->nombre }}
              </p>
              <p class="etiqueta-cls mb-0">{{ $zapato_lote->clasificacion_label }}</p>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="d-flex gap-2 mt-4 no-print">
      <a href="{{ route('preparacion.show', $zapato_lote) }}" class="btn btn-success">
        <i class="fas fa-tags me-2"></i>Ir a asignar precios
      </a>
      <a href="{{ route('aperturas.show', $zapato_lote->apertura) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Volver a la apertura
      </a>
    </div>

  </div>
</div>

@endsection

@push('styles')
<style>
  /* ── Pantalla ──────────────────────────────────────── */
  .etiqueta-card {
    border-color: #e0e0e0 !important;
    border-radius: .5rem !important;
    background: #fff;
  }
  .etiqueta-marca  { font-size: .6rem; color: #888; font-weight: 600; letter-spacing: .04em; }
  .etiqueta-codigo { font-size: .65rem; font-family: monospace; font-weight: 700; letter-spacing: .05em; color: #1a3c5e; }
  .etiqueta-cat    { font-size: .58rem; color: #555; }
  .etiqueta-cls    { font-size: .55rem; color: #888; }

  /* ── Impresión ─────────────────────────────────────── */
  @media print {
    /* Ocultar todo el layout del dashboard */
    body * { visibility: hidden; }

    /* Mostrar solo la hoja de barcodes */
    #hoja-barcodes,
    #hoja-barcodes * { visibility: visible; }

    #hoja-barcodes {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      padding: 4mm !important;
    }

    /* Grid de impresión: 4 columnas fijas */
    #grid-etiquetas {
      display: grid !important;
      grid-template-columns: repeat(4, 1fr) !important;
      gap: 2mm !important;
    }

    .etiqueta-col {
      width: auto !important;
      flex: none !important;
      max-width: none !important;
      padding: 0 !important;
    }

    .etiqueta-card {
      page-break-inside: avoid;
      border: 0.4pt solid #ccc !important;
      border-radius: 0 !important;
      padding: 1.5mm !important;
    }

    .etiqueta-marca  { font-size: 6pt; color: #666; margin-bottom: 1mm; }
    .etiqueta-codigo { font-size: 7.5pt; font-family: monospace; font-weight: 700; letter-spacing: .04em; }
    .etiqueta-cat    { font-size: 6pt; color: #333; }
    .etiqueta-cls    { font-size: 5.5pt; color: #666; }

    .barcode-svg    { width: 100% !important; height: 36pt !important; }

    .no-print       { display: none !important; }
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.etiqueta-card').forEach(card => {
    const codigo = card.dataset.codigo;
    const svg    = card.querySelector('.barcode-svg');
    if (!codigo || !svg) return;

    JsBarcode(svg, codigo, {
      format:       'CODE128',
      width:        1.6,
      height:       44,
      displayValue: false,
      margin:       2,
      lineColor:    '#1a3c5e',
      background:   '#ffffff',
    });
  });
});
</script>
@endpush
