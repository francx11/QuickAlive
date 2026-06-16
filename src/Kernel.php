<?php

declare(strict_types=1);

namespace App;

use DI\Container;
use DI\ContainerBuilder;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Kernel
{
    private readonly Container $container;
    private readonly Router $router;

    public function __construct(private readonly string $basePath)
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($this->basePath . '/config/services.php');
        $this->container = $builder->build();

        $strategy = new ApplicationStrategy();
        $strategy->setContainer($this->container);

        $this->router = new Router();
        $this->router->setStrategy($strategy);

        (require $this->basePath . '/src/Http/routes.php')($this->router);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->router->dispatch($request);
    }

    public function run(): void
    {
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        $response = $this->handle($creator->fromGlobals());

        (new SapiEmitter())->emit($response);
    }
}
