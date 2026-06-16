<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use Anthropic\Client;
use App\Application\Actividad\RecomendarActividadesService;
use App\Domain\Usuario\UsuarioRepositoryInterface;
use App\Infrastructure\View\JsonResponder;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class AsistenteIAController
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarios,
        private RecomendarActividadesService $recomendaciones,
        private TwigResponder $twig,
        private JsonResponder $json,
    ) {}

    public function render(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        if (!$this->usuarios->esPremium($idUsuario)) {
            return $this->twig->render('user/gestionAsistenteIA/upsellPremium.html');
        }

        return $this->twig->render('user/gestionAsistenteIA/asistente.html', ['idUsuario' => $idUsuario]);
    }

    public function activarPremiumDemo(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        return $this->json->render(['exito' => $this->usuarios->activarPremiumDemo($idUsuario)]);
    }

    public function procesarMensajeChat(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        if (!$this->usuarios->esPremium($idUsuario)) {
            return $this->json->render(['error' => 'Funcion disponible solo para usuarios premium'], 403);
        }

        $mensaje = trim($request->getParsedBody()['mensaje'] ?? '');

        if ($mensaje === '' || mb_strlen($mensaje) > 500) {
            return $this->json->render(['error' => 'Mensaje invalido'], 400);
        }

        // Conjunto de actividades reales del usuario: única fuente de verdad para el grounding.
        $actividadesCandidatas = $this->recomendaciones->paraUsuario($idUsuario);

        if ($actividadesCandidatas === []) {
            return $this->json->render([
                'respuesta' => 'No tienes actividades disponibles para recomendar en este momento.',
                'actividadesRecomendadas' => [],
            ]);
        }

        $candidatasPorId = [];
        $listaParaPrompt = [];

        foreach ($actividadesCandidatas as $actividad) {
            $candidatasPorId[$actividad['idActividad']] = $actividad;
            $listaParaPrompt[] = [
                'id' => (int) $actividad['idActividad'],
                'nombre' => $actividad['nombreActividad'],
                'descripcion' => $actividad['descripcion'],
                'duracionMinutos' => $actividad['duracion'],
            ];
        }

        $client = new Client(apiKey: getenv('ANTHROPIC_API_KEY'));

        $systemPrompt = 'Eres el asistente de actividades de QuickAlive. Solo puedes recomendar actividades '
            . 'que aparezcan en la lista de candidatas proporcionada por el usuario, identificadas por su "id". '
            . 'Nunca inventes actividades ni ids que no estén en esa lista. Responde en español, en un tono cercano, '
            . 'explicando brevemente por qué elegiste cada actividad recomendada.';

        $mensajeUsuario = "Petición del usuario: \"{$mensaje}\"\n\n"
            . 'Actividades candidatas (JSON): ' . json_encode($listaParaPrompt, JSON_UNESCAPED_UNICODE);

        try {
            $respuesta = $client->messages->create(
                model: 'claude-opus-4-8',
                maxTokens: 1024,
                system: $systemPrompt,
                messages: [
                    ['role' => 'user', 'content' => $mensajeUsuario],
                ],
                outputConfig: [
                    'format' => [
                        'type' => 'json_schema',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'reply' => ['type' => 'string'],
                                'idsRecomendados' => [
                                    'type' => 'array',
                                    'items' => ['type' => 'integer'],
                                ],
                            ],
                            'required' => ['reply', 'idsRecomendados'],
                            'additionalProperties' => false,
                        ],
                    ],
                ],
            );
        } catch (\Throwable) {
            return $this->json->render(['error' => 'No se pudo contactar con el asistente IA'], 502);
        }

        $textoRespuesta = null;

        foreach ($respuesta->content as $bloque) {
            if ($bloque->type === 'text') {
                $textoRespuesta = $bloque->text;
                break;
            }
        }

        $datos = $textoRespuesta ? json_decode($textoRespuesta, true) : null;

        if (!is_array($datos) || !isset($datos['reply'], $datos['idsRecomendados'])) {
            return $this->json->render(['error' => 'Respuesta del asistente IA no válida'], 502);
        }

        // Anti-alucinación: solo se aceptan ids que existan realmente entre las candidatas.
        $actividadesRecomendadas = [];

        foreach ($datos['idsRecomendados'] as $id) {
            if (isset($candidatasPorId[$id])) {
                $actividadesRecomendadas[] = $candidatasPorId[$id];
            }
        }

        return $this->json->render([
            'respuesta' => $datos['reply'],
            'actividadesRecomendadas' => $actividadesRecomendadas,
        ]);
    }
}
