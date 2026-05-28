@extends('layouts.user_type.auth')

@section('content')
<div class="row">

  {{-- Categorías --}}
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6 class="mb-0">Categorías de zapato</h6>
        <p class="text-xs text-secondary mb-0">Ej: Hombre, Mujer, Niño/a</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('configuracion.storeCategoria') }}" class="d-flex gap-2 mb-3">
          @csrf
          <input type="text" name="nombre" class="form-control form-control-sm"
                 placeholder="Nueva categoría..." required maxlength="50">
          <button type="submit" class="btn btn-primary btn-sm text-nowrap">
            <i class="fas fa-plus"></i> Agregar
          </button>
        </form>

        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <tbody>
              @forelse($categorias as $cat)
              <tr>
                <td><p class="text-sm font-weight-bold mb-0">{{ $cat->nombre }}</p></td>
                <td class="text-end">
                  <form action="{{ route('configuracion.destroyCategoria', $cat) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-link p-0 text-danger text-xs"
                            data-confirm="Se eliminará la categoría '{{ $cat->nombre }}'."
                            data-title="¿Eliminar categoría?"
                            data-ok="Sí, eliminar"
                            data-icon="question">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td class="text-secondary text-sm text-center py-3">Sin categorías registradas.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Tipos de zapato --}}
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6 class="mb-0">Tipos de zapato</h6>
        <p class="text-xs text-secondary mb-0">Ej: Tenis, Bota, Sandalia, Escolar, Formal</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('configuracion.storeTipo') }}" class="d-flex gap-2 mb-3">
          @csrf
          <input type="text" name="nombre" class="form-control form-control-sm"
                 placeholder="Nuevo tipo..." required maxlength="50">
          <button type="submit" class="btn btn-primary btn-sm text-nowrap">
            <i class="fas fa-plus"></i> Agregar
          </button>
        </form>

        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <tbody>
              @forelse($tipos as $tipo)
              <tr>
                <td><p class="text-sm font-weight-bold mb-0">{{ $tipo->nombre }}</p></td>
                <td class="text-end">
                  <form action="{{ route('configuracion.destroyTipo', $tipo) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-link p-0 text-danger text-xs"
                            data-confirm="Se eliminará el tipo '{{ $tipo->nombre }}'."
                            data-title="¿Eliminar tipo de zapato?"
                            data-ok="Sí, eliminar"
                            data-icon="question">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td class="text-secondary text-sm text-center py-3">Sin tipos registrados.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  {{-- Tallas --}}
  <div class="col-lg-4 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6 class="mb-0">Tallas</h6>
        <p class="text-xs text-secondary mb-0">Ej: 36, 42, S, M, XL</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('configuracion.storeTalla') }}" class="d-flex gap-2 mb-3">
          @csrf
          <input type="text" name="nombre" class="form-control form-control-sm"
                 placeholder="Nueva talla..." required maxlength="20">
          <button type="submit" class="btn btn-primary btn-sm text-nowrap">
            <i class="fas fa-plus"></i> Agregar
          </button>
        </form>

        <div class="table-responsive" style="max-height:320px; overflow-y:auto">
          <table class="table align-items-center mb-0">
            <tbody>
              @forelse($tallas as $talla)
              <tr>
                <td><p class="text-sm font-weight-bold mb-0">{{ $talla->nombre }}</p></td>
                <td class="text-end">
                  <form action="{{ route('configuracion.destroyTalla', $talla) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-link p-0 text-danger text-xs"
                            data-confirm="Se eliminará la talla '{{ $talla->nombre }}'."
                            data-title="¿Eliminar talla?"
                            data-ok="Sí, eliminar"
                            data-icon="question">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td class="text-secondary text-sm text-center py-3">Sin tallas registradas.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
