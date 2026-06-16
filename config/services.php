<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return [
    ResponseFactoryInterface::class => DI\create(Psr17Factory::class),

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
