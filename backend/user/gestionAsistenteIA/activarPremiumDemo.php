<?php
// TODO: endpoint de demostración. Sustituir por una activación real ligada
// a una pasarela de pago (Stripe/PayPal) antes de pasar a producción.
require_once '../../bd/bd.php';

session_start();

header('Content-Type: application/json');

if (empty($_SESSION['loggedin'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$idUsuario = $_SESSION['idUsuario'];
$bd = new BD();
$exito = $bd->activarPremiumDemo($idUsuario);

echo json_encode(['exito' => $exito]);
