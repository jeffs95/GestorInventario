@extends('layouts.user_type.auth')

@section('content')

{{-- ══ Stats ═══════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-xl-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-primary shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-layer-group text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Lotes</p>
          <h5 class="mb-0 font-weight-bolder">{{ number_format($lotes->total()) }}</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-success shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-check text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">En inventario</p>
          <h5 class="mb-0 font-weight-bolder">{{ number_format($stats['en_inventario']) }}</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-info shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-tag text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Vendidos (filtro)</p>
          <h5 class="mb-0 font-weight-bolder">{{ number_format($stats['vendido']) }}</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="icon icon-shape icon-sm bg-gradient-warning shadow text-center border-radius-md flex-shrink-0">
          <i class="fas fa-dollar-sign text-white opacity-10"></i>
        </div>
        <div>
          <p class="text-xs text-secondary mb-0">Valor inventario</p>
          <h5 class="mb-0 font-weight-bolder">Q{{ number_format($stats['valor_total'], 0) }}</h5>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══ Filtros ══════════════════════════════════════════════════════════════ --}}
<div class="card mb-3">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filtros</h6>
    <button class="btn btn-sm btn-outline-secondary py-1 px-2" type="button"
            data-bs-toggle="collapse" data-bs-target="#filtrosPanel">
      <i class="fas fa-sliders-h me-1"></i>Mostrar / ocultar
    </button>
  </div>
  <div class="collapse show" id="filtrosPanel">
    <div class="card-body pt-2 pb-3">
      <form method="GET" action="{{ route('inventario.index') }}" id="formFiltros">

        {{-- Fila 1 --}}
        <div class="row g-2 mb-2">
          <div class="col-md-3">
            <label class="form-label text-xs fw-bold mb-1">Buscar</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
              <input type="text" name="buscar" class="form-control"
                     placeholder="Código, marca, color…" value="{{ request('buscar') }}">
            </div>
          </div>
          @if(auth()->user()->isDueno())
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Sucursal</label>
            <select name="sucursal_id" class="form-select form-select-sm">
              <option value="">Todas</option>
              @foreach($sucursales as $s)
                <option value="{{ $s->id }}" {{ request('sucursal_id') == $s->id ? 'selected':'' }}>{{ $s->nombre }}</option>
              @endforeach
            </select>
          </div>
          @endif
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Categoría</label>
            <select name="categoria_id" class="form-select form-select-sm">
              <option value="">Todas</option>
              @foreach($categorias as $c)
                <option value="{{ $c->id }}" {{ request('categoria_id') == $c->id ? 'selected':'' }}>{{ $c->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Tipo</label>
            <select name="tipo_id" class="form-select form-select-sm">
              <option value="">Todos</option>
              @foreach($tipos as $t)
                <option value="{{ $t->id }}" {{ request('tipo_id') == $t->id ? 'selected':'' }}>{{ $t->nombre }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Talla</label>
            <select name="talla_id" class="form-select form-select-sm">
              <option value="">Todas</option>
              @foreach($tallas as $ta)
                <option value="{{ $ta->id }}" {{ request('talla_id') == $ta->id ? 'selected':'' }}>{{ $ta->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Fila 2 --}}
        <div class="row g-2 align-items-end">
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Clasificación</label>
            <select name="clasificacion" class="form-select form-select-sm">
              <option value="">Todas</option>
              <option value="regular"        {{ request('clasificacion')==='regular'        ?'selected':'' }}>Regular</option>
              <option value="primera_lavado" {{ request('clasificacion')==='primera_lavado' ?'selected':'' }}>Primera (Lavado)</option>
              <option value="primera_lustre" {{ request('clasificacion')==='primera_lustre' ?'selected':'' }}>Primera (Lustre)</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Estado</label>
            <select name="estado" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="en_inventario" {{ request('estado')==='en_inventario'?'selected':'' }}>En inventario</option>
              <option value="vendido"       {{ request('estado')==='vendido'      ?'selected':'' }}>Vendido</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Género</label>
            <select name="genero" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="hombre" {{ request('genero')==='hombre'?'selected':'' }}>Hombre</option>
              <option value="mujer"  {{ request('genero')==='mujer' ?'selected':'' }}>Mujer</option>
              <option value="nino"   {{ request('genero')==='nino'  ?'selected':'' }}>Niño</option>
              <option value="nina"   {{ request('genero')==='nina'  ?'selected':'' }}>Niña</option>
              <option value="unisex" {{ request('genero')==='unisex'?'selected':'' }}>Unisex</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Condición</label>
            <select name="condicion" class="form-select form-select-sm">
              <option value="">Todas</option>
              <option value="muy_bueno" {{ request('condicion')==='muy_bueno'?'selected':'' }}>Muy bueno</option>
              <option value="bueno"     {{ request('condicion')==='bueno'    ?'selected':'' }}>Bueno</option>
              <option value="regular"   {{ request('condicion')==='regular'  ?'selected':'' }}>Regular</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label text-xs fw-bold mb-1">Foto</label>
            <select name="con_foto" class="form-select form-select-sm">
              <option value="">Todos</option>
              <option value="1" {{ request('con_foto')==='1'?'selected':'' }}>Con foto</option>
              <option value="0" {{ request('con_foto')==='0'?'selected':'' }}>Sin foto</option>
            </select>
          </div>
          <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm">
              <i class="fas fa-search me-1"></i>Filtrar
            </button>
            <a href="{{ route('inventario.index') }}" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-times me-1"></i>Limpiar
            </a>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- ══ Barra de herramientas ════════════════════════════════════════════════ --}}
<div class="card mb-3">
  <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">

    <span class="text-sm text-secondary">
      <strong class="text-dark">{{ $lotes->total() }}</strong>
      {{ $lotes->total() === 1 ? 'lote' : 'lotes' }} ·
      <strong class="text-dark">{{ number_format($stats['total']) }}</strong>
      zapatos
      @if(request()->hasAny(['buscar','sucursal_id','categoria_id','tipo_id','clasificacion','estado','talla_id','genero','condicion','con_foto']))
        <span class="badge bg-gradient-info ms-1">Filtros activos</span>
      @endif
    </span>

    <div class="d-flex align-items-center gap-2 flex-wrap">

      {{-- Expandir / Colapsar todos --}}
      <button type="button" id="btnExpandirTodos"
              class="btn btn-sm btn-outline-primary py-1 px-2">
        <i class="fas fa-expand-alt me-1"></i>Expandir todos
      </button>
      <button type="button" id="btnColapsarTodos"
              class="btn btn-sm btn-outline-secondary py-1 px-2">
        <i class="fas fa-compress-alt me-1"></i>Colapsar todos
      </button>

      {{-- Lotes por página --}}
      <select class="form-select form-select-sm" style="width:auto"
              onchange="cambiarPorPagina(this.value)">
        @foreach([5, 8, 15, 25] as $n)
          <option value="{{ $n }}" {{ request('per_page', 8) == $n ? 'selected':'' }}>
            {{ $n }} lotes/pág.
          </option>
        @endforeach
      </select>

      {{-- Exportar CSV --}}
      <a href="{{ request()->fullUrlWithQuery(['exportar' => 'csv']) }}"
         class="btn btn-sm btn-outline-success">
        <i class="fas fa-file-csv me-1"></i>Exportar CSV
      </a>

    </div>
  </div>
</div>

{{-- ══ Acordeón de lotes ════════════════════════════════════════════════════ --}}
@php
  $clsColor = ['regular' => 'secondary', 'primera_lavado' => 'warning', 'primera_lustre' => 'info'];
@endphp

@forelse($lotes as $lote)
@php
  $zapatos      = $lote->zapatos;
  $cntInv       = $zapatos->where('estado', 'en_inventario')->count();
  $cntVendido   = $zapatos->where('estado', 'vendido')->count();
  $cntPendiente = $zapatos->where('estado', 'pendiente_precio')->count();
  $valorInv     = $zapatos->where('estado', 'en_inventario')->sum('precio_lista');
  $clsBadge     = $clsColor[$lote->clasificacion] ?? 'secondary';
  $collapseId   = 'lote-body-' . $lote->id;
@endphp

<div class="card mb-2 shadow-sm" id="card-lote-{{ $lote->id }}">

  {{-- ── Cabecera del acordeón ──────────────────────────────────────────── --}}
  <div class="card-header p-0 border-0" style="border-radius:1rem">
    <button class="lote-toggle-btn w-100 text-start"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#{{ $collapseId }}"
            aria-expanded="false"
            aria-controls="{{ $collapseId }}">

      <div class="d-flex align-items-center gap-3 flex-wrap px-3 py-3">

        {{-- Ícono + identificador --}}
        <div class="d-flex align-items-center gap-2 flex-shrink-0" style="min-width:180px">
          <div class="icon icon-shape icon-sm shadow border-radius-md flex-shrink-0 d-flex align-items-center justify-content-center"
               style="background:linear-gradient(310deg,#1a3c5e,#0ea5e9)">
            <i class="fas fa-layer-group" style="color:#fff;font-size:.7rem"></i>
          </div>
          <div>
            <p class="mb-0 text-sm font-weight-bold text-dark lh-1">
              Lote {{ $lote->apertura->lote->numero_lote ?? '—' }}
            </p>
            <p class="mb-0 text-xxs text-secondary">
              {{ $lote->apertura->lote->proveedor->nombre ?? '—' }}
              @if($lote->apertura->lote->fecha_compra)
                · {{ \Carbon\Carbon::parse($lote->apertura->lote->fecha_compra)->format('d/m/Y') }}
              @endif
            </p>
          </div>
        </div>

        {{-- Badges clasificación + estado --}}
        <div class="d-flex gap-1 flex-wrap">
          <span class="badge badge-sm bg-gradient-{{ $clsBadge }}">
            {{ $lote->clasificacion_label }}
          </span>
          <span class="badge badge-sm bg-gradient-{{ $lote->estado_color }}">
            {{ $lote->estado_label }}
          </span>
          @if($lote->categoria)
            <span class="badge badge-sm bg-gradient-light text-dark border" style="font-size:.65rem">
              {{ $lote->categoria->nombre }}
            </span>
          @endif
        </div>

        {{-- Contadores y valor (push a la derecha) --}}
        <div class="d-flex gap-3 ms-auto align-items-center">

          @if($cntInv > 0)
          <div class="text-end">
            <p class="text-xxs text-secondary mb-0 lh-1">Inventario</p>
            <p class="text-sm font-weight-bolder text-success mb-0">{{ $cntInv }}</p>
          </div>
          @endif

          @if($cntVendido > 0)
          <div class="text-end">
            <p class="text-xxs text-secondary mb-0 lh-1">Vendidos</p>
            <p class="text-sm font-weight-bolder text-info mb-0">{{ $cntVendido }}</p>
          </div>
          @endif

          @if($cntPendiente > 0)
          <div class="text-end">
            <p class="text-xxs text-secondary mb-0 lh-1">Sin precio</p>
            <p class="text-sm font-weight-bolder text-warning mb-0">{{ $cntPendiente }}</p>
          </div>
          @endif

          <div class="text-end">
            <p class="text-xxs text-secondary mb-0 lh-1">Valor</p>
            <p class="text-sm font-weight-bolder text-primary mb-0">
              Q{{ number_format($valorInv, 0) }}
            </p>
          </div>

          {{-- Chevron --}}
          <div class="lote-chevron d-flex align-items-center ps-1">
            <i class="fas fa-chevron-down text-secondary" style="font-size:.75rem;transition:transform .25s"></i>
          </div>
        </div>

      </div>
    </button>
  </div>

  {{-- ── Cuerpo colapsable: tabla de zapatos ─────────────────────────────── --}}
  <div class="collapse" id="{{ $collapseId }}">

    <div class="table-responsive" style="border-top:1px solid #f0f0f0">
      <table class="table align-middle mb-0">
        <thead style="background:#f8f9fa">
          <tr>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Foto</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Categoría / Tipo</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Talla</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Color / Marca</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Precio Q</th>
            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estado</th>
          </tr>
        </thead>
        <tbody>
          @forelse($zapatos as $z)
          <tr>
            {{-- Foto --}}
            <td class="ps-3">
              @if($z->foto_path)
                <img src="{{ route('inventario.foto', $z) }}"
                     alt="foto"
                     class="avatar avatar-md border-radius-md shadow foto-thumb"
                     style="width:44px;height:44px;object-fit:cover;cursor:pointer"
                     data-src="{{ route('inventario.foto', $z) }}"
                     data-codigo="{{ $z->codigo_unico }}">
              @else
                <div class="avatar avatar-md border-radius-md bg-gradient-light d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px">
                  <i class="fas fa-image text-secondary opacity-5"></i>
                </div>
              @endif
            </td>

            {{-- Código --}}
            <td class="ps-2">
              <span class="text-xs font-weight-bold font-monospace text-primary">
                {{ $z->codigo_unico }}
              </span>
            </td>

            {{-- Categoría / Tipo --}}
            <td class="ps-2">
              <p class="text-xs font-weight-bold mb-0">{{ $z->categoria->nombre ?? '—' }}</p>
              <p class="text-xxs text-secondary mb-0">{{ $z->tipo->nombre ?? '—' }}</p>
            </td>

            {{-- Talla --}}
            <td class="ps-2">
              <span class="badge badge-sm bg-gradient-dark">
                {{ $z->talla->nombre ?? $z->talla ?? '—' }}
              </span>
            </td>

            {{-- Color / Marca --}}
            <td class="ps-2">
              <p class="text-xs mb-0">{{ $z->color ?? '—' }}</p>
              <p class="text-xxs text-secondary mb-0">{{ $z->marca ?? '—' }}</p>
            </td>

            {{-- Precio --}}
            <td class="ps-2">
              <span class="text-xs font-weight-bold text-success">
                Q{{ number_format($z->precio_lista, 2) }}
              </span>
            </td>

            {{-- Estado --}}
            <td class="ps-2">
              <span class="badge badge-sm bg-gradient-{{ $z->estado_color }}">
                {{ $z->estado_label }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center py-3 text-secondary text-xs">
              No hay zapatos que coincidan con los filtros en este lote.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pie del acordeón: enlace a preparación --}}
    <div class="px-3 py-2 d-flex justify-content-between align-items-center"
         style="background:#f8f9fa;border-radius:0 0 1rem 1rem;border-top:1px solid #f0f0f0">
      <span class="text-xxs text-secondary">
        <i class="fas fa-info-circle me-1"></i>
        {{ $zapatos->count() }} {{ $zapatos->count() === 1 ? 'zapato' : 'zapatos' }} mostrados
        (lote total: {{ $lote->cantidad_contada }})
      </span>
      <a href="{{ route('preparacion.show', $lote) }}"
         class="btn btn-sm btn-outline-primary py-1 px-3"
         style="font-size:.75rem">
        <i class="fas fa-tags me-1"></i>Ver preparación
      </a>
    </div>

  </div>

</div>
@empty

<div class="card text-center py-5">
  <div class="card-body">
    <i class="fas fa-search fa-2x text-secondary opacity-3 mb-3 d-block"></i>
    <p class="text-secondary text-sm mb-1">No se encontraron lotes con los filtros seleccionados.</p>
    <a href="{{ route('inventario.index') }}" class="btn btn-outline-primary btn-sm mt-2">
      <i class="fas fa-times me-1"></i>Quitar filtros
    </a>
  </div>
</div>

@endforelse

{{-- ══ Paginación ═══════════════════════════════════════════════════════════ --}}
@if($lotes->hasPages())
<div class="d-flex justify-content-center mt-4 mb-2">{{ $lotes->links() }}</div>
@endif

{{-- ══ Modal lightbox foto ══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalFoto" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow-lg" style="border-radius:1rem;overflow:hidden">
      <div class="modal-header border-0 pb-0 px-3 pt-2">
        <small id="modalFotoCodigo" class="text-primary font-monospace font-weight-bold"></small>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <img id="modalFotoImg" src="" alt=""
             style="width:100%;max-height:420px;object-fit:contain;background:#f8f9fa">
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
  /* ── Botón de acordeón personalizado ─── */
  .lote-toggle-btn {
    background: none;
    border: none;
    cursor: pointer;
    border-radius: 1rem;
    transition: background .15s;
  }
  .lote-toggle-btn:hover {
    background: rgba(14, 165, 233, .04);
  }

  /* ── Chevron rota cuando el acordeón está abierto ─── */
  .lote-toggle-btn[aria-expanded="true"] .lote-chevron i {
    transform: rotate(180deg);
  }

  /* ── Tabla interior ─── */
  .card .table tbody tr:last-child td { border-bottom: none; }
  .table thead th { border-top: none; }

  /* ── Foto thumb ─── */
  .foto-thumb:hover { opacity: .85; }
</style>
@endpush

@push('scripts')
<script>
// ── Expandir / Colapsar todos ────────────────────────────────────────────────
document.getElementById('btnExpandirTodos')?.addEventListener('click', () => {
  document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
    const target = document.querySelector(btn.dataset.bsTarget);
    if (target && !target.classList.contains('show')) {
      new bootstrap.Collapse(target, { toggle: false }).show();
    }
  });
});

document.getElementById('btnColapsarTodos')?.addEventListener('click', () => {
  document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
    const target = document.querySelector(btn.dataset.bsTarget);
    if (target && target.classList.contains('show')) {
      new bootstrap.Collapse(target, { toggle: false }).hide();
    }
  });
});

// ── Cambiar lotes por página ──────────────────────────────────────────────────
function cambiarPorPagina(n) {
  const url = new URL(window.location.href);
  url.searchParams.set('per_page', n);
  url.searchParams.set('page', 1);
  window.location.href = url.toString();
}

// ── Lightbox de foto ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const modal    = new bootstrap.Modal(document.getElementById('modalFoto'));
  const imgEl    = document.getElementById('modalFotoImg');
  const codigoEl = document.getElementById('modalFotoCodigo');

  document.querySelectorAll('.foto-thumb').forEach(img => {
    img.addEventListener('click', () => {
      imgEl.src = img.dataset.src;
      codigoEl.textContent = img.dataset.codigo;
      modal.show();
    });
  });
});
</script>
@endpush
