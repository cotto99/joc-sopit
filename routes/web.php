<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Cliente\TicketController as ClienteTicketController;
use App\Http\Controllers\Admin\SucursalController;
use App\Http\Controllers\Admin\ContactoController;
use App\Http\Controllers\Admin\TecnicoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\FacturaController;

Route::get('/', fn() => redirect()->route('login'));


// ======================
// ===== ADMIN =====
// ======================

Route::prefix('admin')
    ->middleware(['auth', 'verified', 'rol:admin,tecnico'])
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // ===== TECNICOS =====
    Route::resource('tecnicos', TecnicoController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names('admin.tecnicos');

    // ===== CATEGORIAS =====
    Route::resource('categorias', CategoriaController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names('admin.categorias');

    // ===== FACTURAS =====
    Route::resource('facturas', FacturaController::class)
        ->only(['index', 'show'])
        ->names('admin.facturas');

        Route::resource('admin/facturas', FacturaController::class);
    Route::patch('/facturas/{factura}/estado',
        [FacturaController::class, 'actualizarEstado'])
        ->name('admin.facturas.estado');

    // ===== SUCURSALES =====
    Route::get('/sucursales', [SucursalController::class, 'index'])
        ->name('admin.sucursales.index');

    Route::post('/sucursales', [SucursalController::class, 'store'])
        ->name('admin.sucursales.store');

    Route::put('/sucursales/{sucursal}', [SucursalController::class, 'update'])
        ->name('admin.sucursales.update');

    Route::delete('/sucursales/{sucursal}', [SucursalController::class, 'destroy'])
        ->name('admin.sucursales.destroy');

    // ===== CONTACTOS =====
    Route::get('/contactos', [ContactoController::class, 'index'])
        ->name('admin.contactos.index');

    Route::post('/contactos', [ContactoController::class, 'store'])
        ->name('admin.contactos.store');

    Route::put('/contactos/{contacto}', [ContactoController::class, 'update'])
        ->name('admin.contactos.update');

    Route::delete('/contactos/{contacto}', [ContactoController::class, 'destroy'])
        ->name('admin.contactos.destroy');

    // ===== EMPRESAS =====
    Route::resource('empresas', EmpresaController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names('admin.empresas');

    // ===== TICKETS (CORREGIDO AQUÍ 👇) =====
    Route::resource('tickets', AdminTicketController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names('admin.tickets');

    // Acciones especiales de tickets
    Route::patch('/tickets/{ticket}/asignar',
        [AdminTicketController::class, 'asignar'])
        ->name('admin.tickets.asignar');

        Route::post('/tickets/{ticket}/estado',    [AdminTicketController::class, 'cambiarEstado'])->name('admin.tickets.estado');


        Route::post('/tickets/{ticket}/comentario',[AdminTicketController::class, 'agregarComentario'])->name('admin.tickets.comentario');


    Route::post('/tickets/{ticket}/cargo',
        [AdminTicketController::class, 'agregarCargo'])
        ->name('admin.tickets.cargo');

    Route::delete('/tickets/cargo/{cargo}',
        [AdminTicketController::class, 'eliminarCargo'])
        ->name('admin.tickets.cargo.eliminar');

    Route::post('/tickets/{ticket}/factura',
        [AdminTicketController::class, 'generarFactura'])
        ->name('admin.tickets.factura');

    // Relación empresa → sucursales y contactos
    Route::post('/empresas/{empresa}/sucursales',
        [SucursalController::class, 'store'])
        ->name('admin.empresas.sucursales.store');

    Route::post('/empresas/{empresa}/contactos',
        [ContactoController::class, 'store'])
        ->name('admin.empresas.contactos.store');
});


// ======================
// ===== CLIENTE =====
// ======================

Route::prefix('cliente')
    ->middleware(['auth', 'verified', 'rol:cliente'])
    ->group(function () {

    Route::get('/tickets',
        [ClienteTicketController::class, 'index'])
        ->name('cliente.tickets.index');

    Route::get('/tickets/crear',
        [ClienteTicketController::class, 'create'])
        ->name('cliente.tickets.create');

    Route::post('/tickets',
        [ClienteTicketController::class, 'store'])
        ->name('cliente.tickets.store');

    Route::get('/tickets/{ticket}',
        [ClienteTicketController::class, 'show'])
        ->name('cliente.tickets.show');

    Route::post('/tickets/{ticket}/comentario',
        [ClienteTicketController::class, 'agregarComentario'])
        ->name('cliente.tickets.comentario');
});


// ======================
// Redirect por rol
// ======================

Route::get('/dashboard', function () {
    return match(auth()->user()->rol) {
        'admin', 'tecnico' => redirect()->route('admin.dashboard'),
        'cliente'          => redirect()->route('cliente.tickets.index'),
        default            => redirect()->route('login'),
    };
})->middleware(['auth'])->name('dashboard');


require __DIR__.'/auth.php';