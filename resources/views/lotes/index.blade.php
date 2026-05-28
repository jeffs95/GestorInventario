@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">

    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Lotes de compra</h6>
        @if(auth()->user()->isDueno())
        <a href="{{ route('lotes.create') }}" class="btn btn-primary btn-sm mb-0">
          <i class="fas fa-plus me-1"></i> Nuevo Lote
        </a>
        @endif
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lote</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Fecha</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Proveedor</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sucursal</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costales</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Peso total</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costo total</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($lotes as $lote)
              <tr>
                <td>
                  <p class="text-xs font-weight-bold mb-0 px-3 font-monospace">{{ $lote->numero_lote }}</p>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0 ps-2">{{ $lote->fecha_compra->format('d/m/Y') }}</p>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0 ps-2">{{ $lote->proveedor->nombre }}</p>
                </td>
                <td>
                  <p class="text-xs text-secondary mb-0 ps-2">{{ $lote->sucursalDestino->nombre }}</p>
                </td>
                <td>
                  <span class="badge badge-sm bg-gradient-info ps-2">{{ $lote->costales_count }}</span>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0 ps-2">{{ number_format($lote->peso_total, 2) }} lbs</p>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0 ps-2 text-primary">Q{{ number_format($lote->costo_total, 2) }}</p>
                </td>
                <td>
                  @if($lote->estado === 'activo')
                    <span class="badge badge-sm bg-gradient-success">Activo</span>
                  @else
                    <span class="badge badge-sm bg-gradient-secondary">Cerrado</span>
                  @endif
                </td>
                <td class="align-middle">
                  <a href="{{ route('lotes.show', $lote) }}" class="btn btn-link p-0 text-info text-xs">
                    <i class="fas fa-eye"></i> Ver
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center py-4 text-secondary text-sm">No hay lotes registrados.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">
          {{ $lotes->links() }}
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
