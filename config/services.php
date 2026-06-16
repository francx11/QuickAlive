<?php

declare(strict_types=1);

use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Domain\Preferencia\PreferenciaRepositoryInterface;
use App\Domain\Usuario\UsuarioRepositoryInterface;
use App\Http\Controllers\Admin\ActividadController;
use App\Infrastructure\Mail\PhpMailerMailer;
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

    ActividadController::class => DI\autowire()
        ->constructorParameter('uploadDir', dirname(__DIR__) . '/public/assets/img'),

    PhpMailerMailer::class => DI\autowire()
        ->constructorParameter('host', 'smtp.gmail.com')
        ->constructorParameter('username', getenv('CORREO_GMAIL'))
        ->constructorParameter('password', getenv('CONTRASENA_GMAIL')),

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
        $loader = new FilesystemLoader(dirname(__DIR__) . '/resources/views');

        return new Environment($loader);
    },
];
