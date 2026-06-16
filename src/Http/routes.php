<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\SesionController;
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
};
