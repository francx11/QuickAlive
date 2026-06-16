<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class PanelController
{
    public function __construct(
        private TwigResponder $twig,
    ) {}

    public function render(ServerRequestInterface $request): ResponseInterface
    {
        return $this->twig->render('panelAdmin.html', ['logueado' => true]);
    }
}
