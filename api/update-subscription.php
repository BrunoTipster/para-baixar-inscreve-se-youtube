<?php
/**
 * api/update-subscription.php — Atualiza status de inscrição no banco
 * DJ Bruno Pendrives
 * 
 * Método: POST
 * Uso: Após o usuário confirmar que se inscreveu
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/youtube.php';

header('Content-Type: application/json; charset=utf-8');

iniciarSessao();

if (!estaAutenticado() || empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autorizado.']);
    exit;
}

$usuario = getUsuarioSessao();
$inscrito = checkYouTubeSubscription($usuario['token'] ?? '');

if ($inscrito) {
    $db = getDB();
    $db->prepare("UPDATE usuarios SET inscrito = 1 WHERE id = ?")
       ->execute([$usuario['id']]);
    $_SESSION['usuario_inscrito'] = true;
}

echo json_encode(['inscrito' => $inscrito]);
exit;
