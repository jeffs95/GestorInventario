@extends('layouts.user_type.auth')

@section('content')

{{-- ══════════════════════════════════════════════════════════════════════
     FILA 1 — Tarjetas de estadísticas
══════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">

  {{-- Zapatos en inventario --}}
  <div class="col-xl-3 col-sm-6">
    <div class="card">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="text-sm mb-1 text-secondary font-weight-bold">Zapatos en inventario</p>
            <h4 class="font-weight-bolder mb-0 text-dark">{{ number_format($stats['zapatos_inventario']) }}</h4>
          </div>
          <div class="icon icon-shape icon-lg bg-gradient-success shadow text-center border-radius-md">
            <i class="fas fa-shoe-prints text-white opacity-10 fs-5"></i>
          </div>
        </div>
        <hr class="horizontal dark my-2">
        <a href="{{ route('inventario.index') }}" class="text-xs text-success font-weight-bold">
          Ver inventario <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- Lotes de compra --}}
  <div class="col-xl-3 col-sm-6">
    <div class="card">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="text-sm mb-1 text-secondary font-weight-bold">Lotes de compra</p>
            <h4 class="font-weight-bolder mb-0 text-dark">{{ number_format($stats['lotes_total']) }}</h4>
          </div>
          <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-md">
            <i class="fas fa-boxes text-white opacity-10 fs-5"></i>
          </div>
        </div>
        <hr class="horizontal dark my-2">
        <a href="{{ route('lotes.index') }}" class="text-xs text-primary font-weight-bold">
          Ver lotes <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- Costales pendientes --}}
  <div class="col-xl-3 col-sm-6">
    <div class="card">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="text-sm mb-1 text-secondary font-weight-bold">Costales por abrir</p>
            <h4 class="font-weight-bolder mb-0 text-dark">{{ number_format($stats['costales_pendientes']) }}</h4>
          </div>
          <div class="icon icon-shape icon-lg bg-gradient-warning shadow text-center border-radius-md">
            <i class="fas fa-archive text-white opacity-10 fs-5"></i>
          </div>
        </div>
        <hr class="horizontal dark my-2">
        <a href="{{ route('costales.index') }}" class="text-xs text-warning font-weight-bold">
          Ver costales <i class="fas fa-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

  {{-- En preparación --}}
  <div class="col-xl-3 col-sm-6">
    <div class="card">
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <p class="text-sm mb-1 text-secondary font-weight-bold">Lotes en preparación</p>
            <h4 class="font-weight-bolder mb-0 text-dark">{{ number_format($stats['en_preparacion']) }}</h4>
          </div>
          <div class="icon icon-shape icon-lg bg-gradient-info shadow text-center border-radius-md">
            <i class="fas fa-soap text-white opacity-10 fs-5"></i>
          </div>
        </div>
        <hr class="horizontal dark my-2">
        <span class="text-xs text-secondary">Primera sin registrar uno a uno</span>
      </div>
    </div>
  </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════════
     FILA 2 — Accesos rápidos
══════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Accesos rápidos</h6>
      </div>
      <div class="card-body pt-3">
        <div class="row g-3">

          <div class="col-lg-3 col-sm-6">
            <a href="{{ route('inventario.index') }}"
               class="btn btn-outline-success w-100 d-flex align-items-center gap-2 py-3">
              <i class="fas fa-shoe-prints fs-5"></i>
              <span class="text-start">
                <strong class="d-block">Ver Inventario</strong>
                <small class="text-secondary">Todos los zapatos</small>
              </span>
            </a>
          </div>

          <div class="col-lg-3 col-sm-6">
            <a href="{{ route('lotes.index') }}"
               class="btn btn-outline-primary w-100 d-flex align-items-center gap-2 py-3">
              <i class="fas fa-boxes fs-5"></i>
              <span class="text-start">
                <strong class="d-block">Lotes de compra</strong>
                <small class="text-secondary">Ver todos los lotes</small>
              </span>
            </a>
          </div>

          <div class="col-lg-3 col-sm-6">
            <a href="{{ route('aperturas.create') }}"
               class="btn btn-outline-warning w-100 d-flex align-items-center gap-2 py-3">
              <i class="fas fa-box-open fs-5"></i>
              <span class="text-start">
                <strong class="d-block">Abrir costal</strong>
                <small class="text-secondary">Nueva apertura</small>
              </span>
            </a>
          </div>

          <div class="col-lg-3 col-sm-6">
            <a href="{{ route('costales.index') }}"
               class="btn btn-outline-info w-100 d-flex align-items-center gap-2 py-3">
              <i class="fas fa-archive fs-5"></i>
              <span class="text-start">
                <strong class="d-block">Costales</strong>
                <small class="text-secondary">Ver todos</small>
              </span>
            </a>
          </div>

          @if(auth()->user()->hasRole('dueno'))
          <div class="col-lg-3 col-sm-6">
            <a href="{{ route('lotes.create') }}"
               class="btn btn-outline-dark w-100 d-flex align-items-center gap-2 py-3">
              <i class="fas fa-plus-circle fs-5"></i>
              <span class="text-start">
                <strong class="d-block">Nuevo lote</strong>
                <small class="text-secondary">Registrar compra</small>
              </span>
            </a>
          </div>

          <div class="col-lg-3 col-sm-6">
            <a href="{{ route('configuracion.index') }}"
               class="btn btn-outline-secondary w-100 d-flex align-items-center gap-2 py-3">
              <i class="fas fa-sliders-h fs-5"></i>
              <span class="text-start">
                <strong class="d-block">Configuración</strong>
                <small class="text-secondary">Categorías, tipos, tallas</small>
              </span>
            </a>
          </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     FILA 3 — Últimos lotes + Últimas aperturas
══════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3">

  {{-- Últimos lotes --}}
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Últimos lotes</h6>
        <a href="{{ route('lotes.index') }}" class="text-xs text-primary">Ver todos</a>
      </div>
      <div class="card-body px-0 pt-0 pb-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Lote</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Proveedor</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costales</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costo</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ultimosLotes as $lote)
              <tr>
                <td class="ps-3">
                  <a href="{{ route('lotes.show', $lote) }}"
                     class="text-xs font-weight-bold text-primary font-monospace">
                    {{ $lote->numero_lote }}
                  </a>
                  <p class="text-xxs text-secondary mb-0">{{ $lote->fecha ? \Carbon\Carbon::parse($lote->fecha)->format('d/m/Y') : '—' }}</p>
                </td>
                <td class="ps-2">
                  <span class="text-xs">{{ $lote->proveedor->nombre ?? '—' }}</span>
                </td>
                <td class="ps-2">
                  <span class="badge badge-sm bg-gradient-primary">{{ $lote->total_costales }}</span>
                </td>
                <td class="ps-2">
                  <span class="text-xs font-weight-bold text-dark">Q{{ number_format($lote->costo_total, 2) }}</span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-secondary text-sm">
                  <i class="fas fa-inbox me-1"></i> Sin lotes registrados aún.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Últimas aperturas --}}
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-box-open me-2 text-warning"></i>Últimas aperturas</h6>
        <a href="{{ route('aperturas.create') }}" class="btn btn-sm btn-primary py-1 px-2 mb-0">
          <i class="fas fa-plus me-1"></i>Nueva
        </a>
      </div>
      <div class="card-body px-0 pt-0 pb-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Apertura</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Lote</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Fecha</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ultimasAperturas as $apertura)
              <tr>
                <td class="ps-3">
                  <a href="{{ route('aperturas.show', $apertura) }}"
                     class="text-xs font-weight-bold text-warning">
                    #{{ $apertura->id }}
                  </a>
                </td>
                <td class="ps-2">
                  <span class="text-xs">{{ $apertura->lote->numero_lote ?? '—' }}</span>
                  <p class="text-xxs text-secondary mb-0">{{ $apertura->lote->proveedor->nombre ?? '' }}</p>
                </td>
                <td class="ps-2">
                  @php
                    $color = match($apertura->estado ?? 'pendiente') {
                      'clasificada' => 'success',
                      'en_proceso'  => 'warning',
                      default       => 'secondary',
                    };
                  @endphp
                  <span class="badge badge-sm bg-gradient-{{ $color }}">
                    {{ ucfirst(str_replace('_', ' ', $apertura->estado ?? 'pendiente')) }}
                  </span>
                </td>
                <td class="ps-2">
                  <span class="text-xs text-secondary">
                    {{ $apertura->created_at ? $apertura->created_at->format('d/m/Y') : '—' }}
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-secondary text-sm">
                  <i class="fas fa-inbox me-1"></i> Sin aperturas registradas aún.
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
