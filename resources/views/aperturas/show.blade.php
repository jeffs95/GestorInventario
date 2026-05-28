@extends('layouts.user_type.auth')

@section('content')
<div class="row">

  {{-- ── Header de la apertura ──────────────────────────────────────────── --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-body py-3">
        <div class="row align-items-center g-3">

          <div class="col-auto">
            <div class="icon icon-shape icon-lg bg-gradient-warning shadow text-center border-radius-md">
              <i class="fas fa-box-open text-white opacity-10 text-xl"></i>
            </div>
          </div>

          <div class="col">
            <div class="d-flex align-items-center gap-2">
              <h5 class="mb-0">Apertura #{{ $apertura->id }}</h5>
              @if($apertura->estado === 'abierta')
                <span class="badge bg-gradient-warning">Abierta</span>
              @else
                <span class="badge bg-gradient-success">Clasificada</span>
              @endif
            </div>
            <p class="mb-0 text-sm text-secondary">
              <strong>Lote:</strong>
              <a href="{{ route('lotes.show', $apertura->lote) }}" class="text-info">
                {{ $apertura->lote->numero_lote }}
              </a>
              &nbsp;|&nbsp;
              <strong>Proveedor:</strong> {{ $apertura->lote->proveedor->nombre }}
              &nbsp;|&nbsp;
              <strong>Sucursal:</strong> {{ $apertura->lote->sucursalDestino->nombre }}
              &nbsp;|&nbsp;
              <strong>Fecha:</strong> {{ $apertura->fecha->format('d/m/Y') }}
            </p>
            @if($apertura->notas)
              <p class="mb-0 text-xs text-secondary mt-1"><strong>Notas:</strong> {{ $apertura->notas }}</p>
            @endif
          </div>

          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Costales abiertos</p>
            <h6 class="mb-0">{{ $apertura->costales->count() }}</h6>
          </div>

          <div class="col-auto text-end">
            <p class="mb-0 text-xs text-secondary">Primera contados</p>
            <h4 class="mb-0 text-primary">{{ $stats['primera_lavado'] + $stats['primera_lustre'] }}</h4>
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- ── Costales incluidos ──────────────────────────────────────────────── --}}
  <div class="col-12 mb-3">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">
          <i class="fas fa-shopping-bag me-2 text-secondary"></i>
          Costales en esta apertura ({{ $apertura->costales->count() }})
        </h6>
      </div>
      <div class="card-body px-3 pt-2 pb-2">
        <div class="d-flex flex-wrap gap-2">
          @foreach($apertura->costales as $costal)
            <a href="{{ route('costales.show', $costal) }}"
               class="badge bg-gradient-secondary text-decoration-none py-2 px-3 fs-6"
               title="Ver costal">
              {{ $costal->numero_costal ?? '#'.$costal->id }}
              &nbsp;·&nbsp;{{ number_format($costal->peso_libras, 0) }} lbs
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- ── Estadísticas ────────────────────────────────────────────────────── --}}
  <div class="col-sm-3 mb-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h4 class="mb-0 text-secondary">{{ $stats['regulares'] }}</h4>
        <p class="text-xs text-secondary mb-0">Regulares en inventario</p>
      </div>
    </div>
  </div>
  <div class="col-sm-3 mb-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h4 class="mb-0 text-warning">{{ $stats['primera_lavado'] }}</h4>
        <p class="text-xs text-secondary mb-0">Primera Lavado (contados)</p>
      </div>
    </div>
  </div>
  <div class="col-sm-3 mb-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h4 class="mb-0 text-info">{{ $stats['primera_lustre'] }}</h4>
        <p class="text-xs text-secondary mb-0">Primera Lustre (contados)</p>
      </div>
    </div>
  </div>
  <div class="col-sm-3 mb-3">
    <div class="card">
      <div class="card-body text-center py-3">
        <h4 class="mb-0 text-success">{{ $stats['primera_preparada'] }}</h4>
        <p class="text-xs text-secondary mb-0">Primera ya en inventario</p>
      </div>
    </div>
  </div>

  @if($apertura->estado === 'abierta')

  {{-- ── Formulario: Regulares (batch) ─────────────────────────────────── --}}
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6 class="mb-0">
          <i class="fas fa-layer-group me-2 text-secondary"></i>Agregar zapatos
          <span class="badge bg-gradient-secondary">Regulares (por lote)</span>
        </h6>
        <p class="text-xs text-secondary mb-0 mt-1">Crea múltiples registros de una sola vez</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('aperturas.clasificar', $apertura) }}">
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
              <input type="number" name="cantidad" class="form-control form-control-sm"
                     min="1" max="1000" placeholder="Ej: 80" required>
            </div>
            <div class="col-md-4 mb-2">
              <label class="form-label text-xs fw-bold">Precio lista (Q) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="precio_lista"
                     class="form-control form-control-sm" placeholder="Ej: 25.00" required>
            </div>
            <div class="col-12 mb-2">
              <label class="form-label text-xs fw-bold">Notas</label>
              <input type="text" name="notas" class="form-control form-control-sm"
                     placeholder="Observaciones opcionales">
            </div>
          </div>
          <button type="submit" class="btn btn-secondary btn-sm mt-2 w-100">
            <i class="fas fa-plus me-1"></i> Agregar lote al inventario
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- ── Formulario: Primera (conteo batch) ────────────────────────────── --}}
  <div class="col-lg-6 mb-4">
    <div class="card h-100">
      <div class="card-header pb-0">
        <h6 class="mb-0">
          <i class="fas fa-star me-2 text-warning"></i>Contar zapatos
          <span class="badge bg-gradient-warning">De Primera (Conteo)</span>
        </h6>
        <p class="text-xs text-secondary mb-0 mt-1">
          Registra cuántos salieron. Quedan en <strong>En proceso</strong> hasta terminar lavado/preparación.
        </p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('aperturas.clasificar', $apertura) }}">
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
              <label class="form-label text-xs fw-bold">Tipo de primera <span class="text-danger">*</span></label>
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
              <label class="form-label text-xs fw-bold">Cantidad <span class="text-danger">*</span></label>
              <input type="number" name="cantidad" class="form-control form-control-sm"
                     min="1" max="1000" placeholder="Ej: 300" required>
            </div>
            <div class="col-12 mb-2">
              <label class="form-label text-xs fw-bold">Precio lista estimado (Q)</label>
              <input type="number" step="0.01" name="precio_lista"
                     class="form-control form-control-sm" placeholder="0.00 (se puede ajustar después)">
            </div>
            <div class="col-12 mb-2">
              <label class="form-label text-xs fw-bold">Notas</label>
              <input type="text" name="notas" class="form-control form-control-sm"
                     placeholder="Observaciones del lote de primera...">
            </div>
          </div>
          <button type="submit" class="btn btn-warning btn-sm mt-2 w-100">
            <i class="fas fa-star me-1"></i> Registrar conteo de primera
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- ── Cerrar apertura ─────────────────────────────────────────────────── --}}
  <div class="col-12 mb-4">
    <div class="card border border-success">
      <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
          <h6 class="mb-0 text-success">¿Terminaste de clasificar esta apertura?</h6>
          <p class="mb-0 text-xs text-secondary">
            Al cerrar, los {{ $apertura->costales->count() }} costales quedan marcados como
            <strong>Clasificados</strong> y no se pueden agregar más zapatos.
          </p>
        </div>
        <form action="{{ route('aperturas.cerrar', $apertura) }}" method="POST">
          @csrf
          <button type="button" class="btn btn-success btn-sm"
                  data-confirm="Los {{ $apertura->costales->count() }} costales quedarán marcados como Clasificados."
                  data-title="¿Cerrar apertura?"
                  data-ok="Sí, cerrar"
                  data-btn-class="btn-success"
                  data-icon="question">
            <i class="fas fa-check me-1"></i> Cerrar apertura
          </button>
        </form>
      </div>
    </div>
  </div>

  @endif {{-- apertura abierta --}}

  {{-- ── Conteos de primera registrados ────────────────────────────────── --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">
          <i class="fas fa-star me-2 text-warning"></i>
          Conteos de primera en esta apertura
          @if($stats['lotes_pendientes'] > 0)
            <span class="badge bg-gradient-warning ms-1">{{ $stats['lotes_pendientes'] }} pendiente{{ $stats['lotes_pendientes'] !== 1 ? 's' : '' }} de preparar</span>
          @endif
        </h6>
        <p class="text-xs text-secondary mb-0 mt-1">
          Estos son los conteos registrados al abrir los costales.
          El inventario real se genera cuando termine la preparación.
        </p>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Tipo de primera</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Categoría</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tipo zapato</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Talla</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Contados</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Preparados</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pendientes</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Precio est.</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
                <th class="text-secondary opacity-7"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($apertura->zapatoLotes as $lote)
              <tr>
                <td class="ps-3">
                  @php $badgeColor = $lote->clasificacion === 'primera_lavado' ? 'warning' : 'info'; @endphp
                  <span class="badge badge-sm bg-gradient-{{ $badgeColor }}">
                    {{ $lote->clasificacion_label }}
                  </span>
                </td>
                <td class="ps-2"><span class="text-xs">{{ $lote->categoria->nombre }}</span></td>
                <td class="ps-2"><span class="text-xs">{{ $lote->tipo->nombre }}</span></td>
                <td class="ps-2"><span class="text-xs font-weight-bold">{{ $lote->talla }}</span></td>
                <td class="ps-2">
                  <span class="text-xs font-weight-bold text-dark">{{ $lote->cantidad_contada }}</span>
                </td>
                <td class="ps-2">
                  <span class="text-xs font-weight-bold text-success">{{ $lote->cantidad_registrada }}</span>
                </td>
                <td class="ps-2">
                  @if($lote->pendientes > 0)
                    <span class="text-xs font-weight-bold text-warning">{{ $lote->pendientes }}</span>
                  @else
                    <span class="text-xs text-success"><i class="fas fa-check"></i> Listo</span>
                  @endif
                </td>
                <td class="ps-2">
                  <span class="text-xs text-secondary">
                    {{ $lote->precio_estimado ? 'Q'.number_format($lote->precio_estimado, 2) : '—' }}
                  </span>
                </td>
                <td class="ps-2">
                  <span class="badge badge-sm bg-gradient-{{ $lote->estado_color }}">
                    {{ $lote->estado_label }}
                  </span>
                </td>
                <td class="text-center pe-3">
                  @if($lote->estado === 'contado')
                    {{-- Preparación lista: inicia el proceso --}}
                    <form action="{{ route('preparacion.iniciar', $lote) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="button" class="btn btn-warning btn-sm mb-0"
                              data-confirm="Los zapatos saldrán de bodega para iniciar el proceso de preparación."
                              data-title="¿Iniciar preparación?"
                              data-ok="Sí, iniciar"
                              data-btn-class="btn-warning"
                              data-icon="question">
                        <i class="fas fa-soap me-1"></i> Preparación lista
                      </button>
                    </form>
                  @elseif($lote->estado === 'en_preparacion')
                    <a href="{{ route('preparacion.show', $lote) }}"
                       class="btn btn-primary btn-sm mb-0">
                      <i class="fas fa-shoe-prints me-1"></i> Registrar uno a uno
                    </a>
                  @else
                    <a href="{{ route('preparacion.show', $lote) }}"
                       class="btn btn-outline-success btn-sm mb-0">
                      <i class="fas fa-eye me-1"></i> Ver
                    </a>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center py-4 text-secondary text-sm">
                  Aún no hay conteos de primera registrados.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Regulares en inventario ─────────────────────────────────────────── --}}
  @if($apertura->zapatos->where('clasificacion','regular')->count())
  <div class="col-12 mt-2">
    <div class="card">
      <div class="card-header pb-0">
        <h6 class="mb-0">
          <i class="fas fa-boxes me-2 text-secondary"></i>
          Zapatos regulares en inventario ({{ $apertura->zapatos->where('clasificacion','regular')->count() }})
        </h6>
      </div>
      <div class="card-body px-3 pt-2 pb-3">
        <table id="tablaZapatos" class="table table-hover align-items-center mb-0 w-100">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Categoría</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Talla</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Precio lista</th>
            </tr>
          </thead>
          <tbody>
            @foreach($apertura->zapatos->where('clasificacion','regular') as $zapato)
            <tr>
              <td><span class="text-xs font-monospace">{{ $zapato->codigo_unico }}</span></td>
              <td><span class="text-xs">{{ $zapato->categoria->nombre }}</span></td>
              <td><span class="text-xs">{{ $zapato->tipo->nombre }}</span></td>
              <td><span class="text-xs font-weight-bold">{{ $zapato->talla }}</span></td>
              <td><span class="text-xs font-weight-bold text-primary">Q{{ number_format($zapato->precio_lista, 2) }}</span></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
  #tablaZapatos_wrapper .dataTables_filter input,
  #tablaZapatos_wrapper .dataTables_length select {
    border-radius: 0.5rem;
    border: 1px solid #d2d6da;
    padding: 0.4rem 0.75rem;
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
