@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Proveedores</h6>
        <a href="{{ route('proveedores.create') }}" class="btn btn-primary btn-sm mb-0">
          <i class="fas fa-plus me-1"></i> Nuevo proveedor
        </a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nombre</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Teléfono</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">NIT</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Costales</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($proveedores as $proveedor)
              <tr>
                <td>
                  <div class="px-2 py-1">
                    <h6 class="mb-0 text-sm">{{ $proveedor->nombre }}</h6>
                    <p class="text-xs text-secondary mb-0">{{ $proveedor->direccion }}</p>
                  </div>
                </td>
                <td><p class="text-xs text-secondary mb-0">{{ $proveedor->telefono ?? '—' }}</p></td>
                <td><p class="text-xs text-secondary mb-0">{{ $proveedor->nit ?? '—' }}</p></td>
                <td>
                  <span class="badge badge-sm bg-gradient-info">{{ $proveedor->costales_count }}</span>
                </td>
                <td>
                  <span class="badge badge-sm bg-gradient-{{ $proveedor->activo ? 'success' : 'secondary' }}">
                    {{ $proveedor->activo ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td class="align-middle">
                  <a href="{{ route('proveedores.edit', $proveedor) }}" class="text-secondary font-weight-bold text-xs me-3">
                    <i class="fas fa-edit"></i> Editar
                  </a>
                  @if($proveedor->activo)
                  <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-link p-0 text-danger text-xs"
                            data-confirm="El proveedor '{{ $proveedor->nombre }}' quedará inactivo."
                            data-title="¿Desactivar proveedor?"
                            data-ok="Sí, desactivar"
                            data-icon="warning">
                      <i class="fas fa-power-off"></i> Desactivar
                    </button>
                  </form>
                  @else
                  <form action="{{ route('proveedores.update', $proveedor) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="reactivar" value="1">
                    <button type="button" class="btn btn-link p-0 text-success text-xs"
                            data-confirm="El proveedor '{{ $proveedor->nombre }}' volverá a estar activo."
                            data-title="¿Activar proveedor?"
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
                <td colspan="6" class="text-center py-4 text-secondary text-sm">No hay proveedores registrados.</td>
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
