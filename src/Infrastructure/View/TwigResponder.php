<?php

declare(strict_types=1);

namespace App\Infrastructure\View;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;

final readonly class TwigResponder
{
    public function __construct(
        private Environment $twig,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    /** @param array<string, mixed> $context */
    public function render(string $template, array $context = [], int $status = 200): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($status)
            ->withHeader('Content-Type', 'text/html; charset=utf-8');

        $response->getBody()->write($this->twig->render($template, $context));

        return $response;
    }
}
