<?php
/**
 * api/check-subscription.php — Endpoint JSON: verifica inscrição no canal
 * DJ Bruno Pendrives
 * 
 * Método: GET
 * Autenticação: Sessão ativa obrigatória
 * Retorno: {"inscrito": true|false, "mensagem": "..."}
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/youtube.php';

// Define resposta como JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

iniciarSessao();

// Verifica se é requisição AJAX válida
if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    http_response_code(403);
    echo json_encode(['erro' => 'Acesso direto não permitido.']);
    exit;
}

// Verifica autenticação
if (!estaAutenticado()) {
    http_response_code(401);
    echo json_encode(['inscrito' => false, 'mensagem' => 'Não autenticado. Faça login primeiro.']);
    exit;
}

$usuario = getUsuarioSessao();
$accessToken = $usuario['token'] ?? '';

$inscrito = false;

if (!empty($accessToken)) {
    // Verifica em tempo real via YouTube API
    $inscrito = checkYouTubeSubscription($accessToken);

    // Atualiza banco de dados com resultado atualizado
    $db = getDB();
    $db->prepare("UPDATE usuarios SET inscrito = ?, ultimo_acesso = NOW() WHERE id = ?")
       ->execute([$inscrito ? 1 : 0, $usuario['id']]);

    // Atualiza sessão
    $_SESSION['usuario_inscrito'] = $inscrito;
} else {
    // Sem token: consulta o banco (pode estar desatualizado)
    $db = getDB();
    $stmt = $db->prepare("SELECT inscrito FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario['id']]);
    $row = $stmt->fetch();
    $inscrito = !empty($row['inscrito']);
}

// Retorna resultado
echo json_encode([
    'inscrito'  => $inscrito,
    'mensagem'  => $inscrito
        ? 'Você está inscrito no canal! Downloads liberados.'
        : 'Você ainda não está inscrito no canal @Hosttimerkelvin.',
    'canal_url' => 'https://www.youtube.com/@Hosttimerkelvin?sub_confirmation=1',
]);
exit;
