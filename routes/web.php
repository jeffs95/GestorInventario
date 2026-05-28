<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CostalesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\AperturasController;
use App\Http\Controllers\PreparacionController;
use App\Http\Controllers\LotesController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\SucursalesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\VentasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas autenticadas
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Perfil del usuario autenticado
    Route::get('/user-profile', [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/logout', [SessionsController::class, 'destroy']);

    // ── Inventario (todos los roles) ────────────────────────────────────
    Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('inventario/{zapato}/foto', [InventarioController::class, 'foto'])->name('inventario.foto');

    // ── Ventas / Punto de venta ─────────────────────────────────────────
    Route::get('ventas', [VentasController::class, 'index'])->name('ventas.index');
    Route::post('ventas', [VentasController::class, 'store'])->name('ventas.store');

    // ── Lotes de compra ─────────────────────────────────────────────────
    Route::get('lotes', [LotesController::class, 'index'])->name('lotes.index');
    Route::get('lotes/create', [LotesController::class, 'create'])->name('lotes.create')->middleware('role:dueno');
    Route::post('lotes', [LotesController::class, 'store'])->name('lotes.store')->middleware('role:dueno');
    Route::get('lotes/{lote}', [LotesController::class, 'show'])->name('lotes.show');

    // ── Preparación de zapatos de primera ───────────────────────────────
    Route::post('preparacion/{zapato_lote}/iniciar', [PreparacionController::class, 'iniciar'])->name('preparacion.iniciar');
    Route::get('preparacion/{zapato_lote}/barcodes', [PreparacionController::class, 'barcodes'])->name('preparacion.barcodes');
    Route::get('preparacion/{zapato_lote}', [PreparacionController::class, 'show'])->name('preparacion.show');
    Route::post('preparacion/{zapato_lote}', [PreparacionController::class, 'store'])->name('preparacion.store');

    // ── Aperturas de costales ────────────────────────────────────────────
    Route::get('aperturas/create', [AperturasController::class, 'create'])->name('aperturas.create');
    Route::post('aperturas', [AperturasController::class, 'store'])->name('aperturas.store');
    Route::get('aperturas/{apertura}', [AperturasController::class, 'show'])->name('aperturas.show');
    Route::post('aperturas/{apertura}/clasificar', [AperturasController::class, 'clasificar'])->name('aperturas.clasificar');
    Route::post('aperturas/{apertura}/cerrar', [AperturasController::class, 'cerrar'])->name('aperturas.cerrar');

    // ── Costales ────────────────────────────────────────────────────────
    Route::get('costales', [CostalesController::class, 'index'])->name('costales.index');
    // create/store van ANTES del parámetro {costal} para evitar colisión
    Route::get('costales/create', [CostalesController::class, 'create'])->name('costales.create')->middleware('role:dueno');
    Route::post('costales', [CostalesController::class, 'store'])->name('costales.store')->middleware('role:dueno');
    Route::get('costales/{costal}', [CostalesController::class, 'show'])->name('costales.show');
    Route::post('costales/{costal}/clasificar', [CostalesController::class, 'clasificar'])->name('costales.clasificar');
    Route::post('costales/{costal}/cerrar-clasificacion', [CostalesController::class, 'cerrarClasificacion'])->name('costales.cerrarClasificacion');

    // ── Solo dueño ───────────────────────────────────────────────────────
    Route::group(['middleware' => 'role:dueno'], function () {

        // Sucursales
        Route::resource('sucursales', SucursalesController::class)
            ->parameters(['sucursales' => 'sucursal']);

        // Proveedores
        Route::resource('proveedores', ProveedoresController::class)
            ->parameters(['proveedores' => 'proveedor']);

        // Usuarios
        Route::resource('usuarios', UsuariosController::class)
            ->parameters(['usuarios' => 'usuario']);

        // Configuración (categorías y tipos)
        Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::post('configuracion/categorias', [ConfiguracionController::class, 'storeCategoria'])->name('configuracion.storeCategoria');
        Route::delete('configuracion/categorias/{categoria}', [ConfiguracionController::class, 'destroyCategoria'])->name('configuracion.destroyCategoria');
        Route::post('configuracion/tipos', [ConfiguracionController::class, 'storeTipo'])->name('configuracion.storeTipo');
        Route::delete('configuracion/tipos/{tipo}', [ConfiguracionController::class, 'destroyTipo'])->name('configuracion.destroyTipo');
        Route::post('configuracion/tallas', [ConfiguracionController::class, 'storeTalla'])->name('configuracion.storeTalla');
        Route::delete('configuracion/tallas/{talla}', [ConfiguracionController::class, 'destroyTalla'])->name('configuracion.destroyTalla');
    });

    // Páginas de la plantilla (mantenidas)
    Route::get('profile', fn () => view('profile'))->name('profile');
    Route::get('billing', fn () => view('billing'))->name('billing');
    Route::get('rtl', fn () => view('rtl'))->name('rtl');
    Route::get('tables', fn () => view('tables'))->name('tables');
    Route::get('virtual-reality', fn () => view('virtual-reality'))->name('virtual-reality');
    Route::get('static-sign-in', fn () => view('static-sign-in'))->name('sign-in');
    Route::get('static-sign-up', fn () => view('static-sign-up'))->name('sign-up');
    Route::get('user-management', fn () => view('laravel-examples/user-management'))->name('user-management');
    Route::get('/login', fn () => view('dashboard'))->name('sign-up');
});

/*
|--------------------------------------------------------------------------
| Rutas invitado
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
    Route::get('/login/forgot-password', [ResetController::class, 'create']);
    Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/login', fn () => view('session/login-session'))->name('login');
