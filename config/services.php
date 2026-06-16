<?php

declare(strict_types=1);

use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Domain\Preferencia\PreferenciaRepositoryInterface;
use App\Domain\Usuario\UsuarioRepositoryInterface;
use App\Http\Controllers\Admin\ActividadController;
use App\Infrastructure\Persistence\Mysql\ActividadRepository;
use App\Infrastructure\Persistence\Mysql\PreferenciaRepository;
use App\Infrastructure\Persistence\Mysql\UsuarioRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return [
    ResponseFactoryInterface::class => DI\create(Psr17Factory::class),

    UsuarioRepositoryInterface::class => DI\autowire(UsuarioRepository::class),
    ActividadRepositoryInterface::class => DI\autowire(ActividadRepository::class),
    PreferenciaRepositoryInterface::class => DI\autowire(PreferenciaRepository::class),

    // The legacy app stores uploaded activity images flat under /imgs at the
    // project root (pre-existing layout; Phase 5 may relocate it).
    ActividadController::class => DI\autowire()
        ->constructorParameter('uploadDir', dirname(__DIR__) . '/imgs'),

    Connection::class => function (): Connection {
        return DriverManager::getConnection([
            'driverClass' => \Doctrine\DBAL\Driver\Mysqli\Driver::class,
            'host' => getenv('DB_HOST'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'dbname' => getenv('DB_NAME'),
            'charset' => 'utf8mb4',
        ]);
    },

    Environment::class => function (): Environment {
        $root = dirname(__DIR__);

        // Phase 5 will move these into resources/views; until then, the legacy
        // frontend/*/templates trees are added as flat search paths so migrated
        // controllers can render the existing templates unchanged.
        $loader = new FilesystemLoader([
            $root . '/resources/views',
            $root . '/frontend/common/templates',
            $root . '/frontend/user/templates',
            $root . '/frontend/admin/templates',
        ]);

        return new Environment($loader);
    },
];
