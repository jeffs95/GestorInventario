@extends('layouts.user_type.auth')

@section('content')
<div class="row">

  {{-- Header del lote --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-body py-3">
        <div class="row align-items-center g-3">

          <div class="col-auto">
            <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-md">
              <i class="fas fa-layer-group text-white opacity-10 text-xl"></i>
            </div>
          </div>

          <div class="col">
            <h5 class="mb-0 font-monospace">{{ $lote->numero_lote }}</h5>
            <p class="mb-0 text-sm text-secondary">
              <strong>Proveedor:</strong> {{ $lote->proveedor->nombre }} &nbsp;|&nbsp;
              <strong>Sucursal:</strong> {{ $lote->sucursalDestino->nombre }} &nbsp;|&nbsp;
              <strong>Fecha:</strong> {{ $lote->fecha_compra->format('d/m/Y') }}
            </p>
          </div>

          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Precio predeterminado</p>
            <h6 class="mb-0">Q{{ number_format($lote->precio_por_libra, 2) }}/lb</h6>
          </div>

          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Total libras</p>
            <h6 class="mb-0">{{ number_format($lote->peso_total, 2) }} lbs</h6>
          </div>

          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Costo total</p>
            <h4 class="mb-0 text-primary">Q{{ number_format($lote->costo_total, 2) }}</h4>
          </div>

          <div class="col-auto">
            @if($lote->estado === 'activo')
              <span class="badge badge-sm bg-gradient-success fs-6">Activo</span>
            @else
              <span class="badge badge-sm bg-gradient-secondary fs-6">Cerrado</span>
            @endif
          </div>

        </div>

        @if($lote->notas)
        <div class="mt-2 pt-2 border-top">
          <p class="text-xs text-secondary mb-0"><strong>Notas:</strong> {{ $lote->notas }}</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- ── Análisis de Rentabilidad ────────────────────────────────────── --}}
  @php
    $pctBar   = min($rentabilidad['pct_recuperado'], 100);
    $barClass = $pctBar < 30 ? 'bg-gradient-danger'
              : ($pctBar < 60 ? 'bg-gradient-warning'
              : ($pctBar < 100 ? 'bg-gradient-info'
              : 'bg-gradient-success'));
  @endphp
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0">
          <i class="fas fa-chart-line me-2 text-success"></i>
          Análisis de Rentabilidad
        </h6>
        <span class="badge badge-sm
          {{ $rentabilidad['pct_recuperado'] >= 100 ? 'bg-gradient-success'
            : ($rentabilidad['pct_recuperado'] >= 60  ? 'bg-gradient-info'
            : ($rentabilidad['pct_recuperado'] >= 30  ? 'bg-gradient-warning'
            : 'bg-gradient-danger')) }}
          fs-6 px-3 py-2">
          {{ number_format($rentabilidad['pct_recuperado'], 1) }}% del costo recuperado
        </span>
      </div>

      <div class="card-body pt-2">

        {{-- Barra de progreso de costo recuperado --}}
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-xs text-secondary">Costo recuperado via ventas</span>
            <span class="text-xs font-weight-bold">
              Q{{ number_format($rentabilidad['ingresos_ventas'], 2) }}
              <span class="text-secondary">/ Q{{ number_format($rentabilidad['costo_total'], 2) }}</span>
            </span>
          </div>
          <div class="progress" style="height:10px;border-radius:8px;background:#e9ecef">
            <div class="progress-bar {{ $barClass }}"
                 role="progressbar"
                 style="width:{{ $pctBar }}%;border-radius:8px;transition:width .6s ease"
                 aria-valuenow="{{ $pctBar }}" aria-valuemin="0" aria-valuemax="100">
            </div>
          </div>
          @if($rentabilidad['pct_recuperado'] > 100)
            <small class="text-success mt-1 d-block">
              <i class="fas fa-check-circle me-1"></i>
              ¡Costo totalmente recuperado! Excedente de Q{{ number_format($rentabilidad['ingresos_ventas'] - $rentabilidad['costo_total'], 2) }}
            </small>
          @endif
        </div>

        {{-- 4 tarjetas de métricas --}}
        <div class="row g-3 mb-3">

          <div class="col-6 col-md-3">
            <div class="p-3 border border-radius-lg text-center h-100" style="background:rgba(236,242,255,.6)">
              <p class="text-xs text-secondary mb-1"><i class="fas fa-coins me-1"></i>Costo invertido</p>
              <h5 class="mb-0 text-primary">Q{{ number_format($rentabilidad['costo_total'], 2) }}</h5>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="p-3 border border-radius-lg text-center h-100" style="background:rgba(230,255,240,.6)">
              <p class="text-xs text-secondary mb-1"><i class="fas fa-cash-register me-1"></i>Ingresos por ventas</p>
              <h5 class="mb-0 text-success">Q{{ number_format($rentabilidad['ingresos_ventas'], 2) }}</h5>
              <small class="text-secondary">{{ $rentabilidad['vendidos'] }} {{ $rentabilidad['vendidos'] == 1 ? 'zapato' : 'zapatos' }}</small>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="p-3 border border-radius-lg text-center h-100" style="background:rgba(255,250,230,.6)">
              <p class="text-xs text-secondary mb-1"><i class="fas fa-boxes me-1"></i>Valor en inventario</p>
              <h5 class="mb-0 text-warning">Q{{ number_format($rentabilidad['valor_inventario'], 2) }}</h5>
              <small class="text-secondary">{{ $rentabilidad['en_inventario'] }} disponibles</small>
            </div>
          </div>

          <div class="col-6 col-md-3">
            <div class="p-3 border border-radius-lg text-center h-100"
                 style="background:{{ $rentabilidad['ganancia_real'] >= 0 ? 'rgba(230,255,240,.6)' : 'rgba(255,235,235,.6)' }}">
              <p class="text-xs text-secondary mb-1"><i class="fas fa-chart-bar me-1"></i>Ganancia real</p>
              <h5 class="mb-0 {{ $rentabilidad['ganancia_real'] >= 0 ? 'text-success' : 'text-danger' }}">
                {{ $rentabilidad['ganancia_real'] >= 0 ? '+' : '' }}Q{{ number_format($rentabilidad['ganancia_real'], 2) }}
              </h5>
              <small class="{{ $rentabilidad['ganancia_real'] >= 0 ? 'text-success' : 'text-danger' }} opacity-8">
                {{ $rentabilidad['ganancia_real'] >= 0 ? 'utilidad' : 'pérdida' }} hasta ahora
              </small>
            </div>
          </div>

        </div>

        {{-- Ganancia potencial + conteo de estados --}}
        <div class="row g-2 align-items-center">

          <div class="col-md-6">
            <div class="p-3 border-radius-lg d-flex align-items-center gap-3"
                 style="background:rgba(242,246,255,.8);border:1px dashed #adb5bd">
              <i class="fas fa-lightbulb fa-lg text-info flex-shrink-0"></i>
              <div>
                <p class="text-xs text-secondary mb-0">
                  Ganancia potencial
                  <span class="opacity-6">(si se vende todo el inventario al precio lista)</span>
                </p>
                <h5 class="mb-0 {{ $rentabilidad['ganancia_potencial'] >= 0 ? 'text-info' : 'text-danger' }}">
                  {{ $rentabilidad['ganancia_potencial'] >= 0 ? '+' : '' }}Q{{ number_format($rentabilidad['ganancia_potencial'], 2) }}
                </h5>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="row g-2 text-center">
              <div class="col-3">
                <div class="p-2 border-radius-md" style="background:#f8f9fa">
                  <p class="text-xs text-secondary mb-0">Total</p>
                  <h5 class="mb-0">{{ $rentabilidad['total_zapatos'] }}</h5>
                </div>
              </div>
              <div class="col-3">
                <div class="p-2 border-radius-md" style="background:rgba(230,255,240,.5)">
                  <p class="text-xs text-secondary mb-0">Vendidos</p>
                  <h5 class="mb-0 text-success">{{ $rentabilidad['vendidos'] }}</h5>
                </div>
              </div>
              <div class="col-3">
                <div class="p-2 border-radius-md" style="background:rgba(236,242,255,.5)">
                  <p class="text-xs text-secondary mb-0">Disponibles</p>
                  <h5 class="mb-0 text-primary">{{ $rentabilidad['en_inventario'] }}</h5>
                </div>
              </div>
              <div class="col-3">
                <div class="p-2 border-radius-md" style="background:rgba(255,250,230,.5)">
                  <p class="text-xs text-secondary mb-0">Sin precio</p>
                  <h5 class="mb-0 text-warning">{{ $rentabilidad['sin_precio'] }}</h5>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>

  {{-- Aperturas de este lote --}}
  @if($lote->aperturas->count())
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">
          <i class="fas fa-box-open me-2 text-warning"></i>
          Aperturas de clasificación ({{ $lote->aperturas->count() }})
        </h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Apertura</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Fecha</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costales</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Zapatos</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($lote->aperturas as $ap)
              <tr>
                <td><p class="text-xs font-weight-bold mb-0 px-3">Apertura #{{ $ap->id }}</p></td>
                <td><p class="text-xs text-secondary mb-0 ps-2">{{ $ap->fecha->format('d/m/Y') }}</p></td>
                <td><span class="badge badge-sm bg-gradient-secondary ps-2">{{ $ap->costales_count ?? $ap->costales->count() }}</span></td>
                <td><span class="badge badge-sm bg-gradient-info ps-2">{{ $ap->zapatos_count ?? $ap->zapatos->count() }}</span></td>
                <td>
                  @if($ap->estado === 'abierta')
                    <span class="badge badge-sm bg-gradient-warning">Abierta</span>
                  @else
                    <span class="badge badge-sm bg-gradient-success">Clasificada</span>
                  @endif
                </td>
                <td class="align-middle">
                  <a href="{{ route('aperturas.show', $ap) }}" class="btn btn-link p-0 text-info text-xs">
                    <i class="fas fa-eye"></i> Ver
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- Tabla de costales del lote --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0">Costales en este lote ({{ $lote->costales->count() }})</h6>
        <div class="d-flex gap-2">
          @php $costalesDisponibles = $lote->costales->where('estado','recibido')->count(); @endphp
          @if($costalesDisponibles > 0)
          <a href="{{ route('aperturas.create', ['lote_id' => $lote->id]) }}"
             class="btn btn-warning btn-sm mb-0">
            <i class="fas fa-box-open me-1"></i> Abrir costales para clasificar
            <span class="badge bg-white text-dark ms-1">{{ $costalesDisponibles }}</span>
          </a>
          @endif
          <a href="{{ route('costales.index') }}" class="btn btn-outline-secondary btn-sm mb-0">
            <i class="fas fa-shopping-bag me-1"></i> Ver todos los costales
          </a>
        </div>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Costal</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Peso (lbs)</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Q/lb</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costo</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Zapatos clasif.</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($lote->costales as $costal)
              <tr>
                <td>
                  <p class="text-xs font-weight-bold mb-0 px-3 font-monospace">{{ $costal->numero_costal ?? '#'.$costal->id }}</p>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0 ps-2">{{ number_format($costal->peso_libras, 2) }}</p>
                </td>
                <td>
                  <p class="text-xs mb-0 ps-2">Q{{ number_format($costal->precio_por_libra, 2) }}</p>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0 ps-2 text-primary">Q{{ number_format($costal->costo_total, 2) }}</p>
                </td>
                <td>
                  <span class="badge badge-sm bg-gradient-info ps-2">{{ $costal->zapatos->count() }}</span>
                </td>
                <td>
                  <span class="badge badge-sm bg-gradient-{{ $costal->estado_color }}">
                    {{ $costal->estado_label }}
                  </span>
                </td>
                <td class="align-middle">
                  <a href="{{ route('costales.show', $costal) }}" class="btn btn-link p-0 text-info text-xs">
                    <i class="fas fa-eye"></i> Ver costal
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-secondary text-sm">Este lote no tiene costales registrados.</td>
              </tr>
              @endforelse
            </tbody>

            @if($lote->costales->count() > 0)
            <tfoot>
              <tr class="bg-gray-100">
                <td class="px-3 py-2">
                  <p class="text-xs font-weight-bolder mb-0">Totales</p>
                </td>
                <td class="ps-2 py-2">
                  <p class="text-xs font-weight-bolder mb-0">{{ number_format($lote->peso_total, 2) }} lbs</p>
                </td>
                <td class="py-2"></td>
                <td class="ps-2 py-2">
                  <p class="text-xs font-weight-bolder mb-0 text-primary">Q{{ number_format($lote->costo_total, 2) }}</p>
                </td>
                <td class="ps-2 py-2">
                  <span class="badge badge-sm bg-gradient-dark">{{ $lote->costales->sum(fn($c) => $c->zapatos->count()) }}</span>
                </td>
                <td colspan="2"></td>
              </tr>
            </tfoot>
            @endif
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
