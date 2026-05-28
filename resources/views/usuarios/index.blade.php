@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Usuarios del sistema</h6>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm mb-0">
          <i class="fas fa-plus me-1"></i> Nuevo usuario
        </a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuario</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Roles</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Sucursal</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Teléfono</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($usuarios as $usuario)
              <tr>
                <td>
                  <div class="d-flex px-2 py-1 align-items-center">
                    <div class="avatar avatar-sm me-3 bg-gradient-primary border-radius-md d-flex align-items-center justify-content-center">
                      <span class="text-white text-xs fw-bold">{{ strtoupper(substr($usuario->name, 0, 2)) }}</span>
                    </div>
                    <div>
                      <h6 class="mb-0 text-sm">{{ $usuario->name }}</h6>
                      <p class="text-xs text-secondary mb-0">{{ $usuario->email }}</p>
                    </div>
                  </div>
                </td>
                <td>
                  @php $rolColors = ['dueno'=>'primary','encargado'=>'info','preparador'=>'secondary']; @endphp
                  <div class="d-flex gap-1 flex-wrap ps-2">
                    @forelse($usuario->roles as $rol)
                      <span class="badge badge-sm bg-gradient-{{ $rolColors[$rol->slug] ?? 'secondary' }}">
                        {{ $rol->nombre }}
                      </span>
                    @empty
                      <span class="text-xs text-secondary">Sin rol</span>
                    @endforelse
                  </div>
                </td>
                <td><p class="text-xs text-secondary mb-0 ps-2">{{ $usuario->sucursal?->nombre ?? '—' }}</p></td>
                <td><p class="text-xs text-secondary mb-0 ps-2">{{ $usuario->phone ?? '—' }}</p></td>
                <td class="align-middle">
                  <a href="{{ route('usuarios.edit', $usuario) }}" class="text-secondary font-weight-bold text-xs me-3">
                    <i class="fas fa-edit"></i> Editar
                  </a>
                  @if($usuario->id !== auth()->id())
                  <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-link p-0 text-danger text-xs"
                            data-confirm="Se eliminará permanentemente al usuario '{{ $usuario->name }}'."
                            data-title="¿Eliminar usuario?"
                            data-ok="Sí, eliminar"
                            data-icon="warning"
                            data-btn-class="btn-danger">
                      <i class="fas fa-trash"></i> Eliminar
                    </button>
                  </form>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-4 text-secondary text-sm">No hay usuarios registrados.</td>
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
