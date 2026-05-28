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
