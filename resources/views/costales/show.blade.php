@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  {{-- Header del costal --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-body py-3">
        <div class="row align-items-center">
          <div class="col-auto">
            <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-md">
              <i class="fas fa-shopping-bag text-white opacity-10 text-xl"></i>
            </div>
          </div>
          <div class="col">
            <h5 class="mb-0">Costal {{ $costal->numero_costal ?? '#'.$costal->id }}</h5>
            <p class="mb-0 text-sm text-secondary">
              <strong>Proveedor:</strong> {{ $costal->proveedor->nombre }} &nbsp;|&nbsp;
              <strong>Sucursal:</strong> {{ $costal->sucursalDestino->nombre }} &nbsp;|&nbsp;
              <strong>Fecha:</strong> {{ $costal->fecha_compra->format('d/m/Y') }}
            </p>
          </div>
          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Peso</p>
            <h6 class="mb-0">{{ number_format($costal->peso_libras, 2) }} lbs</h6>
          </div>
          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Q por libra</p>
            <h6 class="mb-0">Q{{ number_format($costal->precio_por_libra, 2) }}</h6>
          </div>
          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Costo total</p>
            <h4 class="mb-0 text-primary">Q{{ number_format($costal->costo_total, 2) }}</h4>
          </div>
          <div class="col-auto">
            <span class="badge badge-sm bg-gradient-{{ $costal->estado_color }} fs-6">
              {{ $costal->estado_label }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Estadísticas de clasificación --}}
  <div class="col-sm-3 mb-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h4 class="mb-0 text-primary">{{ $stats['total'] }}</h4>
        <p class="text-xs text-secondary mb-0">Total clasificados</p>
      </div>
    </div>
  </div>
  <div class="col-sm-3 mb-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h4 class="mb-0 text-secondary">{{ $stats['regulares'] }}</h4>
        <p class="text-xs text-secondary mb-0">Regulares</p>
      </div>
    </div>
  </div>
  <div class="col-sm-3 mb-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h4 class="mb-0 text-warning">{{ $stats['primera_lavado'] }}</h4>
        <p class="text-xs text-secondary mb-0">Primera (Lavado)</p>
      </div>
    </div>
  </div>
  <div class="col-sm-3 mb-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h4 class="mb-0 text-info">{{ $stats['primera_lustre'] }}</h4>
        <p class="text-xs text-secondary mb-0">Primera (Lustre)</p>
      </div>
    </div>
  </div>

  @if($costal->estado !== 'clasificado')
  {{-- Formularios de clasificación --}}
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6 class="mb-0"><i class="fas fa-layer-group me-2 text-secondary"></i>Agregar zapatos <span class="badge bg-gradient-secondary">Regulares (por lote)</span></h6>
        <p class="text-xs text-secondary mb-0 mt-1">Crea múltiples registros de una sola vez</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('costales.clasificar', $costal) }}">
          @csrf
          <input type="hidden" name="tipo_clasificacion" value="regular">
          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label text-xs fw-bold">Categoría <span class="text-danger">*</span></label>
              <select name="categoria_id" class="form-select form-select-sm" required>
                <option value="">— Seleccionar —</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label text-xs fw-bold">Tipo <span class="text-danger">*</span></label>
              <select name="tipo_id" class="form-select form-select-sm" required>
                <option value="">— Seleccionar —</option>
                @foreach($tipos as $t)
                  <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label text-xs fw-bold">Talla <span class="text-danger">*</span></label>
              <input type="text" name="talla" class="form-control form-control-sm" placeholder="Ej: 42" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label text-xs fw-bold">Cantidad <span class="text-danger">*</span></label>
              <input type="number" name="cantidad" class="form-control form-control-sm" min="1" max="500" placeholder="Ej: 10" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label text-xs fw-bold">Precio lista (Q) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="precio_lista" class="form-control form-control-sm" placeholder="Ej: 25.00" required>
            </div>
            <div class="col-12 mb-2">
              <label class="form-label text-xs fw-bold">Notas</label>
              <input type="text" name="notas" class="form-control form-control-sm" placeholder="Observaciones opcionales">
            </div>
          </div>
          <button type="submit" class="btn btn-secondary btn-sm mt-2 w-100">
            <i class="fas fa-plus me-1"></i> Agregar lote al inventario
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6 class="mb-0"><i class="fas fa-star me-2 text-warning"></i>Agregar zapato <span class="badge bg-gradient-warning">De Primera (Individual)</span></h6>
        <p class="text-xs text-secondary mb-0 mt-1">Registro individual con código único de seguimiento</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('costales.clasificar', $costal) }}">
          @csrf
          <input type="hidden" name="tipo_clasificacion" value="primera">
          <div class="row">
            <div class="col-md-6 mb-2">
              <label class="form-label text-xs fw-bold">Categoría <span class="text-danger">*</span></label>
              <select name="categoria_id" class="form-select form-select-sm" required>
                <option value="">— Seleccionar —</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label text-xs fw-bold">Tipo <span class="text-danger">*</span></label>
              <select name="tipo_id" class="form-select form-select-sm" required>
                <option value="">— Seleccionar —</option>
                @foreach($tipos as $t)
                  <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <label class="form-label text-xs fw-bold">Clasificación de primera <span class="text-danger">*</span></label>
              <select name="clasificacion" class="form-select form-select-sm" required>
                <option value="">— Seleccionar —</option>
                <option value="primera_lavado">Primera — Lavado (tenis, sucio)</option>
                <option value="primera_lustre">Primera — Lustre (escolar, dama)</option>
              </select>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label text-xs fw-bold">Talla <span class="text-danger">*</span></label>
              <input type="text" name="talla" class="form-control form-control-sm" placeholder="Ej: 42" required>
            </div>
            <div class="col-md-3 mb-2">
              <label class="form-label text-xs fw-bold">Precio lista (Q) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="precio_lista" class="form-control form-control-sm" placeholder="Ej: 350.00" required>
            </div>
            <div class="col-12 mb-2">
              <label class="form-label text-xs fw-bold">Notas</label>
              <input type="text" name="notas" class="form-control form-control-sm" placeholder="Descripción del zapato...">
            </div>
          </div>
          <button type="submit" class="btn btn-warning btn-sm mt-2 w-100">
            <i class="fas fa-star me-1"></i> Registrar zapato de primera
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Cerrar clasificación --}}
  <div class="col-12 mb-4">
    <div class="card border border-success">
      <div class="card-body py-3 d-flex align-items-center justify-content-between">
        <div>
          <h6 class="mb-0 text-success">¿Terminaste de clasificar este costal?</h6>
          <p class="mb-0 text-xs text-secondary">Al cerrar, el costal queda marcado como "Clasificado" y no se puede agregar más zapatos.</p>
        </div>
        <form action="{{ route('costales.cerrarClasificacion', $costal) }}" method="POST">
          @csrf
          <button type="button" class="btn btn-success btn-sm"
                  data-confirm="Ya no podrás agregar más zapatos a este costal."
                  data-title="¿Cerrar clasificación?"
                  data-ok="Sí, cerrar"
                  data-btn-class="btn-success"
                  data-icon="question">
            <i class="fas fa-check me-1"></i> Cerrar clasificación
          </button>
        </form>
      </div>
    </div>
  </div>
  @endif

  {{-- Lista de zapatos del costal --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">Zapatos clasificados de este costal ({{ $stats['total'] }})</h6>
      </div>
      <div class="card-body px-3 pt-2 pb-3">
        <table id="tablaZapatos" class="table table-hover align-items-center mb-0 w-100">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Categoría</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Talla</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Clasificación</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Precio lista</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
            </tr>
          </thead>
          <tbody>
            @foreach($costal->zapatos as $zapato)
            @php $colors = ['regular'=>'secondary','primera_lavado'=>'warning','primera_lustre'=>'info']; @endphp
            <tr>
              <td><span class="text-xs font-weight-bold font-monospace">{{ $zapato->codigo_unico }}</span></td>
              <td><span class="text-xs">{{ $zapato->categoria->nombre }}</span></td>
              <td><span class="text-xs">{{ $zapato->tipo->nombre }}</span></td>
              <td><span class="text-xs font-weight-bold">{{ $zapato->talla }}</span></td>
              <td>
                <span class="badge badge-sm bg-gradient-{{ $colors[$zapato->clasificacion] ?? 'secondary' }}">
                  {{ $zapato->clasificacion_label }}
                </span>
              </td>
              <td><span class="text-xs font-weight-bold text-primary">Q{{ number_format($zapato->precio_lista, 2) }}</span></td>
              <td>
                <span class="badge badge-sm bg-gradient-{{ $zapato->estado_color }}">
                  {{ ucfirst(str_replace('_', ' ', $zapato->estado)) }}
                </span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
  #tablaZapatos_wrapper .dataTables_filter input {
    border-radius: 0.5rem;
    border: 1px solid #d2d6da;
    padding: 0.4rem 0.75rem;
    font-size: 0.875rem;
  }
  #tablaZapatos_wrapper .dataTables_length select {
    border-radius: 0.5rem;
    border: 1px solid #d2d6da;
    padding: 0.3rem 0.6rem;
    font-size: 0.875rem;
  }
  #tablaZapatos_wrapper .dataTables_info,
  #tablaZapatos_wrapper .dataTables_filter label,
  #tablaZapatos_wrapper .dataTables_length label {
    font-size: 0.8rem;
    color: #67748e;
  }
  #tablaZapatos_wrapper .page-link {
    border-radius: 0.5rem !important;
    font-size: 0.8rem;
    color: #344767;
  }
  #tablaZapatos_wrapper .page-item.active .page-link {
    background: linear-gradient(310deg,#7928ca,#ff0080);
    border-color: transparent;
    color: #fff;
  }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
  $('#tablaZapatos').DataTable({
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-MX.json',
      emptyTable: 'Aún no hay zapatos clasificados. Usa los formularios de arriba.'
    },
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
    order: [[0, 'asc']]
  });
});
</script>
@endpush
