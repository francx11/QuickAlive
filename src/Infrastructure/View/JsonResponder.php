<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final readonly class JsonResponder
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function render(mixed $data, int $status = 200): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($status)
            ->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));

        return $response;
    }
}
