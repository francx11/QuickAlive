<?php

declare(strict_types=1);

use App\Http\Controllers\Api\HealthController;
use League\Route\Router;

return static function (Router $router): void {
    $router->map('GET', '/healthz', [HealthController::class, 'check']);
};
