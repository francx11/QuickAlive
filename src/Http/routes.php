<?php

declare(strict_types=1);

use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\SesionController;
use App\Http\Middleware\SessionMiddleware;
use DI\Container;
use League\Route\Router;

return static function (Router $router, Container $container): void {
    $router->middleware($container->get(SessionMiddleware::class));

    $router->map('GET', '/healthz', [HealthController::class, 'check']);

    $router->map('GET', '/backend/api/sesiones/login.php', [SesionController::class, 'login']);
    $router->map('POST', '/backend/api/sesiones/login.php', [SesionController::class, 'login']);
    $router->map('GET', '/backend/api/sesiones/logout.php', [SesionController::class, 'logout']);
};
