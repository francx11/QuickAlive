<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Preferencia\PreferenciaRepositoryInterface;
use App\Infrastructure\View\JsonResponder;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class PreferenciaController
{
    public function __construct(
        private PreferenciaRepositoryInterface $preferencias,
        private TwigResponder $twig,
        private JsonResponder $json,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return $this->twig->render('gestionPreferencias.html', ['logueado' => true]);
    }

    public function alta(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $tipoPreferencia = $request->getParsedBody()['tipoPreferencia'] ?? '';

            $this->preferencias->insertarTipo($tipoPreferencia);

            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', '/backend/admin/gestionPreferencias/gestionPreferencias.php');
        }

        return $this->twig->render('altaTipoPreferencia.html', ['logueado' => true]);
    }

    public function buscar(ServerRequestInterface $request): ResponseInterface
    {
        $tipoPreferenciaBuscado = $request->getParsedBody()['tipoPreferenciaBuscado'] ?? null;

        if ($tipoPreferenciaBuscado === null) {
            return $this->json->render([]);
        }

        return $this->json->render($this->preferencias->buscarCoincidenciasTipo($tipoPreferenciaBuscado));
    }

    public function eliminar(ServerRequestInterface $request): ResponseInterface
    {
        $idTipoPreferencia = (int) ($request->getQueryParams()['id'] ?? -1);

        if (!$this->preferencias->eliminarTipo($idTipoPreferencia)) {
            return $this->responseFactory->createResponse(500)->withHeader('Content-Type', 'text/plain');
        }

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/backend/admin/gestionPreferencias/gestionPreferencias.php');
    }
}
