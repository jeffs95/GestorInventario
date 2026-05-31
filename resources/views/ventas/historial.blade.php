@extends('layouts.user_type.auth')

@section('content')

{{-- ══ Stats del período ════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-xl-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-success shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-receipt text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Ventas en período</p>
          <h5 class="mb-0 font-weight-bolder">{{ number_format($stats['total']) }}</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-primary shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-dollar-sign text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Ingresos totales</p>
          <h5 class="mb-0 font-weight-bolder">Q{{ number_format($stats['ingresos'], 2) }}</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-danger shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-tags text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Total rebajado</p>
          <h5 class="mb-0 font-weight-bolder">Q{{ number_format($stats['rebajado'], 2) }}</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-info shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-chart-line text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Promedio por venta</p>
          <h5 class="mb-0 font-weight-bolder">Q{{ number_format($stats['promedio'], 2) }}</h5>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══ Filtros ══════════════════════════════════════════════════════════════ --}}
<div class="card mb-3">
  <div class="card-body py-3 px-3">
    <form method="GET" action="{{ route('ventas.historial') }}" id="formFiltros">
      <div class="row g-2 align-items-end">

        {{-- Rango de fechas --}}
        <div class="col-auto">
          <label class="form-label text-xs fw-bold mb-1">Desde</label>
          <input type="date" name="fecha_desde" class="form-control form-control-sm"
                 value="{{ $desde }}" max="{{ now()->toDateString() }}">
        </div>
        <div class="col-auto">
          <label class="form-label text-xs fw-bold mb-1">Hasta</label>
          <input type="date" name="fecha_hasta" class="form-control form-control-sm"
                 value="{{ $hasta }}" max="{{ now()->toDateString() }}">
        </div>

        {{-- Sucursal (solo dueño) --}}
        @if(auth()->user()->isDueno())
        <div class="col-md-2">
          <label class="form-label text-xs fw-bold mb-1">Sucursal</label>
          <select name="sucursal_id" class="form-select form-select-sm">
            <option value="">Todas</option>
            @foreach($sucursales as $s)
              <option value="{{ $s->id }}" {{ request('sucursal_id') == $s->id ? 'selected':'' }}>
                {{ $s->nombre }}
              </option>
            @endforeach
          </select>
        </div>
        @endif

        {{-- Vendedor --}}
        <div class="col-md-2">
          <label class="form-label text-xs fw-bold mb-1">Vendedor</label>
          <select name="usuario_id" class="form-select form-select-sm">
            <option value="">Todos</option>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected':'' }}>
                {{ $u->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Clasificación --}}
        <div class="col-md-2">
          <label class="form-label text-xs fw-bold mb-1">Clasificación</label>
          <select name="clasificacion" class="form-select form-select-sm">
            <option value="">Todas</option>
            <option value="regular"        {{ request('clasificacion')==='regular'        ?'selected':'' }}>Regular</option>
            <option value="primera_lavado" {{ request('clasificacion')==='primera_lavado' ?'selected':'' }}>Primera (Lavado)</option>
            <option value="primera_lustre" {{ request('clasificacion')==='primera_lustre' ?'selected':'' }}>Primera (Lustre)</option>
          </select>
        </div>

        {{-- Acciones --}}
        <div class="col-auto d-flex gap-2 flex-wrap">
          <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-search me-1"></i>Filtrar
          </button>

          {{-- Accesos rápidos --}}
          <a href="{{ route('ventas.historial', ['fecha_desde' => now()->toDateString(), 'fecha_hasta' => now()->toDateString()]) }}"
             class="btn btn-outline-success btn-sm">
            <i class="fas fa-calendar-day me-1"></i>Hoy
          </a>
          <a href="{{ route('ventas.historial', ['fecha_desde' => now()->subDay()->toDateString(), 'fecha_hasta' => now()->subDay()->toDateString()]) }}"
             class="btn btn-outline-secondary btn-sm">
            Ayer
          </a>
          <a href="{{ route('ventas.historial', ['fecha_desde' => now()->startOfWeek()->toDateString(), 'fecha_hasta' => now()->toDateString()]) }}"
             class="btn btn-outline-secondary btn-sm">
            Esta semana
          </a>
          <a href="{{ route('ventas.historial') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-times me-1"></i>Limpiar
          </a>
          <a href="{{ request()->fullUrlWithQuery(['exportar' => 'csv']) }}"
             class="btn btn-outline-success btn-sm ms-1">
            <i class="fas fa-file-csv me-1"></i>Exportar CSV
          </a>
        </div>

      </div>
    </form>
  </div>
</div>

{{-- ══ Cuaderno de ventas — agrupado por día ════════════════════════════════ --}}

@if($ventasPorDia->isEmpty())

  <div class="card text-center py-5">
    <div class="card-body">
      <i class="fas fa-book-open fa-2x text-secondary opacity-3 mb-3 d-block"></i>
      <p class="text-secondary text-sm mb-1">No hay ventas registradas en el período seleccionado.</p>
      <a href="{{ route('ventas.index') }}" class="btn btn-primary btn-sm mt-2">
        <i class="fas fa-cash-register me-1"></i>Ir al punto de venta
      </a>
    </div>
  </div>

@else

@foreach($ventasPorDia as $fecha => $ventasDia)
@php
  $dt          = \Carbon\Carbon::parse($fecha);
  $esHoy       = $dt->isToday();
  $esAyer      = $dt->isYesterday();
  $totalDia    = $ventasDia->sum('precio_venta');
  $rebajaDia   = $ventasDia->sum(fn($v) => max(0, (float)$v->precio_lista - (float)$v->precio_venta));
  $cantidadDia = $ventasDia->count();

  // Etiqueta del día
  $dias    = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
  $meses   = ['','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
  $label   = $esHoy  ? 'Hoy — ' :
             ($esAyer ? 'Ayer — ' : '');
  $label  .= ucfirst($dias[$dt->dayOfWeek]) . ' ' . $dt->day . ' de ' . $meses[$dt->month] . ' ' . $dt->year;
@endphp

<div class="card mb-3 {{ $esHoy ? 'border-2' : '' }}"
     style="{{ $esHoy ? 'border-color:#0ea5e9 !important' : '' }}">

  {{-- ── Cabecera del día ──────────────────────────────────────────────── --}}
  <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2"
       style="{{ $esHoy ? 'background:linear-gradient(135deg,#e0f2fe,#f0f9ff)' : 'background:#f8f9fa' }};border-radius:1rem 1rem 0 0">

    <div class="d-flex align-items-center gap-3">
      {{-- Ícono calendario --}}
      <div class="icon icon-shape icon-sm shadow border-radius-md d-flex align-items-center justify-content-center flex-shrink-0"
           style="background:{{ $esHoy ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.12)' }}">
        <i class="fas fa-calendar-day"
           style="font-size:.7rem;color:{{ $esHoy ? '#fff' : '#344767' }}"></i>
      </div>
      <div>
        <h6 class="mb-0 {{ $esHoy ? 'text-primary' : 'text-dark' }}" style="font-size:.95rem">
          {{ $label }}
        </h6>
        <p class="text-xxs text-secondary mb-0">
          {{ $cantidadDia }} {{ $cantidadDia === 1 ? 'venta' : 'ventas' }}
          @if($rebajaDia > 0)
            · <span class="text-danger">Q{{ number_format($rebajaDia, 2) }} rebajado</span>
          @endif
        </p>
      </div>
    </div>

    {{-- Total del día --}}
    <div class="text-end">
      <p class="text-xxs text-secondary mb-0">Ingresos del día</p>
      <h5 class="mb-0 font-weight-bolder text-success">Q{{ number_format($totalDia, 2) }}</h5>
    </div>

  </div>

  {{-- ── Tabla de ventas del día ──────────────────────────────────────── --}}
  <div class="card-body px-0 pt-0 pb-0">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead style="background:#fafafa">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">#</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Hora</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Descripción</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Precio lista</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Vendido a</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dif.</th>
            @if(auth()->user()->isDueno())
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Vendedor</th>
            @endif
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Notas</th>
            <th class="text-secondary opacity-7"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($ventasDia->sortByDesc('created_at') as $i => $venta)
          @php
            $dif        = (float)$venta->precio_venta - (float)$venta->precio_lista;
            $devuelta   = $venta->devolucion !== null;
          @endphp
          <tr class="{{ $devuelta ? 'opacity-6' : '' }}">
            <td class="ps-3">
              <span class="text-xs text-secondary">{{ $cantidadDia - $i }}</span>
            </td>
            <td class="ps-2">
              <span class="text-xs text-secondary font-monospace">
                {{ $venta->created_at->format('H:i') }}
              </span>
            </td>
            <td class="ps-2">
              <span class="text-xs font-weight-bold font-monospace text-primary">
                {{ $venta->zapato->codigo_unico ?? '—' }}
              </span>
            </td>
            <td class="ps-2">
              <p class="text-xs font-weight-bold mb-0">
                {{ $venta->zapato->categoria->nombre ?? '—' }}
                @if($venta->zapato?->talla)
                  · {{ $venta->zapato->talla->nombre ?? $venta->zapato->talla }}
                @endif
              </p>
              <p class="text-xxs text-secondary mb-0">
                {{ $venta->zapato->tipo->nombre ?? '' }}
                @if($venta->zapato?->color)
                  · {{ $venta->zapato->color }}
                @endif
              </p>
            </td>
            <td class="ps-2">
              <span class="text-xs text-secondary">Q{{ number_format($venta->precio_lista, 2) }}</span>
            </td>
            <td class="ps-2">
              <span class="text-sm font-weight-bolder text-success">
                Q{{ number_format($venta->precio_venta, 2) }}
              </span>
            </td>
            <td class="ps-2">
              @if(abs($dif) < 0.01)
                <span class="text-xs text-secondary">—</span>
              @elseif($dif > 0)
                <span class="text-xs font-weight-bold text-success">
                  +Q{{ number_format($dif, 2) }}
                </span>
              @else
                <span class="text-xs font-weight-bold text-danger">
                  -Q{{ number_format(abs($dif), 2) }}
                </span>
              @endif
            </td>
            @if(auth()->user()->isDueno())
            <td class="ps-2">
              <span class="text-xs text-secondary">{{ $venta->usuario->name ?? '—' }}</span>
            </td>
            @endif
            <td class="ps-2">
              <span class="text-xs text-secondary">{{ $venta->notas ?? '—' }}</span>
            </td>
            <td class="pe-3 text-end">
              @if($devuelta)
                <span class="badge badge-sm bg-gradient-secondary">
                  <i class="fas fa-undo me-1"></i>Devuelta
                </span>
              @else
                <a href="{{ route('devoluciones.create', ['venta_id' => $venta->id]) }}"
                   class="btn btn-link p-0 text-danger text-xs"
                   title="Registrar devolución">
                  <i class="fas fa-undo me-1"></i>Devolver
                </a>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- ── Pie del día: subtotales ──────────────────────────────────────── --}}
    <div class="px-4 py-2 d-flex gap-4 flex-wrap"
         style="background:#f8f9fa;border-top:1px solid #f0f0f0;border-radius:0 0 1rem 1rem">
      <span class="text-xs text-secondary">
        <i class="fas fa-receipt me-1 text-success"></i>
        {{ $cantidadDia }} {{ $cantidadDia === 1 ? 'venta' : 'ventas' }}
      </span>
      <span class="text-xs font-weight-bold text-success">
        <i class="fas fa-dollar-sign me-1"></i>
        Q{{ number_format($totalDia, 2) }}
      </span>
      @if($rebajaDia > 0)
      <span class="text-xs font-weight-bold text-danger">
        <i class="fas fa-arrow-down me-1"></i>
        Rebajado: Q{{ number_format($rebajaDia, 2) }}
      </span>
      @endif
      @if($cantidadDia > 0)
      <span class="text-xs text-secondary">
        Promedio: Q{{ number_format($totalDia / $cantidadDia, 2) }}
      </span>
      @endif
    </div>

  </div>
</div>

@endforeach

@endif

{{-- Botón volver al POS --}}
<div class="mt-2 mb-4">
  <a href="{{ route('ventas.index') }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-arrow-left me-1"></i>Volver al punto de venta
  </a>
</div>

@endsection

@push('styles')
<style>
  .card.border-2 { border-width: 2px !important; }
  .table thead th { border-top: none; }
  .table tbody tr:last-child td { border-bottom: none; }
</style>
@endpush
