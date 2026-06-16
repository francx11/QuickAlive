<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Domain\Actividad\NuevaActividadSimpleDTO;
use App\Domain\Preferencia\PreferenciaRepositoryInterface;
use App\Infrastructure\View\JsonResponder;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

final readonly class ActividadController
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private PreferenciaRepositoryInterface $preferencias,
        private TwigResponder $twig,
        private JsonResponder $json,
        private ResponseFactoryInterface $responseFactory,
        private string $uploadDir,
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return $this->twig->render('gestionActividades.html', ['logueado' => true]);
    }

    public function renderAlta(ServerRequestInterface $request): ResponseInterface
    {
        return $this->twig->render('altaActividad.html', [
            'logueado' => true,
            'tiposPreferencias' => $this->preferencias->todosLosTipos(),
        ]);
    }

    public function alta(ServerRequestInterface $request): ResponseInterface
    {
        $datos = $request->getParsedBody();

        $idActividad = $this->actividades->insertarSimple(new NuevaActividadSimpleDTO(
            nombreActividad: $datos['nombre'],
            descripcion: $datos['descripcion'],
            duracion: (int) $datos['duracion'],
        ));

        $this->actividades->insertarCategorias($idActividad, $this->idsTipoPreferencia($datos['preferencias'] ?? '[]'));
        $this->subirImagenes($idActividad, $request);

        return $this->json->render(['success' => true, 'idActividad' => $idActividad]);
    }

    public function buscar(ServerRequestInterface $request): ResponseInterface
    {
        $nombreActividad = $request->getParsedBody()['nombreActividadBuscado'] ?? null;

        if ($nombreActividad === null) {
            return $this->json->render([]);
        }

        return $this->json->render($this->actividades->buscarCoincidencias($nombreActividad));
    }

    public function eliminar(ServerRequestInterface $request): ResponseInterface
    {
        $idActividad = (int) ($request->getQueryParams()['id'] ?? -1);

        if (!$this->actividades->eliminar($idActividad)) {
            return $this->responseFactory->createResponse(500)->withHeader('Content-Type', 'text/plain');
        }

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/backend/admin/gestionActividades/gestionActividades.php');
    }

    public function eliminarFotoGaleria(ServerRequestInterface $request): ResponseInterface
    {
        $numImagen = (int) ($request->getParsedBody()['imagenId'] ?? -1);

        if (!$this->actividades->eliminarFotoGaleria($numImagen)) {
            return $this->json->render(['success' => false, 'message' => 'Error en la eliminación de la imagen']);
        }

        return $this->json->render(['success' => true]);
    }

    public function renderModificar(ServerRequestInterface $request): ResponseInterface
    {
        $idActividad = (int) ($request->getQueryParams()['id'] ?? -1);

        return $this->twig->render('modificarActividad.html', [
            'logueado' => true,
            'tiposPreferencias' => $this->preferencias->todosLosTipos(),
            'idActividad' => $idActividad,
            'imagenes' => $this->actividades->galeria($idActividad),
            'categorias' => $this->actividades->categorias($idActividad),
        ]);
    }

    public function modificar(ServerRequestInterface $request): ResponseInterface
    {
        $datos = $request->getParsedBody();
        $idActividad = (int) ($datos['idActividad'] ?? -1);
        $actividadOriginal = $this->actividades->porId($idActividad);

        if ($actividadOriginal === null) {
            return $this->json->render(['success' => false, 'message' => 'ID de actividad erróneo'], 404);
        }

        $nombreActividad = ($datos['nombre'] ?? '') !== '' ? $datos['nombre'] : $actividadOriginal->nombreActividad;
        $descripcion = ($datos['descripcion'] ?? '') !== '' ? $datos['descripcion'] : $actividadOriginal->descripcion;
        $duracion = ($datos['duracion'] ?? '') !== '' ? (int) $datos['duracion'] : $actividadOriginal->duracion;

        $this->actividades->modificar($idActividad, $nombreActividad, $descripcion, $duracion);

        if (isset($datos['preferencias'])) {
            $this->actividades->modificarTipoPreferencias($idActividad, $this->idsTipoPreferencia($datos['preferencias']));
        }

        $this->subirImagenes($idActividad, $request);

        return $this->json->render(['success' => true]);
    }

    /** @return int[] */
    private function idsTipoPreferencia(string $preferenciasJson): array
    {
        $categorias = json_decode($preferenciasJson, true) ?? [];

        return array_map(static fn (array $categoria): int => (int) $categoria['idTipoPreferencia'], $categorias);
    }

    private function subirImagenes(int $idActividad, ServerRequestInterface $request): void
    {
        $imagenes = $request->getUploadedFiles()['imagenes'] ?? [];

        foreach ($imagenes as $imagen) {
            if (!$imagen instanceof UploadedFileInterface || $imagen->getError() !== UPLOAD_ERR_OK) {
                continue;
            }

            $nombreImagen = basename((string) $imagen->getClientFilename());
            $imagen->moveTo($this->uploadDir . '/' . $nombreImagen);

            $this->actividades->agregarFotoGaleria($idActividad, '/quickalive/imgs/' . $nombreImagen);
        }
    }
}
