<!--
=========================================================
* Soft UI Dashboard - v1.0.3
=========================================================

* Product Page: https://www.creative-tim.com/product/soft-ui-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)

* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>

@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="en" >
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if (env('IS_DEMO'))
      <x-demo-metas></x-demo-metas>
  @endif

  <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/img/favicon.png">
  <title>PacaManager</title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome 6 — CDN CSS (no requiere Kit ni autenticación) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
  <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="/assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />
  <!-- PacaManager — Paleta Azul Océano -->
  <link rel="stylesheet" href="/assets/css/paca-theme.css?v=1.0.3">
  <style>
    .swal-soft-popup {
      border-radius: 1rem !important;
      font-family: 'Open Sans', sans-serif !important;
      padding: 1.5rem !important;
    }
    .swal2-icon { margin-bottom: 0.75rem !important; }
    .swal2-title { font-size: 1.1rem !important; font-weight: 700 !important; color: #344767 !important; }
    .swal2-html-container { font-size: 0.875rem !important; color: #67748e !important; }
    .swal2-actions { gap: 0.5rem !important; margin-top: 1.25rem !important; }
  </style>
  <!-- Hot Toast (react-hot-toast style) -->
  <style>
    #ht-container {
      position: fixed; top: 16px; left: 50%; transform: translateX(-50%);
      z-index: 99999; display: flex; flex-direction: column;
      gap: 8px; align-items: center; pointer-events: none;
    }
    .ht-toast {
      display: flex; align-items: center; gap: 10px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0,0,0,.12), 0 1px 3px rgba(0,0,0,.08);
      padding: 10px 16px 10px 12px;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      font-size: 14px; font-weight: 500; color: #363636; line-height: 1.4;
      max-width: 380px; min-width: 230px;
      pointer-events: auto; cursor: pointer;
      animation: ht-in .35s cubic-bezier(.21,1.02,.73,1) forwards;
      will-change: transform, opacity;
    }
    .ht-toast.ht-out {
      animation: ht-out .3s cubic-bezier(.06,.71,.55,1) forwards;
    }
    .ht-icon {
      width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 800; color: #fff;
    }
    .ht-success { background: #61d345; }
    .ht-error   { background: #ff4b4b; }
    .ht-warning { background: #f59e0b; }
    .ht-info    { background: #0ea5e9; }
    @keyframes ht-in {
      from { opacity: 0; transform: translateY(-14px) scale(.88); }
      to   { opacity: 1; transform: translateY(0)     scale(1);   }
    }
    @keyframes ht-out {
      from { opacity: 1; transform: translateY(0)      scale(1);   }
      to   { opacity: 0; transform: translateY(-10px)  scale(.88); }
    }
  </style>
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  @stack('styles')
</head>

<body class="g-sidenav-show  bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">
  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  <!--   Core JS Files   -->
  <script src="/assets/js/core/popper.min.js"></script>
  <script src="/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="/assets/js/plugins/fullcalendar.min.js"></script>
  <script src="/assets/js/plugins/chartjs.min.js"></script>
  @stack('rtl')
  @stack('dashboard')
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <!-- Hot Toast -->
  <script>
  (function () {
    const _c = document.createElement('div');
    _c.id = 'ht-container';
    document.body.appendChild(_c);

    const _icons = { success:'✓', error:'✕', warning:'!', info:'i' };

    function _show(msg, type, dur) {
      const t = document.createElement('div');
      t.className = 'ht-toast';
      t.innerHTML =
        `<span class="ht-icon ht-${type}">${_icons[type]}</span>` +
        `<span>${msg}</span>`;

      const _dismiss = () => {
        if (t._gone) return; t._gone = true;
        clearTimeout(t._tid);
        t.classList.add('ht-out');
        t.addEventListener('animationend', () => t.remove(), { once: true });
      };

      t.addEventListener('click', _dismiss);
      t.addEventListener('mouseenter', () => clearTimeout(t._tid));
      t.addEventListener('mouseleave', () => { t._tid = setTimeout(_dismiss, 1500); });

      _c.appendChild(t);
      t._tid = setTimeout(_dismiss, dur);
    }

    window.toast = {
      success: (m, d=4000) => _show(m, 'success', d),
      error:   (m, d=4000) => _show(m, 'error',   d),
      warning: (m, d=4000) => _show(m, 'warning', d),
      info:    (m, d=4000) => _show(m, 'info',    d),
    };
  })();
  </script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <script>
    // Configuración global de SweetAlert2
    const _swalBase = {
      customClass: {
        popup:         'swal-soft-popup',
        confirmButton: 'btn btn-sm px-4',
        cancelButton:  'btn btn-sm btn-outline-secondary px-4',
      },
      buttonsStyling: false,
      reverseButtons: true,
      focusCancel: true,
    };

    // Manejador global para formularios con data-confirm
    document.addEventListener('DOMContentLoaded', function () {
      document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-confirm]');
        if (!btn) return;
        e.preventDefault();

        Swal.fire({
          ..._swalBase,
          title:             btn.dataset.title   || '¿Estás seguro?',
          text:              btn.dataset.confirm,
          icon:              btn.dataset.icon     || 'warning',
          confirmButtonText: btn.dataset.ok       || 'Sí, continuar',
          cancelButtonText:  'Cancelar',
          showCancelButton:  true,
          customClass: {
            ..._swalBase.customClass,
            confirmButton: 'btn btn-sm px-4 ' + (btn.dataset.btnClass || 'btn-danger'),
          },
        }).then(r => {
          if (r.isConfirmed) {
            const form = btn.closest('form') || document.getElementById(btn.dataset.form);
            if (form) form.submit();
          }
        });
      });
    });
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="/assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script>
  @stack('scripts')
</body>

</html>
