<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class HealthController
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function check(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(200)
            ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode([
            'status' => 'ok',
            'php' => PHP_VERSION,
        ], JSON_THROW_ON_ERROR));

        return $response;
    }
}
