<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PacaManager — Iniciar sesión</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      height: 100%;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: #0b1e35;
    }

    /* ═══════════════════════════════════════════════
       LAYOUT
    ═══════════════════════════════════════════════ */
    .page {
      display: flex;
      min-height: 100vh;
    }

    /* ═══════════════════════════════════════════════
       PANEL IZQUIERDO — MARCA
    ═══════════════════════════════════════════════ */
    .brand {
      flex: 0 0 52%;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      padding: 48px 56px;

      /* Fondo: gradiente azul marino rico */
      background:
        radial-gradient(ellipse 80% 60% at 10% 20%, rgba(14,165,233,.18) 0%, transparent 60%),
        radial-gradient(ellipse 60% 80% at 90% 80%, rgba(99,102,241,.14) 0%, transparent 55%),
        linear-gradient(160deg, #0b1e35 0%, #0f2d4a 50%, #0e2640 100%);
    }

    /* Malla de puntos decorativa */
    .brand::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image:
        radial-gradient(circle, rgba(255,255,255,.06) 1px, transparent 1px);
      background-size: 28px 28px;
      pointer-events: none;
    }

    /* Círculo decorativo superior derecho */
    .brand::after {
      content: '';
      position: absolute;
      width: 500px; height: 500px;
      border-radius: 50%;
      border: 1px solid rgba(255,255,255,.05);
      top: -180px; right: -180px;
      pointer-events: none;
    }

    /* Logo + nombre */
    .brand-header {
      display: flex;
      align-items: center;
      gap: 12px;
      position: relative;
      z-index: 1;
    }
    .brand-icon {
      width: 44px; height: 44px;
      border-radius: 12px;
      background: linear-gradient(135deg, #0ea5e9, #6366f1);
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 14px rgba(14,165,233,.4);
    }
    .brand-icon i { color: #fff; font-size: 1.1rem; }
    .brand-wordmark {
      font-size: 1.25rem;
      font-weight: 800;
      color: #fff;
      letter-spacing: -.03em;
    }

    /* Contenido central */
    .brand-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      z-index: 1;
      padding: 40px 0 20px;
    }
    .brand-eyebrow {
      font-size: .72rem;
      font-weight: 600;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: #38bdf8;
      margin-bottom: 16px;
    }
    .brand-headline {
      font-size: 2.6rem;
      font-weight: 800;
      color: #fff;
      line-height: 1.18;
      letter-spacing: -.04em;
      margin-bottom: 18px;
    }
    .brand-headline em {
      font-style: normal;
      background: linear-gradient(90deg, #38bdf8, #818cf8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .brand-sub {
      font-size: .9rem;
      color: rgba(255,255,255,.5);
      line-height: 1.7;
      max-width: 340px;
      margin-bottom: 40px;
    }

    /* Tarjetas de stats flotantes */
    .stats-row {
      display: flex;
      gap: 12px;
    }
    .stat-card {
      flex: 1;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.09);
      border-radius: 14px;
      padding: 16px 18px;
      backdrop-filter: blur(8px);
      transition: background .2s;
    }
    .stat-card:hover { background: rgba(255,255,255,.09); }
    .stat-card .sc-label {
      font-size: .7rem;
      font-weight: 500;
      color: rgba(255,255,255,.4);
      text-transform: uppercase;
      letter-spacing: .08em;
      margin-bottom: 6px;
    }
    .stat-card .sc-value {
      font-size: 1.5rem;
      font-weight: 800;
      color: #fff;
      line-height: 1;
      letter-spacing: -.03em;
    }
    .stat-card .sc-icon {
      font-size: .75rem;
      color: #38bdf8;
      margin-top: 8px;
    }

    /* Features list */
    .brand-features {
      margin-top: 36px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      position: relative;
      z-index: 1;
    }
    .feat-item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: .82rem;
      color: rgba(255,255,255,.55);
    }
    .feat-item .fi-dot {
      width: 20px; height: 20px;
      border-radius: 6px;
      background: rgba(14,165,233,.2);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .feat-item .fi-dot i { font-size: .62rem; color: #38bdf8; }

    /* ═══════════════════════════════════════════════
       PANEL DERECHO — FORMULARIO
    ═══════════════════════════════════════════════ */
    .form-side {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 24px;
      position: relative;
      overflow: hidden;

      /* Fondo: gradiente suave azul/índigo */
      background: linear-gradient(145deg, #e8f4fd 0%, #eef0fb 50%, #f0f4f8 100%);
    }

    /* Blob superior derecho */
    .form-side::before {
      content: '';
      position: absolute;
      width: 480px; height: 480px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(14,165,233,.13) 0%, transparent 70%);
      top: -160px; right: -160px;
      pointer-events: none;
    }

    /* Blob inferior izquierdo */
    .form-side::after {
      content: '';
      position: absolute;
      width: 380px; height: 380px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(99,102,241,.10) 0%, transparent 70%);
      bottom: -120px; left: -100px;
      pointer-events: none;
    }

    /* Blob central difuso extra */
    .form-blob {
      position: absolute;
      width: 300px; height: 300px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(56,189,248,.08) 0%, transparent 70%);
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
    }

    .form-card {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 420px;
      background: #fff;
      border-radius: 22px;
      overflow: hidden;
      box-shadow:
        0 2px 4px rgba(15,23,42,.04),
        0 12px 32px rgba(15,23,42,.08),
        0 40px 80px rgba(15,23,42,.07);
    }

    /* Franja de color superior */
    .fc-accent {
      height: 5px;
      background: linear-gradient(90deg, #0b2d4e 0%, #0ea5e9 50%, #6366f1 100%);
    }

    /* Padding interior de la card */
    .fc-body {
      padding: 36px 38px 32px;
    }

    /* Header del form */
    .fc-header { margin-bottom: 28px; }
    .fc-logo {
      width: 52px; height: 52px;
      border-radius: 14px;
      background: linear-gradient(135deg, #0b2d4e, #0ea5e9);
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 18px;
      box-shadow: 0 4px 14px rgba(14,165,233,.35);
    }
    .fc-logo i { color: #fff; font-size: 1rem; }
    .fc-header h2 {
      font-size: 1.6rem;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -.04em;
      margin-bottom: 5px;
    }
    .fc-header p {
      font-size: .875rem;
      color: #94a3b8;
      line-height: 1.5;
    }

    /* Alerta de error */
    .alert-err {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      background: #fff1f2;
      border: 1px solid #fecdd3;
      border-radius: 10px;
      padding: 12px 14px;
      margin-bottom: 24px;
      animation: shake .4s ease;
    }
    .alert-err .ae-icon {
      width: 22px; height: 22px;
      border-radius: 50%;
      background: #fee2e2;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; margin-top: 1px;
    }
    .alert-err .ae-icon i { font-size: .65rem; color: #ef4444; }
    .alert-err span {
      font-size: .825rem;
      color: #b91c1c;
      line-height: 1.5;
    }
    @keyframes shake {
      0%,100%{ transform:translateX(0) }
      20%    { transform:translateX(-5px) }
      40%    { transform:translateX(5px) }
      60%    { transform:translateX(-4px) }
      80%    { transform:translateX(4px) }
    }

    /* ── Campos ─────────────────────────────────── */
    .field { margin-bottom: 18px; }
    .field label {
      display: block;
      font-size: .72rem;
      font-weight: 600;
      color: #64748b;
      margin-bottom: 7px;
      text-transform: uppercase;
      letter-spacing: .07em;
    }
    .input-box {
      position: relative;
    }
    .input-box .ib-icon {
      position: absolute;
      left: 14px; top: 50%;
      transform: translateY(-50%);
      width: 20px; height: 20px;
      border-radius: 6px;
      background: #f1f5f9;
      display: flex; align-items: center; justify-content: center;
      pointer-events: none;
      transition: background .2s;
    }
    .input-box .ib-icon i { font-size: .65rem; color: #94a3b8; }
    .input-box input {
      width: 100%;
      height: 48px;
      padding: 0 44px 0 42px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      font-size: .9rem;
      font-family: inherit;
      color: #0f172a;
      background: #f8fafc;
      outline: none;
      transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .input-box input::placeholder { color: #cbd5e1; }
    .input-box input:focus {
      border-color: #0ea5e9;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(14,165,233,.10);
    }
    .input-box input:focus ~ .ib-icon,
    .input-box:focus-within .ib-icon {
      background: rgba(14,165,233,.1);
    }
    .input-box:focus-within .ib-icon i { color: #0ea5e9; }

    /* Toggle contraseña */
    .toggle-pw {
      position: absolute;
      right: 12px; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      cursor: pointer; padding: 6px;
      color: #cbd5e1;
      line-height: 1;
      border-radius: 6px;
      transition: color .2s, background .2s;
    }
    .toggle-pw:hover { color: #0ea5e9; background: rgba(14,165,233,.08); }
    .toggle-pw i { font-size: .8rem; }

    /* Remember row */
    .remember-row {
      display: flex;
      align-items: center;
      gap: 9px;
      margin-bottom: 24px;
    }
    .custom-check {
      appearance: none;
      width: 18px; height: 18px;
      border: 1.5px solid #cbd5e1;
      border-radius: 5px;
      background: #f8fafc;
      cursor: pointer;
      position: relative;
      flex-shrink: 0;
      transition: border-color .2s, background .2s;
    }
    .custom-check:checked {
      background: #0ea5e9;
      border-color: #0ea5e9;
    }
    .custom-check:checked::after {
      content: '';
      position: absolute;
      left: 4px; top: 1px;
      width: 6px; height: 10px;
      border: 2px solid #fff;
      border-top: none; border-left: none;
      transform: rotate(45deg);
    }
    .remember-row label {
      font-size: .845rem;
      color: #64748b;
      cursor: pointer;
      user-select: none;
    }

    /* Botón submit */
    .btn-submit {
      width: 100%;
      height: 50px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg, #0b2d4e 0%, #0ea5e9 100%);
      color: #fff;
      font-size: .92rem;
      font-weight: 700;
      font-family: inherit;
      letter-spacing: .02em;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      position: relative;
      overflow: hidden;
      transition: transform .15s, box-shadow .2s, opacity .2s;
      box-shadow: 0 4px 14px rgba(14,165,233,.35);
    }
    .btn-submit::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,0);
      transition: background .2s;
    }
    .btn-submit:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(14,165,233,.45);
    }
    .btn-submit:hover::before { background: rgba(255,255,255,.06); }
    .btn-submit:active { transform: translateY(0) scale(.99); }
    .btn-submit i { font-size: .85rem; }

    /* Loading state */
    .btn-submit.loading { opacity: .75; pointer-events: none; }
    .btn-submit .spinner {
      display: none;
      width: 16px; height: 16px;
      border: 2px solid rgba(255,255,255,.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .7s linear infinite;
    }
    .btn-submit.loading .spinner { display: block; }
    .btn-submit.loading .btn-text { display: none; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Divider + link */
    .fc-footer {
      margin-top: 20px;
      text-align: center;
    }
    .forgot-link {
      font-size: .825rem;
      color: #94a3b8;
      text-decoration: none;
      transition: color .2s;
    }
    .forgot-link:hover { color: #0ea5e9; }
    .forgot-link i { font-size: .7rem; margin-right: 4px; }

    /* ═══════════════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════════════ */
    @media (max-width: 900px) {
      .brand { flex: 0 0 44%; padding: 40px 36px; }
      .brand-headline { font-size: 2rem; }
      .stats-row { flex-direction: column; }
    }
    @media (max-width: 680px) {
      .brand { display: none; }
      .form-side {
        background: #0b1e35;
      }
      .form-side::before,
      .form-side::after,
      .form-blob { display: none; }
      .form-card {
        background: #0f2540;
        box-shadow: none;
        border: 1px solid rgba(255,255,255,.08);
      }
      .fc-body { padding: 28px 24px 24px; }
      .fc-header h2 { color: #f1f5f9; }
      .fc-header p  { color: #64748b; }
      .field label  { color: #94a3b8; }
      .input-box input {
        background: rgba(255,255,255,.06);
        border-color: rgba(255,255,255,.1);
        color: #f1f5f9;
      }
      .input-box input:focus {
        background: rgba(255,255,255,.08);
        border-color: #0ea5e9;
      }
      .input-box input::placeholder { color: rgba(255,255,255,.2); }
      .remember-row label { color: #94a3b8; }
      .custom-check { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.15); }
      .forgot-link { color: #64748b; }
    }
  </style>
</head>

<body>
<div class="page">

  {{-- ══════════════════════════════════════════
       PANEL IZQUIERDO — MARCA
  ══════════════════════════════════════════ --}}
  <div class="brand">

    {{-- Logo --}}
    <div class="brand-header">
      <div class="brand-icon">
        <i class="fas fa-shoe-prints"></i>
      </div>
      <span class="brand-wordmark">PacaManager</span>
    </div>

    {{-- Cuerpo central --}}
    <div class="brand-body">
      <p class="brand-eyebrow">Sistema de gestión</p>
      <h1 class="brand-headline">
        Controla tu<br>
        <em>inventario</em><br>
        al instante
      </h1>
      <p class="brand-sub">
        Gestiona lotes, aperturas, ventas y devoluciones de pacas de calzado desde un solo panel.
      </p>

      {{-- Stat cards --}}
      <div class="stats-row">
        <div class="stat-card">
          <div class="sc-label">Zapatos</div>
          <div class="sc-value">∞</div>
          <div class="sc-icon"><i class="fas fa-boxes"></i> en inventario</div>
        </div>
        <div class="stat-card">
          <div class="sc-label">Ventas</div>
          <div class="sc-value">24/7</div>
          <div class="sc-icon"><i class="fas fa-bolt"></i> historial</div>
        </div>
        <div class="stat-card">
          <div class="sc-label">Reportes</div>
          <div class="sc-value">Real</div>
          <div class="sc-icon"><i class="fas fa-chart-line"></i> en tiempo real</div>
        </div>
      </div>
    </div>

    {{-- Features --}}
    <div class="brand-features">
      <div class="feat-item">
        <div class="fi-dot"><i class="fas fa-check"></i></div>
        Apertura de pacas con trazabilidad completa
      </div>
      <div class="feat-item">
        <div class="fi-dot"><i class="fas fa-check"></i></div>
        Ventas y devoluciones con comprobante
      </div>
      <div class="feat-item">
        <div class="fi-dot"><i class="fas fa-check"></i></div>
        Rentabilidad por lote en tiempo real
      </div>
    </div>

  </div>

  {{-- ══════════════════════════════════════════
       PANEL DERECHO — FORMULARIO
  ══════════════════════════════════════════ --}}
  <div class="form-side">
    <div class="form-blob"></div>
    <div class="form-card">

      {{-- Franja accent superior --}}
      <div class="fc-accent"></div>

      <div class="fc-body">

      {{-- Header --}}
      <div class="fc-header">
        <div class="fc-logo">
          <i class="fas fa-shoe-prints"></i>
        </div>
        <h2>Bienvenido de nuevo</h2>
        <p>Ingresa tus credenciales para acceder al panel</p>
      </div>

      {{-- Error --}}
      @if ($errors->has('email') || $errors->has('password'))
      <div class="alert-err">
        <div class="ae-icon"><i class="fas fa-exclamation"></i></div>
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
        <div class="field">
          <label for="email">Correo electrónico</label>
          <div class="input-box">
            <input type="email"
                   id="email"
                   name="email"
                   placeholder="tu@correo.com"
                   value="{{ old('email') }}"
                   autocomplete="email"
                   autofocus
                   required>
            <span class="ib-icon"><i class="fas fa-envelope"></i></span>
          </div>
        </div>

        {{-- Contraseña --}}
        <div class="field">
          <label for="password">Contraseña</label>
          <div class="input-box">
            <input type="password"
                   id="password"
                   name="password"
                   placeholder="••••••••"
                   autocomplete="current-password"
                   required>
            <span class="ib-icon"><i class="fas fa-lock"></i></span>
            <button type="button" class="toggle-pw" id="btnTogglePw" tabindex="-1">
              <i class="fas fa-eye" id="iconPw"></i>
            </button>
          </div>
        </div>

        {{-- Recordarme --}}
        <div class="remember-row">
          <input class="custom-check" type="checkbox" id="remember" name="remember" checked>
          <label for="remember">Recordar sesión</label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-submit" id="btnLogin">
          <span class="btn-text"><i class="fas fa-arrow-right-to-bracket"></i> Iniciar sesión</span>
          <div class="spinner"></div>
        </button>

      </form>

      <div class="fc-footer">
        <a href="/login/forgot-password" class="forgot-link">
          <i class="fas fa-key"></i> ¿Olvidaste tu contraseña?
        </a>
      </div>

      </div>{{-- /.fc-body --}}
    </div>
  </div>

</div>

<script src="/assets/js/core/bootstrap.min.js"></script>
<script>
  // Toggle contraseña
  document.getElementById('btnTogglePw').addEventListener('click', function () {
    const inp  = document.getElementById('password');
    const icon = document.getElementById('iconPw');
    const show = inp.type === 'password';
    inp.type   = show ? 'text' : 'password';
    icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
  });

  // Loading state al enviar
  document.getElementById('formLogin').addEventListener('submit', function () {
    document.getElementById('btnLogin').classList.add('loading');
  });
</script>
</body>
</html>
