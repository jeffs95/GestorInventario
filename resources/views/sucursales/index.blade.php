@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Sucursales</h6>
        <a href="{{ route('sucursales.create') }}" class="btn btn-primary btn-sm mb-0">
          <i class="fas fa-plus me-1"></i> Nueva sucursal
        </a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sucursal</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dirección</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Teléfono</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Encargado</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($sucursales as $sucursal)
              <tr>
                <td>
                  <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-0 text-sm">{{ $sucursal->nombre }}</h6>
                    </div>
                  </div>
                </td>
                <td><p class="text-xs text-secondary mb-0">{{ $sucursal->direccion ?? '—' }}</p></td>
                <td><p class="text-xs text-secondary mb-0">{{ $sucursal->telefono ?? '—' }}</p></td>
                <td><p class="text-xs text-secondary mb-0">{{ $sucursal->encargado?->name ?? '—' }}</p></td>
                <td>
                  <span class="badge badge-sm bg-gradient-{{ $sucursal->activo ? 'success' : 'secondary' }}">
                    {{ $sucursal->activo ? 'Activa' : 'Inactiva' }}
                  </span>
                </td>
                <td class="align-middle">
                  <a href="{{ route('sucursales.edit', $sucursal) }}" class="text-secondary font-weight-bold text-xs me-3">
                    <i class="fas fa-edit"></i> Editar
                  </a>
                  @if($sucursal->activo)
                  <form action="{{ route('sucursales.destroy', $sucursal) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-link p-0 text-danger text-xs"
                            data-confirm="La sucursal '{{ $sucursal->nombre }}' quedará inactiva."
                            data-title="¿Desactivar sucursal?"
                            data-ok="Sí, desactivar"
                            data-icon="warning">
                      <i class="fas fa-power-off"></i> Desactivar
                    </button>
                  </form>
                  @else
                  <form action="{{ route('sucursales.update', $sucursal) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="reactivar" value="1">
                    <button type="button" class="btn btn-link p-0 text-success text-xs"
                            data-confirm="La sucursal '{{ $sucursal->nombre }}' volverá a estar activa."
                            data-title="¿Activar sucursal?"
                            data-ok="Sí, activar"
                            data-btn-class="btn-success"
                            data-icon="question">
                      <i class="fas fa-check-circle"></i> Activar
                    </button>
                  </form>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-secondary text-sm">
                  No hay sucursales registradas aún.
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
