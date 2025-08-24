<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ConfCorreoController;
use App\Http\Controllers\CorreoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ConfiguracionCredencialesController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\UserPersonalizacionController;



use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\SucursalController;


Route::get('/', function () {
    return redirect('/login');
});


Route::get('/reset-password', [ResetPassword::class, 'show'])->middleware('guest')->name('reset-password');
Route::post('/reset-password', [ResetPassword::class, 'send'])->middleware('guest')->name('reset.perform');
Route::get('/change-password', [ChangePassword::class, 'show'])->middleware('guest')->name('change-password');
Route::post('/change-password', [ChangePassword::class, 'update'])->middleware('guest')->name('change.perform');

//RUTAS PARA EJECUTAR ARTISAN EN PRODUCCION

Route::middleware(['auth', 'can:ejecutar-artisan'])->group(function () {

    Route::get('/artisan-panel', [ArtisanController::class, 'verificacion'])->name('artisan.admin');

    Route::post('/artisan-panel', [ArtisanController::class, 'index'])->name('artisan.verificar');

    Route::post('/artisan/run', [ArtisanController::class, 'run'])->name('artisan.run');
});

Route::post('/guardar-color-sidebar', [UserPersonalizacionController::class, 'guardarSidebarColor'])->middleware('auth');
Route::post('/user/personalizacion/sidebar-type', [UserPersonalizacionController::class, 'updateSidebarType'])->middleware('auth');
Route::post('/user/preferences', [UserPersonalizacionController::class, 'updateDark'])->middleware('auth');



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



Route::middleware(['auth', 'can:Administración de Usuarios'])->group(function () {

    Route::get('/usuarios', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('can:usuarios.ver');

    Route::get('/usuarios/crear', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware('can:usuarios.crear');

    Route::post('/usuarios', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('can:usuarios.crear');

    Route::get('/usuarios/{user}', [UserController::class, 'show'])
        ->name('users.show')
        ->middleware('can:usuarios.ver');

    Route::get('/usuarios/edit/{id}', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('can:usuarios.editar');

    Route::put('/usuarios/{id}/{perfil}', [UserController::class, 'update'])
        ->name('users.update');

    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('can:usuarios.eliminar');

    Route::get('/datos/usuario/{id}', [UserController::class, 'GetUsuario'])
        ->name('users.get')
        ->middleware('can:usuarios.ver');

    Route::get('/usuarios/exportar/excel', [UserController::class, 'exportExcel'])->name('usuarios.exportar_excel')->middleware(middleware: 'can:usuarios.exportar_excel');
    Route::get('/usuarios/exportar/pdf', [UserController::class, 'exportPDF'])->name('usuarios.exportar_pdf')->middleware('can:usuarios.exportar_pdf');


});



//Rutas para secciones
Route::resource('secciones', SeccionController::class)->except([
    'show',




])->middleware(['auth', 'role:admin']);

Route::post('/api/sugerir-icono', [SeccionController::class, 'SugerirIcono']);

Route::post('obtener/dato/menu', [SeccionController::class, 'cambiarSeccion'])->middleware(['auth', 'role:admin']);
//Rutas para Menus
Route::resource('menus', MenuController::class)->except([
    'show',
])->middleware(['auth', 'role:admin']);


// Rutas para la configuracion de correo

Route::middleware(['auth', 'can:Configuración'])->group(function () {

    Route::get('/configuracion/correo', [ConfCorreoController::class, 'index'])
        ->name('configuracion.correo.index')
        ->middleware('can:configuracion_correo.ver');

    Route::post('/configuracion/correo/guardar', [ConfCorreoController::class, 'store'])
        ->name('configuracion.correo.store')
        ->middleware('can:configuracion_correo.actualizar');
    Route::put('configuracion_correo', [ConfCorreoController::class, 'update'])->name('configuracion_correo.update')->middleware('can:configuracion_correo.actualizar');


    Route::get('/correo/prueba', [ConfCorreoController::class, 'enviarPrueba'])
        ->name('correo.prueba');

    Route::get('/correos/plantillas', [CorreoController::class, 'index'])
        ->name('correos.index')
        ->middleware('can:plantillas.ver');

    Route::put('/editar/plantilla/{id}', [CorreoController::class, 'update_plantilla'])
        ->name('plantilla.update')
        ->middleware('can:plantillas.actualizar');

    Route::get('/obtener/plantilla/{id}', [CorreoController::class, 'GetPlantilla'])
        ->name('obtener.correo');

});


//cambio de contraseña
Route::middleware(['auth'])->group(function () {

    Route::get('/usuario/contraseña', [PasswordController::class, 'ActualizarContraseña'])->name('user.actualizar.contraseña');
    Route::put('password/update', [PasswordController::class, 'update'])->name('password.actualizar');

    Route::get('/usuario/perfil', [UserController::class, 'Perfil'])
        ->name('perfil');
});



Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/roles', [RoleController::class, 'index'])
        ->name('roles.index')
        ->middleware('can:roles.inicio');

    Route::get('/roles/create', [RoleController::class, 'create'])
        ->name('roles.create')
        ->middleware('can:roles.crear');

    Route::post('/roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('can:roles.guardar');

    Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])
        ->name('roles.edit')
        ->middleware('can:roles.editar');

    Route::put('/roles/{id}', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('can:roles.actualizar');

    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('can:roles.eliminar');

    Route::get('/permissions', [PermissionController::class, 'index'])
        ->name('permissions.index')
        ->middleware('can:permisos.inicio');

    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->name('permissions.create')
        ->middleware('can:permisos.crear');

    Route::post('/permissions', [PermissionController::class, 'store'])
        ->name('permissions.store')
        ->middleware('can:permisos.guardar');

    Route::get('/permissions/edit/{id}', [PermissionController::class, 'edit'])
        ->name('permissions.edit')
        ->middleware('can:permisos.editar');

    Route::put('/permissions/{id}', [PermissionController::class, 'update'])
        ->name('permissions.update')
        ->middleware('can:permisos.actualizar');

    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])
        ->name('permissions.destroy')
        ->middleware('can:permisos.eliminar');

    Route::get('/permissions/cargar/menu/{id}/{rol_id}', [RoleController::class, 'get_permisos_menu'])
        ->name('permissions.menu');

});




//Rutas configuracion general

Route::middleware(['auth', 'role:admin', 'can:Configuración General'])->group(function () {

    Route::get('/admin/configuracion', [ConfiguracionController::class, 'edit'])
        ->name('admin.configuracion.edit')
        ->middleware('can:configuracion.inicio');

    Route::put('/admin/configuracion', [ConfiguracionController::class, 'update'])
        ->name('admin.configuracion.update')
        ->middleware('can:configuracion.actualizar');

});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/configuracion/credenciales', [ConfiguracionCredencialesController::class, 'index'])->name('configuracion.credenciales.index')->middleware('can:configuracion.credenciales_ver');
    Route::post('/configuracion/credenciales/actualizar', [ConfiguracionCredencialesController::class, 'actualizar'])->name('configuracion.credenciales.actualizar')->middleware('can:configuracion.credenciales_actualizar');

});


//doble factor de autenticacion
Route::get('/2fa/verify', [TwoFactorController::class, 'index'])->name('verify.index');
Route::post('/2fa/verify', [TwoFactorController::class, 'store'])->name('verify.store');
Route::post('/2fa/resend', [TwoFactorController::class, 'resend'])->name('verify.resend');

//Catalogo


Route::middleware(['auth'])->group(function () {


    Route::post('/secciones/ordenar', [SeccionController::class, 'ordenar'])->name('secciones.ordenar');

});
Route::middleware(['auth', 'can:Administración y Parametrización'])->group(function () {

    // Rutas para catalogos
    Route::get('/catalogos', [CatalogoController::class, 'index'])->name('catalogos.index')->middleware('can:catalogo.ver');
    Route::get('/catalogos/create', [CatalogoController::class, 'create'])->name('catalogos.create')->middleware('can:catalogo.crear');
    Route::post('/catalogos', [CatalogoController::class, 'store'])->name('catalogos.store')->middleware('can:catalogo.guardar');
    Route::get('/catalogos/{id}', [CatalogoController::class, 'show'])->name('catalogos.show')->middleware('can:catalogo.ver_detalle');
    Route::get('/catalogos/{id}/edit', [CatalogoController::class, 'edit'])->name('catalogos.edit')->middleware('can:catalogo.editar');
    Route::put('/catalogos/{id}', [CatalogoController::class, 'update'])->name('catalogos.update')->middleware('can:catalogo.actualizar');
    Route::delete('/catalogos/{id}', [CatalogoController::class, 'destroy'])->name('catalogos.destroy')->middleware('can:catalogo.eliminar');


});



//LOGICA DE NEGOCIO


//rutas para CATEGORIAS

Route::get('/admin/categorias/reporte', [App\Http\Controllers\CategoriaController::class, 'generarReporte'])
    ->name('admin.categorias.reporte');
Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index')->middleware('can:categoria.ver');

Route::get('/admin/categorias/create', [App\Http\Controllers\CategoriaController::class, 'create'])->name('admin.categorias.create')->middleware('auth');
Route::post('/admin/categorias/create', [App\Http\Controllers\CategoriaController::class, 'store'])->name('admin.categorias.store')->middleware('auth');
Route::get('/admin/categorias/{id}', [App\Http\Controllers\CategoriaController::class, 'show'])->name('admin.categorias.show')->middleware('auth');
Route::get('/admin/categorias/{id}/edit', [App\Http\Controllers\CategoriaController::class, 'edit'])->name('admin.categorias.edit')->middleware('auth');
Route::put('/admin/categorias/{id}', [App\Http\Controllers\CategoriaController::class, 'update'])->name('admin.categorias.update')->middleware('auth');
Route::delete('/admin/categorias/{id}', [App\Http\Controllers\CategoriaController::class, 'destroy'])->name('admin.categorias.destroy')->middleware('auth');





//RUTAS PARA LABORATORIOS
Route::get('/admin/laboratorios/reporte', [App\Http\Controllers\LaboratorioController::class, 'generarReporte'])
    ->name('admin.laboratorios.reporte');
Route::get('/admin/laboratorios', [App\Http\Controllers\LaboratorioController::class, 'index'])->name('admin.laboratorios.index')->middleware('auth');
Route::get('/admin/laboratorios/create', [App\Http\Controllers\LaboratorioController::class, 'create'])->name('admin.laboratorios.create')->middleware('auth');
Route::post('/admin/laboratorios/create', [App\Http\Controllers\LaboratorioController::class, 'store'])->name('admin.laboratorios.store')->middleware('auth');
Route::get('/admin/laboratorios/{id}', [App\Http\Controllers\LaboratorioController::class, 'show'])->name('admin.laboratorios.show')->middleware('auth');
Route::get('/admin/laboratorios/{id}/edit', [App\Http\Controllers\LaboratorioController::class, 'edit'])->name('admin.laboratorios.edit')->middleware('auth');
Route::put('/admin/laboratorios/{id}', [App\Http\Controllers\LaboratorioController::class, 'update'])->name('admin.laboratorios.update')->middleware('auth');
Route::delete('/admin/laboratorios/{id}', [App\Http\Controllers\LaboratorioController::class, 'destroy'])->name('admin.laboratorios.destroy')->middleware('auth');


//RUTAS PARA PRODUCTOS

Route::get('/admin/productos', [App\Http\Controllers\ProductoController::class, 'index'])->name('admin.productos.index')->middleware('auth');
Route::get('/admin/productos/create', [App\Http\Controllers\ProductoController::class, 'create'])->name('admin.productos.create')->middleware('auth');
Route::post('/admin/productos/create', [App\Http\Controllers\ProductoController::class, 'store'])->name('admin.productos.store')->middleware('auth');
Route::get('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'show'])->name('admin.productos.show')->middleware('auth');
Route::get('/admin/productos/{id}/edit', [App\Http\Controllers\ProductoController::class, 'edit'])->name('admin.productos.edit')->middleware('auth');
Route::put('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'update'])->name('admin.productos.update')->middleware('auth');
Route::delete('/admin/productos/{id}', [App\Http\Controllers\ProductoController::class, 'destroy'])->name('admin.productos.destroy')->middleware('auth');

Route::get('/admin/productos/buscar', [App\Http\Controllers\ProductoController::class, 'buscar'])->name('admin.productos.buscar');
Route::get('/admin/productos/get/{id}', [App\Http\Controllers\ProductoController::class, 'get'])->name('admin.productos.get');
Route::get('/admin/laboratorios/buscar', [App\Http\Controllers\LaboratorioController::class, 'buscar'])->name('admin.laboratorios.buscar');



Route::get('/admin/productos/reporte/{tipo}', [App\Http\Controllers\ProductoController::class, 'generarReporte'])
    ->where('tipo', 'pdf|excel|csv')
    ->name('admin.productos.reporte');


//RUTAS PARA PROVEEDOR
Route::get('/admin/proveedores/reporte', [App\Http\Controllers\ProveedorController::class, 'generarReporte'])
    ->name('admin.proveedores.reporte');
Route::get('/admin/proveedores', [App\Http\Controllers\ProveedorController::class, 'index'])->name('admin.proveedores.index')->middleware('auth');
Route::get('/admin/proveedores/create', [App\Http\Controllers\ProveedorController::class, 'create'])->name('admin.proveedores.create')->middleware('auth');
Route::post('/admin/proveedores/create', [App\Http\Controllers\ProveedorController::class, 'store'])->name('admin.proveedores.store')->middleware('auth');
Route::get('/admin/proveedores/{id}', [App\Http\Controllers\ProveedorController::class, 'show'])->name('admin.proveedores.show')->middleware('auth');
Route::get('/admin/proveedores/{id}/edit', [App\Http\Controllers\ProveedorController::class, 'edit'])->name('admin.proveedores.edit')->middleware('auth');
Route::put('/admin/proveedores/{id}', [App\Http\Controllers\ProveedorController::class, 'update'])->name('admin.proveedores.update')->middleware('auth');
Route::delete('/admin/proveedores/{id}', [App\Http\Controllers\ProveedorController::class, 'destroy'])->name('admin.proveedores.destroy')->middleware('auth');


//RUTAS PARA COMPRAS

Route::get('/admin/compras', [App\Http\Controllers\CompraController::class, 'index'])->name('admin.compras.index')->middleware('auth');
Route::get('/admin/compras/create', [App\Http\Controllers\CompraController::class, 'create'])->name('admin.compras.create')->middleware('auth');
Route::post('/admin/compras/create', [App\Http\Controllers\CompraController::class, 'store'])->name('admin.compras.store')->middleware('auth');
Route::get('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'show'])->name('admin.compras.show')->middleware('auth');
Route::get('/admin/compras/{id}/edit', [App\Http\Controllers\CompraController::class, 'edit'])->name('admin.compras.edit')->middleware('auth');
Route::put('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'update'])->name('admin.compras.update')->middleware('auth');
Route::delete('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'destroy'])->name('admin.compras.destroy')->middleware('auth');

Route::get('/admin/compras/reporte/{tipo}', [App\Http\Controllers\CompraController::class, 'reporte'])
    ->where('tipo', 'pdf|excel|csv')
    ->name('admin.compras.reporte');


Route::get('/admin/compras/pdf/{id}', [App\Http\Controllers\CompraController::class, 'pdf'])->name('admin.compras.pdf')->middleware('auth');
//tmp
Route::post('/admin/compras/create/tmp', [App\Http\Controllers\TmpCompraController::class, 'tmp_compras'])->name('admin.compras.tmp_compras')->middleware('auth');
Route::delete('/admin/compras/create/tmp/{id}', [App\Http\Controllers\TmpCompraController::class, 'destroy'])->name('admin.compras.tmp_compras.destroy')->middleware('auth');


//ruta para detalles de la compra
Route::post('/admin/compras/detalle/create', [App\Http\Controllers\DetalleCompraController::class, 'store'])->name('admin.detalle.compras.store')->middleware('auth');
Route::delete('/admin/compras/detalle/{id}', [App\Http\Controllers\DetalleCompraController::class, 'destroy'])->name('admin.detalle.compras.destroy')->middleware('auth');

Route::post('/compras/agregar-tmp', [App\Http\Controllers\CompraController::class, 'agregarTmp'])->name('admin.compras.agregar-tmp');
Route::post('/compras/eliminar-tmp', [App\Http\Controllers\CompraController::class, 'eliminarTmp'])->name('admin.compras.eliminar-tmp');
Route::post('/compras/actualizar-tmp', [App\Http\Controllers\CompraController::class, 'actualizarTmp'])->name('admin.compras.actualizar-tmp');



// routes/lote
Route::post('/admin/compras/agregar-lote', [App\Http\Controllers\CompraController::class, 'agregarLote'])->name('compras.agregarLote');

Route::get('/admin/compras/tmp', [App\Http\Controllers\CompraController::class, 'mostrarTmpCompras'])->name('compras.tmp');

//RUTAS PARA CLIENTES
Route::get('/admin/clientes/reporte', [App\Http\Controllers\ClienteController::class, 'generarReporte'])
    ->name('admin.clientes.reporte');
Route::get('/admin/clientes', [App\Http\Controllers\ClienteController::class, 'index'])->name('admin.clientes.index')->middleware('auth');
Route::get('/admin/clientes/create', [App\Http\Controllers\ClienteController::class, 'create'])->name('admin.clientes.create')->middleware('auth');
Route::post('/admin/clientes/create', [App\Http\Controllers\ClienteController::class, 'store'])->name('admin.clientes.store')->middleware('auth');
Route::get('/admin/clientes/{id}', [App\Http\Controllers\ClienteController::class, 'show'])->name('admin.clientes.show')->middleware('auth');
Route::get('/admin/clientes/{id}/edit', [App\Http\Controllers\ClienteController::class, 'edit'])->name('admin.clientes.edit')->middleware('auth');
Route::put('/admin/clientes/{id}', [App\Http\Controllers\ClienteController::class, 'update'])->name('admin.clientes.update')->middleware('auth');
Route::delete('/admin/clientes/{id}', [App\Http\Controllers\ClienteController::class, 'destroy'])->name('admin.clientes.destroy')->middleware('auth');

//RUTAS PARA VENTAS

Route::get('/admin/ventas', [App\Http\Controllers\VentaController::class, 'index'])->name('admin.ventas.index')->middleware('auth');
Route::get('/admin/ventas/create', [App\Http\Controllers\VentaController::class, 'create'])->name('admin.ventas.create')->middleware('auth');
Route::post('/admin/ventas/create', [App\Http\Controllers\VentaController::class, 'store'])->name('admin.ventas.store')->middleware('auth');
Route::get('/admin/ventas/{id}', [App\Http\Controllers\VentaController::class, 'show'])->name('admin.ventas.show')->middleware('auth');

Route::get('/admin/ventas/pdf/{id}', [App\Http\Controllers\VentaController::class, 'pdf'])->name('admin.ventas.pdf')->middleware('auth');

Route::get('/admin/ventas/{id}/edit', [App\Http\Controllers\VentaController::class, 'edit'])->name('admin.ventas.edit')->middleware('auth');
Route::put('/admin/ventas/{id}', [App\Http\Controllers\VentaController::class, 'update'])->name('admin.ventas.update')->middleware('auth');
Route::delete('/admin/ventas/{id}', [App\Http\Controllers\VentaController::class, 'destroy'])->name('admin.ventas.destroy')->middleware('auth');
//ruta para crear al cliente
Route::post('/admin/ventas/cliente/create', [App\Http\Controllers\VentaController::class, 'cliente_store'])->name('admin.ventas.cliente.store')->middleware('auth');


Route::get('/admin/ventas/reporte/{tipo}', [App\Http\Controllers\VentaController::class, 'reporte'])
    ->where('tipo', 'pdf|excel|csv')
    ->name('admin.ventas.reporte');

//tmp ventas
Route::post('/admin/ventas/create/tmp', [App\Http\Controllers\TmpVentaController::class, 'tmp_ventas'])->name('admin.ventas.tmp_ventas')->middleware('auth');
Route::delete('/admin/ventas/create/tmp/{id}', [App\Http\Controllers\TmpVentaController::class, 'destroy'])->name('admin.ventas.tmp_ventas.destroy')->middleware('auth');


//ruta para detalles de la ventas
Route::post('/admin/ventas/detalle/create', [App\Http\Controllers\DetalleVentaController::class, 'store'])->name('admin.detalle.ventas.store')->middleware('auth');
Route::delete('/admin/ventas/detalle/{id}', [App\Http\Controllers\DetalleVentaController::class, 'destroy'])->name('admin.detalle.ventas.destroy')->middleware('auth');

//RUTAS PARA CAJA

Route::get('/admin/cajas', [App\Http\Controllers\CajaController::class, 'index'])->name('admin.cajas.index')->middleware('auth');
Route::get('/admin/cajas/create', [App\Http\Controllers\CajaController::class, 'create'])->name('admin.cajas.create')->middleware('auth');
Route::post('/admin/cajas/create', [App\Http\Controllers\CajaController::class, 'store'])->name('admin.cajas.store')->middleware('auth');
Route::get('/admin/cajas/{id}', [App\Http\Controllers\CajaController::class, 'show'])->name('admin.cajas.show')->middleware('auth');
Route::get('/admin/cajas/{id}/edit', [App\Http\Controllers\CajaController::class, 'edit'])->name('admin.cajas.edit')->middleware('auth');
Route::put('/admin/cajas/{id}', [App\Http\Controllers\CajaController::class, 'update'])->name('admin.cajas.update')->middleware('auth');
Route::delete('/admin/cajas/{id}', [App\Http\Controllers\CajaController::class, 'destroy'])->name('admin.cajas.destroy')->middleware('auth');

Route::get('/admin/cajas/reporte/{tipo}', [App\Http\Controllers\CajaController::class, 'reportecaja'])
    ->where('tipo', 'pdf|excel|csv')
    ->name('admin.cajas.reporte');


Route::get('/admin/cajas/pdf/{id}', [App\Http\Controllers\CajaController::class, 'pdf'])
    ->name('admin.cajas.pdf')
    ->middleware('auth');


// ruta de ingreso
Route::get('/admin/cajas/{id}/ingreso-egreso', [App\Http\Controllers\CajaController::class, 'ingresoegreso'])->name('admin.cajas.ingresoegreso')->middleware('auth');
Route::post('/admin/cajas/create_ingresos_egresos', [App\Http\Controllers\CajaController::class, 'store_ingresos_egresos'])->name('admin.cajas.storeingresosegresos')->middleware('auth');
Route::get('/admin/cajas/{id}/cierre', [App\Http\Controllers\CajaController::class, 'cierre'])->name('admin.cajas.cierre')->middleware('auth');
Route::post('/admin/cajas/create_cierre', [App\Http\Controllers\CajaController::class, 'store_cierre'])->name('admin.cajas.storecierre')->middleware('auth');

// Mostrar la reporte de inresos 
Route::get('admin/reporte/ingresos', [App\Http\Controllers\reporteController::class, 'reporteIngresosView'])->name('admin.reporte.ingresos.index');
Route::get('admin/reporte/ingresos-por-fecha', [App\Http\Controllers\reporteController::class, 'ingresosPorFecha'])->name('admin.reporte.ingresos_por_fecha');
Route::get('admin/reporte/ingresos_por_fecha/pdf', [App\Http\Controllers\reporteController::class, 'ingresosPorFechaPDF'])->name('admin.reporte.ingresos_por_fecha_pdf');


// Mostrar la reporte de inresos 
Route::get('admin/reporte/egresos', [App\Http\Controllers\reporteController::class, 'reporteEgresosView'])->name('admin.reporte.egresos.index');
Route::get('admin/reporte/egresos-por-fecha', [App\Http\Controllers\reporteController::class, 'EgresosPorFecha'])->name('admin.reporte.egresos_por_fecha');
Route::get('admin/reporte/egresos_por_fecha/pdf', [App\Http\Controllers\reporteController::class, 'EgresosPorFechaPDF'])->name('admin.reporte.egresos_por_fecha_pdf');



// Catálogo público
Route::get('/admin/catalogo', [App\Http\Controllers\CatalogController::class, 'index'])->name('admin.catalogo.index');
Route::get('/admin/catalogo/{producto}', [App\Http\Controllers\CatalogController::class, 'show'])->name('admin.catalogo.show');


Route::get('/admin/catalogo', [App\Http\Controllers\CatalogController::class, 'index'])->name('admin.catalogo.index');
Route::get('/admin/catalogo/{id}', [App\Http\Controllers\CatalogController::class, 'show'])->name('admin.catalogo.show');



Route::get('/admin/catalogo/categoria/{categoria}', [App\Http\Controllers\CatalogController::class, 'ver'])->name('admin.catalogo.categoria');





// Ruta para el formulario de búsqueda (GET)
Route::get('/admin/buscar-productos', [App\Http\Controllers\CatalogController::class, 'buscar'])
    ->name('admin.catalogo.buscar');

// Ruta para el autocompletado (AJAX)
Route::get('/admin/buscar-autocomplete', [App\Http\Controllers\CatalogController::class, 'search'])
    ->name('admin.catalogo.search');


// lotes rutas
Route::get('/admin/lotes/reporte', [App\Http\Controllers\LoteController::class, 'generarReporte'])
    ->name('admin.lotes.reporte');
Route::get('/admin/lotes', [App\Http\Controllers\LoteController::class, 'index'])->name('admin.lotes.index')->middleware('auth');
Route::get('/admin/lotes/create', [App\Http\Controllers\LoteController::class, 'create'])->name('admin.lotes.create')->middleware('auth');
Route::post('/admin/lotes/create', [App\Http\Controllers\LoteController::class, 'store'])->name('admin.lotes.store')->middleware('auth');
Route::get('/admin/lotes/{id}', [App\Http\Controllers\LoteController::class, 'show'])->name('admin.lotes.show')->middleware('auth');
Route::get('/admin/lotes/{id}/edit', [App\Http\Controllers\LoteController::class, 'edit'])->name('admin.lotes.edit')->middleware('auth');
Route::put('/admin/lotes/{id}', [App\Http\Controllers\LoteController::class, 'update'])->name('admin.lotes.update')->middleware('auth');
Route::delete('/admin/lotes/{id}', [App\Http\Controllers\LoteController::class, 'destroy'])->name('admin.lotes.destroy')->middleware('auth');


//cchat
//Route::post('/chat-ia', [App\Http\Controllers\ChatIAController::class, 'preguntar']);


// inventario
Route::get('/admin/inventario', [App\Http\Controllers\InventarioController::class, 'index'])->name('admin.inventario.index');


Route::get('/admin/inventario/bajo-stock', [App\Http\Controllers\InventarioController::class, 'bajoStock'])->name('admin.inventario.bajo_stock');


Route::get('/admin/inventario/productos_porvencer', [App\Http\Controllers\InventarioController::class, 'productosPorVencer'])->name('admin.inventario.productos_porvencer');

Route::get('/admin/compras/mensual', [App\Http\Controllers\InventarioController::class, 'comprasMensuales'])->name('admin.compras.mensual');
Route::get('/admin/inventario/reporte_compras', [App\Http\Controllers\InventarioController::class, 'imprimirCompras'])->name('admin.inventario.reporte_compras');

Route::get('/admin/inventario/reporte_ventas', [App\Http\Controllers\InventarioController::class, 'imprimirVentas'])->name('admin.inventario.reporte_ventas');

Route::get('/admin/inventario/reportegeneral', [App\Http\Controllers\InventarioController::class, 'reporteGeneral'])->name('admin.inventario.reportegeneral');



Route::get('/admin/usuarios/reporte/{tipo}', [App\Http\Controllers\UsuarioController::class, 'generarReporte'])
    ->where('tipo', 'pdf|excel|csv|print')
    ->name('admin.usuarios.reporte');