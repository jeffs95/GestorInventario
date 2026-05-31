<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recibo de venta — {{ $venta->zapato->codigo_unico }}</title>
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Courier New', monospace;
      background: #f0f0f0;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 24px 16px;
      min-height: 100vh;
    }

    /* Botones de acción — ocultos al imprimir */
    .acciones {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      no-print: true;
    }
    .btn-imprimir {
      background: #1a3c5e;
      color: #fff;
      border: none;
      padding: 10px 24px;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      font-family: sans-serif;
    }
    .btn-cerrar {
      background: #fff;
      color: #555;
      border: 1px solid #ddd;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      font-family: sans-serif;
    }
    .btn-devolver {
      background: #f5365c;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 14px;
      cursor: pointer;
      font-family: sans-serif;
      display: flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
    }

    /* Ticket */
    .ticket {
      background: #fff;
      width: 100%;
      max-width: 360px;
      border-radius: 4px 4px 0 0;
      box-shadow: 0 4px 24px rgba(0,0,0,.14);
      padding: 24px 20px 16px;
    }

    /* Borde dentado inferior */
    .ticket-bottom {
      width: 100%;
      max-width: 360px;
      height: 20px;
      background:
        radial-gradient(circle at 10px 0, #f0f0f0 8px, transparent 9px),
        repeating-linear-gradient(to right, transparent 0, transparent 9px, #fff 9px, #fff 20px);
      background-color: #fff;
      box-shadow: 0 4px 24px rgba(0,0,0,.14);
    }

    .ticket-logo {
      text-align: center;
      margin-bottom: 12px;
    }
    .ticket-logo .nombre-negocio {
      font-family: sans-serif;
      font-size: 18px;
      font-weight: 800;
      color: #1a3c5e;
      letter-spacing: .04em;
    }
    .ticket-logo .sucursal {
      font-family: sans-serif;
      font-size: 11px;
      color: #888;
      margin-top: 2px;
    }

    .divider {
      border: none;
      border-top: 1px dashed #ccc;
      margin: 10px 0;
    }

    .ticket-fecha {
      text-align: center;
      font-size: 11px;
      color: #888;
      margin-bottom: 10px;
    }

    .ticket-titulo {
      text-align: center;
      font-family: sans-serif;
      font-size: 12px;
      font-weight: 700;
      color: #1a3c5e;
      text-transform: uppercase;
      letter-spacing: .1em;
      margin-bottom: 10px;
    }

    .ticket-fila {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      color: #444;
      margin-bottom: 4px;
    }
    .ticket-fila .label { color: #888; }
    .ticket-fila .valor { font-weight: 600; text-align: right; }

    .ticket-precio {
      text-align: center;
      margin: 14px 0 6px;
    }
    .ticket-precio .label-precio {
      font-family: sans-serif;
      font-size: 11px;
      color: #888;
      text-transform: uppercase;
      letter-spacing: .06em;
    }
    .ticket-precio .monto {
      font-family: sans-serif;
      font-size: 32px;
      font-weight: 800;
      color: #1a3c5e;
      line-height: 1.1;
    }

    /* Barcode */
    .barcode-wrap {
      text-align: center;
      margin: 14px 0 6px;
    }
    .barcode-wrap svg { max-width: 100%; }
    .barcode-codigo {
      font-size: 13px;
      font-weight: 700;
      color: #1a3c5e;
      letter-spacing: .1em;
      margin-top: 4px;
    }

    .nota-devolucion {
      text-align: center;
      font-family: sans-serif;
      font-size: 10px;
      color: #aaa;
      margin-top: 12px;
      line-height: 1.5;
    }
    .nota-devolucion strong { color: #888; }

    /* ── Media Print ─────────────────────────────────────────────────── */
    @media print {
      body { background: #fff; padding: 0; }
      .acciones { display: none !important; }
      .ticket {
        box-shadow: none;
        border: 1px solid #ddd;
        max-width: 100%;
        width: 80mm; /* ticket de 80mm */
      }
      .ticket-bottom { display: none; }
    }
  </style>
</head>
<body>

  {{-- Botones (ocultos al imprimir) --}}
  <div class="acciones">
    <button class="btn-imprimir" onclick="window.print()">
      🖨 Imprimir
    </button>
    @if(!$venta->devolucion)
    <a class="btn-devolver"
       href="{{ route('devoluciones.create', ['venta_id' => $venta->id]) }}">
      ↩ Devolver
    </a>
    @endif
    <button class="btn-cerrar" onclick="window.close()">✕ Cerrar</button>
  </div>

  {{-- Ticket --}}
  <div class="ticket">

    {{-- Logo / nombre del negocio --}}
    <div class="ticket-logo">
      <div class="nombre-negocio">PacaManager</div>
      @if($venta->sucursal)
        <div class="sucursal">{{ $venta->sucursal->nombre }}</div>
      @endif
    </div>

    <hr class="divider">

    {{-- Fecha y hora --}}
    <div class="ticket-fecha">
      {{ $venta->created_at->format('d/m/Y') }}
      &nbsp;·&nbsp;
      {{ $venta->created_at->format('H:i') }}
      @if($venta->usuario)
        &nbsp;·&nbsp; {{ $venta->usuario->name }}
      @endif
    </div>

    <div class="ticket-titulo">Comprobante de Venta</div>

    <hr class="divider">

    {{-- Detalles del zapato --}}
    <div class="ticket-fila">
      <span class="label">Categoría</span>
      <span class="valor">{{ $venta->zapato->categoria->nombre ?? '—' }}</span>
    </div>
    @if($venta->zapato->tipo)
    <div class="ticket-fila">
      <span class="label">Tipo</span>
      <span class="valor">{{ $venta->zapato->tipo->nombre }}</span>
    </div>
    @endif
    @if($venta->zapato->talla)
    <div class="ticket-fila">
      <span class="label">Talla</span>
      <span class="valor">{{ $venta->zapato->talla->nombre ?? $venta->zapato->talla }}</span>
    </div>
    @endif
    @if($venta->zapato->color)
    <div class="ticket-fila">
      <span class="label">Color</span>
      <span class="valor">{{ $venta->zapato->color }}</span>
    </div>
    @endif
    <div class="ticket-fila">
      <span class="label">Clasificación</span>
      <span class="valor">{{ $venta->zapato->clasificacion_label }}</span>
    </div>
    @if($venta->notas)
    <div class="ticket-fila">
      <span class="label">Nota</span>
      <span class="valor">{{ $venta->notas }}</span>
    </div>
    @endif

    <hr class="divider">

    {{-- Precio --}}
    <div class="ticket-precio">
      <div class="label-precio">Precio pagado</div>
      <div class="monto">Q{{ number_format($venta->precio_venta, 2) }}</div>
    </div>

    <hr class="divider">

    {{-- Barcode --}}
    <div class="barcode-wrap">
      <svg id="barcode"></svg>
      <div class="barcode-codigo">{{ $venta->zapato->codigo_unico }}</div>
    </div>

    {{-- Nota devolución --}}
    <div class="nota-devolucion">
      <strong>Guarde este comprobante.</strong><br>
      Necesario para devoluciones.<br>
      Válido en cualquier sucursal.
    </div>

  </div>

  {{-- Borde dentado --}}
  <div class="ticket-bottom"></div>

  <script>
    JsBarcode('#barcode', '{{ $venta->zapato->codigo_unico }}', {
      format:      'CODE128',
      width:       1.8,
      height:      55,
      displayValue: false,
      margin:      4,
      lineColor:   '#1a3c5e',
      background:  '#ffffff',
    });
  </script>

</body>
</html>
