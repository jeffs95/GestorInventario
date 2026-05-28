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
  <!-- Toastify -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
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

  <!-- Toastify -->
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
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
