<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\ActividadController;
use App\Http\Controllers\Admin\PreferenciaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\SesionController;
use App\Http\Controllers\User\AsistenteIAController;
use App\Http\Controllers\User\HistorialController;
use App\Http\Controllers\User\ListaActividadesController;
use App\Http\Controllers\User\RecomendacionesController;
use App\Http\Controllers\User\RegistroController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\SessionMiddleware;
use DI\Container;
use League\Route\Router;
use League\Route\RouteGroup;

return static function (Router $router, Container $container): void {
    $router->middleware($container->get(SessionMiddleware::class));

    $router->map('GET', '/healthz', [HealthController::class, 'check']);

    $router->map('GET', '/backend/api/sesiones/login.php', [SesionController::class, 'login']);
    $router->map('POST', '/backend/api/sesiones/login.php', [SesionController::class, 'login']);
    $router->map('GET', '/backend/api/sesiones/logout.php', [SesionController::class, 'logout']);

    $router->group('/backend/admin', static function (RouteGroup $admin) {
        $admin->map('GET', '/gestionUsuarios/gestionUsuarios.php', [UsuarioController::class, 'index']);
        $admin->map('GET', '/gestionUsuarios/altaUsuario.php', [UsuarioController::class, 'alta']);
        $admin->map('POST', '/gestionUsuarios/altaUsuario.php', [UsuarioController::class, 'alta']);
        $admin->map('POST', '/gestionUsuarios/buscarUsuario.php', [UsuarioController::class, 'buscar']);
        $admin->map('GET', '/gestionUsuarios/eliminarUsuario.php', [UsuarioController::class, 'eliminar']);
        $admin->map('GET', '/gestionUsuarios/modificarUsuario.php', [UsuarioController::class, 'modificar']);
        $admin->map('POST', '/gestionUsuarios/modificarUsuario.php', [UsuarioController::class, 'modificar']);
    })
        ->middleware($container->get(AuthMiddleware::class))
        ->middleware($container->get(AdminMiddleware::class));

    $router->group('/backend/admin', static function (RouteGroup $admin) {
        $admin->map('GET', '/gestionActividades/gestionActividades.php', [ActividadController::class, 'index']);
        $admin->map('GET', '/gestionActividades/renderAltaActividad.php', [ActividadController::class, 'renderAlta']);
        $admin->map('POST', '/gestionActividades/altaActividad.php', [ActividadController::class, 'alta']);
        $admin->map('POST', '/gestionActividades/buscarActividad.php', [ActividadController::class, 'buscar']);
        $admin->map('GET', '/gestionActividades/eliminarActividad.php', [ActividadController::class, 'eliminar']);
        $admin->map('POST', '/gestionActividades/eliminarFotoGaleria.php', [ActividadController::class, 'eliminarFotoGaleria']);
        $admin->map('GET', '/gestionActividades/renderModificarActividad.php', [ActividadController::class, 'renderModificar']);
        $admin->map('POST', '/gestionActividades/modificarActividad.php', [ActividadController::class, 'modificar']);
    })
        ->middleware($container->get(AuthMiddleware::class))
        ->middleware($container->get(AdminMiddleware::class));

    $router->group('/backend/admin', static function (RouteGroup $admin) {
        $admin->map('GET', '/gestionPreferencias/gestionPreferencias.php', [PreferenciaController::class, 'index']);
        $admin->map('GET', '/gestionPreferencias/altaTipoPreferencia.php', [PreferenciaController::class, 'alta']);
        $admin->map('POST', '/gestionPreferencias/altaTipoPreferencia.php', [PreferenciaController::class, 'alta']);
        $admin->map('POST', '/gestionPreferencias/buscarTipoPreferencia.php', [PreferenciaController::class, 'buscar']);
        $admin->map('GET', '/gestionPreferencias/eliminarTipoPreferencia.php', [PreferenciaController::class, 'eliminar']);
    })
        ->middleware($container->get(AuthMiddleware::class))
        ->middleware($container->get(AdminMiddleware::class));

    // Public registration flow -- no auth required, the user doesn't exist yet.
    $router->map('GET', '/backend/user/gestionRegistro/registroBasico.php', [RegistroController::class, 'registroBasico']);
    $router->map('POST', '/backend/user/gestionRegistro/registroBasico.php', [RegistroController::class, 'registroBasico']);
    $router->map('GET', '/backend/user/gestionRegistro/registroPreferencias.php', [RegistroController::class, 'registroPreferencias']);
    $router->map('POST', '/backend/user/gestionRegistro/guardarPreferencias.php', [RegistroController::class, 'guardarPreferencias']);

    // Updating preferences from an existing account does require auth.
    $router->map('GET', '/backend/user/gestionRegistro/renderActualizarPreferencias.php', [RegistroController::class, 'renderActualizarPreferencias'])
        ->middleware($container->get(AuthMiddleware::class));
    $router->map('POST', '/backend/user/gestionRegistro/actualizarPreferencias.php', [RegistroController::class, 'actualizarPreferencias'])
        ->middleware($container->get(AuthMiddleware::class));

    $router->group('/backend/user/gestionListaActividades', static function (RouteGroup $group) {
        $group->map('GET', '/renderListaActividades.php', [ListaActividadesController::class, 'render']);
        $group->map('POST', '/marcarComoCompletada.php', [ListaActividadesController::class, 'marcarCompletada']);
        $group->map('POST', '/modificarFechaRealizacion.php', [ListaActividadesController::class, 'modificarFechaRealizacion']);
    })
        ->middleware($container->get(AuthMiddleware::class));

    $router->group('/backend/user/gestionHistorialActividades', static function (RouteGroup $group) {
        $group->map('GET', '/renderHistorialActividades.php', [HistorialController::class, 'render']);
        $group->map('POST', '/volverArealizarActividad.php', [HistorialController::class, 'volverARealizar']);
    })
        ->middleware($container->get(AuthMiddleware::class));

    $router->group('/backend/user/gestionRecomendaciones', static function (RouteGroup $group) {
        $group->map('GET', '/controladorInteresActividad.php', [RecomendacionesController::class, 'decidirInteresActividad']);
        $group->map('GET', '/controladorActividadGeolocalizable.php', [RecomendacionesController::class, 'insertarYDecidirGeolocalizable']);
        $group->map('POST', '/insertarActividadGeolocalizable.php', [RecomendacionesController::class, 'insertarGeolocalizable']);
        $group->map('GET', '/obtenerActividadesGeolocalizables.php', [RecomendacionesController::class, 'obtenerGeolocalizablesDisponibles']);
        $group->map('GET', '/obtenerActividadesSimples.php', [RecomendacionesController::class, 'obtenerSimplesRecomendadas']);
    })
        ->middleware($container->get(AuthMiddleware::class));

    $router->group('/backend/user/gestionAsistenteIA', static function (RouteGroup $group) {
        $group->map('GET', '/renderAsistenteIA.php', [AsistenteIAController::class, 'render']);
        $group->map('GET', '/activarPremiumDemo.php', [AsistenteIAController::class, 'activarPremiumDemo']);
        $group->map('POST', '/procesarMensajeChat.php', [AsistenteIAController::class, 'procesarMensajeChat']);
    })
        ->middleware($container->get(AuthMiddleware::class));
};
