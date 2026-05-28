
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
      <div class="icon icon-shape icon-sm bg-gradient-primary shadow border-radius-md d-flex align-items-center justify-content-center me-2">
        <i class="fas fa-shoe-prints" style="color:#fff;font-size:.75rem"></i>
      </div>
      <span class="ms-1 font-weight-bold">PacaManager</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">

  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      {{-- Dashboard --}}
      <li class="nav-item">
        <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ url('dashboard') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('dashboard') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-tachometer-alt" style="color:{{ Request::is('dashboard') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      {{-- ── OPERACIONES ────────────────────────────────── --}}
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Operaciones</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('lotes*') ? 'active' : '' }}" href="{{ url('lotes') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('lotes*') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-layer-group" style="color:{{ Request::is('lotes*') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Lotes de compra</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('costales*') ? 'active' : '' }}" href="{{ url('costales') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('costales*') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-shopping-bag" style="color:{{ Request::is('costales*') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Todos los costales</span>
        </a>
      </li>

      {{-- ── INVENTARIO ─────────────────────────────────── --}}
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Inventario</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('inventario*') ? 'active' : '' }}" href="{{ url('inventario') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('inventario*') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-boxes" style="color:{{ Request::is('inventario*') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Ver Inventario</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('ventas*') ? 'active' : '' }}" href="{{ url('ventas') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('ventas*') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-cash-register" style="color:{{ Request::is('ventas*') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Registrar venta</span>
        </a>
      </li>

      {{-- ── GESTIÓN (solo dueño) ───────────────────────── --}}
      @if(auth()->user()?->isDueno())
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Gestión</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('sucursales*') ? 'active' : '' }}" href="{{ url('sucursales') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('sucursales*') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-store" style="color:{{ Request::is('sucursales*') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Sucursales</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('proveedores*') ? 'active' : '' }}" href="{{ url('proveedores') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('proveedores*') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-truck" style="color:{{ Request::is('proveedores*') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Proveedores</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}" href="{{ url('usuarios') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('usuarios*') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-users" style="color:{{ Request::is('usuarios*') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Usuarios</span>
        </a>
      </li>

      {{-- ── CONFIGURACIÓN (solo dueño) ─────────────────── --}}
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Configuración</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('configuracion*') ? 'active' : '' }}" href="{{ url('configuracion') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('configuracion*') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-cog" style="color:{{ Request::is('configuracion*') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Categorías y Tipos</span>
        </a>
      </li>
      @endif

      {{-- ── CUENTA ──────────────────────────────────────── --}}
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Mi cuenta</h6>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ Request::is('user-profile') ? 'active' : '' }}" href="{{ url('user-profile') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:{{ Request::is('user-profile') ? 'linear-gradient(310deg,#1a3c5e,#0ea5e9)' : 'rgba(30,96,145,.13)' }}">
            <i class="fas fa-user" style="color:{{ Request::is('user-profile') ? '#fff' : '#344767' }};font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Mi perfil</span>
        </a>
      </li>

      <li class="nav-item pb-3">
        <a class="nav-link" href="{{ url('logout') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
               style="background:rgba(30,96,145,.13)">
            <i class="fas fa-sign-out-alt" style="color:#344767;font-size:.75rem"></i>
          </div>
          <span class="nav-link-text ms-1">Cerrar sesión</span>
        </a>
      </li>

    </ul>
  </div>

</aside>
