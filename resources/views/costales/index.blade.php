@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Compras de Costales</h6>
        @if(auth()->user()->isDueno())
        <a href="{{ route('costales.create') }}" class="btn btn-primary btn-sm mb-0">
          <i class="fas fa-plus me-1"></i> Registrar costal
        </a>
        @endif
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Proveedor</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sucursal</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Peso (lbs)</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Q/lb</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costo total</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Zapatos</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Fecha</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($costales as $costal)
              <tr>
                <td><p class="text-xs font-weight-bold mb-0 px-3">{{ $costal->numero_costal ?? '#'.$costal->id }}</p></td>
                <td><p class="text-xs text-secondary mb-0 ps-2">{{ $costal->proveedor->nombre }}</p></td>
                <td><p class="text-xs text-secondary mb-0 ps-2">{{ $costal->sucursalDestino->nombre }}</p></td>
                <td><p class="text-xs font-weight-bold mb-0 ps-2">{{ number_format($costal->peso_libras, 2) }}</p></td>
                <td><p class="text-xs mb-0 ps-2">Q{{ number_format($costal->precio_por_libra, 2) }}</p></td>
                <td><p class="text-xs font-weight-bold mb-0 ps-2 text-primary">Q{{ number_format($costal->costo_total, 2) }}</p></td>
                <td>
                  <span class="badge badge-sm bg-gradient-info ps-2">{{ $costal->zapatos_count }}</span>
                </td>
                <td>
                  <span class="badge badge-sm bg-gradient-{{ $costal->estado_color }}">
                    {{ $costal->estado_label }}
                  </span>
                </td>
                <td><p class="text-xs text-secondary mb-0 ps-2">{{ $costal->fecha_compra->format('d/m/Y') }}</p></td>
                <td class="align-middle">
                  <a href="{{ route('costales.show', $costal) }}" class="btn btn-link p-0 text-info text-xs">
                    <i class="fas fa-eye"></i> Ver
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="10" class="text-center py-4 text-secondary text-sm">No hay costales registrados.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 pt-3">
          {{ $costales->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
