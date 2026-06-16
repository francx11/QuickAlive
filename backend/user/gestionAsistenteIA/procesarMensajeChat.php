<?php
require_once '../../bd/bd.php';
require_once "../../../vendor/autoload.php";

use Anthropic\Client;

session_start();

header('Content-Type: application/json');

if (empty($_SESSION['loggedin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$idUsuario = $_SESSION['idUsuario'];
$bd = new BD();

if (!$bd->esUsuarioPremium($idUsuario)) {
    http_response_code(403);
    echo json_encode(['error' => 'Funcion disponible solo para usuarios premium']);
    exit;
}

$mensaje = trim($_POST['mensaje'] ?? '');

if ($mensaje === '' || mb_strlen($mensaje) > 500) {
    http_response_code(400);
    echo json_encode(['error' => 'Mensaje invalido']);
    exit;
}

// Conjunto de actividades reales del usuario: única fuente de verdad para el grounding.
$actividadesCandidatas = $bd->recomendarActividadesPersonalizadas($idUsuario);

if (empty($actividadesCandidatas)) {
    echo json_encode([
        'respuesta' => 'No tienes actividades disponibles para recomendar en este momento.',
        'actividadesRecomendadas' => [],
    ]);
    exit;
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
    . "Actividades candidatas (JSON): " . json_encode($listaParaPrompt, JSON_UNESCAPED_UNICODE);

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
} catch (\Throwable $e) {
    http_response_code(502);
    echo json_encode(['error' => 'No se pudo contactar con el asistente IA']);
    exit;
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
    http_response_code(502);
    echo json_encode(['error' => 'Respuesta del asistente IA no válida']);
    exit;
}

// Anti-alucinación: solo se aceptan ids que existan realmente entre las candidatas.
$actividadesRecomendadas = [];
foreach ($datos['idsRecomendados'] as $id) {
    if (isset($candidatasPorId[$id])) {
        $actividadesRecomendadas[] = $candidatasPorId[$id];
    }
}

echo json_encode([
    'respuesta' => $datos['reply'],
    'actividadesRecomendadas' => $actividadesRecomendadas,
]);
