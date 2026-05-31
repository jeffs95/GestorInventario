<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PacaManager — Iniciar sesión</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/soft-ui-dashboard.css?v=1.0.3">
  <link rel="stylesheet" href="/assets/css/paca-theme.css?v=1.0.3">

  <style>
    *, *::before, *::after { box-sizing: border-box; }

    html, body {
      height: 100%;
      margin: 0;
      font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #f8f9fa;
    }

    /* ── Layout split ─────────────────────────────────────── */
    .login-wrap {
      display: flex;
      min-height: 100vh;
    }

    /* ── Panel izquierdo — marca ──────────────────────────── */
    .brand-panel {
      flex: 0 0 42%;
      background: linear-gradient(160deg, #0d2e4a 0%, #1a3c5e 45%, #1d6fa4 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 48px 40px;
      position: relative;
      overflow: hidden;
    }

    /* Círculos decorativos de fondo */
    .brand-panel::before,
    .brand-panel::after {
      content: '';
      position: absolute;
      border-radius: 50%;
      background: rgba(255,255,255,.04);
    }
    .brand-panel::before {
      width: 380px; height: 380px;
      top: -100px; right: -120px;
    }
    .brand-panel::after {
      width: 260px; height: 260px;
      bottom: -60px; left: -80px;
    }

    .brand-logo {
      width: 72px; height: 72px;
      border-radius: 18px;
      background: rgba(255,255,255,.12);
      backdrop-filter: blur(4px);
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 20px;
      border: 1px solid rgba(255,255,255,.15);
    }
    .brand-logo i {
      font-size: 1.8rem;
      color: #fff;
    }
    .brand-name {
      font-size: 2rem;
      font-weight: 800;
      color: #fff;
      letter-spacing: -.02em;
      margin-bottom: 10px;
    }
    .brand-tagline {
      font-size: .9rem;
      color: rgba(255,255,255,.65);
      text-align: center;
      line-height: 1.6;
      max-width: 260px;
    }

    .brand-dots {
      display: flex; gap: 8px;
      margin-top: 36px;
    }
    .brand-dots span {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: rgba(255,255,255,.3);
    }
    .brand-dots span:first-child {
      background: rgba(255,255,255,.8);
      width: 24px;
      border-radius: 4px;
    }

    /* ── Panel derecho — formulario ───────────────────────── */
    .form-panel {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 24px;
      background: #fff;
    }

    .form-card {
      width: 100%;
      max-width: 400px;
    }

    .form-header {
      margin-bottom: 32px;
    }
    .form-header .mini-logo {
      width: 40px; height: 40px;
      border-radius: 10px;
      background: linear-gradient(310deg, #1a3c5e, #0ea5e9);
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 14px;
    }
    .form-header .mini-logo i { color: #fff; font-size: .85rem; }
    .form-header h2 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1a1f36;
      margin-bottom: 4px;
    }
    .form-header p {
      color: #8898aa;
      font-size: .875rem;
      margin: 0;
    }

    /* Campos */
    .field-group { margin-bottom: 20px; }
    .field-group label {
      display: block;
      font-size: .78rem;
      font-weight: 600;
      color: #525f7f;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: .04em;
    }
    .field-group .input-wrap {
      position: relative;
    }
    .field-group .input-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #adb5bd;
      font-size: .85rem;
      pointer-events: none;
    }
    .field-group input {
      width: 100%;
      height: 46px;
      padding: 0 14px 0 38px;
      border: 1.5px solid #e2e8f0;
      border-radius: 8px;
      font-size: .9rem;
      color: #344767;
      background: #f8fafc;
      outline: none;
      transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .field-group input:focus {
      border-color: #0ea5e9;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(14,165,233,.12);
    }
    .field-group input::placeholder { color: #b0bac9; }

    /* Toggle password */
    .toggle-pass {
      position: absolute;
      right: 12px; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      cursor: pointer; padding: 4px;
      color: #adb5bd;
      pointer-events: auto;
    }
    .toggle-pass:hover { color: #0ea5e9; }

    /* Error de validación */
    .field-error {
      font-size: .78rem;
      color: #f5365c;
      margin-top: 5px;
    }

    /* Remember me */
    .remember-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 24px;
    }
    .remember-row input[type=checkbox] {
      width: 16px; height: 16px;
      accent-color: #0ea5e9;
      cursor: pointer;
    }
    .remember-row label {
      font-size: .85rem;
      color: #525f7f;
      cursor: pointer;
      user-select: none;
    }

    /* Botón */
    .btn-login {
      width: 100%;
      height: 48px;
      border: none;
      border-radius: 8px;
      background: linear-gradient(310deg, #1a3c5e, #0ea5e9);
      color: #fff;
      font-size: .95rem;
      font-weight: 700;
      cursor: pointer;
      transition: opacity .2s, transform .1s;
      letter-spacing: .02em;
    }
    .btn-login:hover  { opacity: .9; }
    .btn-login:active { transform: scale(.98); }

    /* Link olvidé contraseña */
    .forgot-link {
      display: block;
      text-align: center;
      margin-top: 18px;
      font-size: .82rem;
      color: #8898aa;
      text-decoration: none;
    }
    .forgot-link:hover { color: #0ea5e9; }

    /* Alerta de error general */
    .alert-login {
      background: #fff5f5;
      border: 1px solid #fed7d7;
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 20px;
      font-size: .85rem;
      color: #c53030;
      display: flex;
      align-items: flex-start;
      gap: 8px;
    }
    .alert-login i { margin-top: 1px; flex-shrink: 0; }

    /* ── Responsive: ocultar panel de marca en móvil ──────── */
    @media (max-width: 768px) {
      .brand-panel { display: none; }
      .form-panel  { background: #f8f9fa; }
      .form-card   { max-width: 360px; }
    }
  </style>
</head>

<body>
<div class="login-wrap">

  {{-- ── Panel de marca (izquierdo) ──────────────────────── --}}
  <div class="brand-panel">
    <div class="brand-logo">
      <i class="fas fa-shoe-prints"></i>
    </div>
    <div class="brand-name">PacaManager</div>
    <p class="brand-tagline">
      Control de inventario y ventas para negocios de pacas de calzado
    </p>
    <div class="brand-dots">
      <span></span><span></span><span></span>
    </div>
  </div>

  {{-- ── Panel del formulario (derecho) ──────────────────── --}}
  <div class="form-panel">
    <div class="form-card">

      <div class="form-header">
        <div class="mini-logo">
          <i class="fas fa-shoe-prints"></i>
        </div>
        <h2>Bienvenido de nuevo</h2>
        <p>Ingresa tus credenciales para continuar</p>
      </div>

      {{-- Errores generales --}}
      @if ($errors->has('email') || $errors->has('password'))
      <div class="alert-login">
        <i class="fas fa-exclamation-circle"></i>
        <span>
          @if($errors->has('email'))
            {{ $errors->first('email') }}
          @else
            {{ $errors->first('password') }}
          @endif
        </span>
      </div>
      @endif

      <form method="POST" action="/session" id="formLogin">
        @csrf

        {{-- Email --}}
        <div class="field-group">
          <label for="email">Correo electrónico</label>
          <div class="input-wrap">
            <i class="fas fa-envelope"></i>
            <input type="email"
                   id="email"
                   name="email"
                   placeholder="tu@correo.com"
                   value="{{ old('email') }}"
                   autocomplete="email"
                   autofocus
                   required>
          </div>
        </div>

        {{-- Contraseña --}}
        <div class="field-group">
          <label for="password">Contraseña</label>
          <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password"
                   id="password"
                   name="password"
                   placeholder="••••••••"
                   autocomplete="current-password"
                   required>
            <button type="button" class="toggle-pass" id="btnTogglePass" tabindex="-1">
              <i class="fas fa-eye" id="iconPass"></i>
            </button>
          </div>
        </div>

        {{-- Recordarme --}}
        <div class="remember-row">
          <input type="checkbox" id="remember" name="remember" checked>
          <label for="remember">Recordar sesión</label>
        </div>

        <button type="submit" class="btn-login">
          Iniciar sesión
        </button>

      </form>

      <a href="/login/forgot-password" class="forgot-link">
        ¿Olvidaste tu contraseña?
      </a>

    </div>
  </div>

</div>

<script src="/assets/js/core/bootstrap.min.js"></script>
<script>
  // Toggle mostrar/ocultar contraseña
  document.getElementById('btnTogglePass').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon  = document.getElementById('iconPass');
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'fas fa-eye-slash';
    } else {
      input.type = 'password';
      icon.className = 'fas fa-eye';
    }
  });
</script>
</body>
</html>
