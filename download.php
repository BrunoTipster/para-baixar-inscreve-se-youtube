<?php
/**
 * download.php — Verifica inscrição e libera download do arquivo
 * DJ Bruno Pendrives
 * 
 * Segurança:
 * - Usuário deve estar autenticado
 * - Token é revalidado via YouTube API a cada download
 * - ID da música é validado via prepared statement
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/youtube.php';

iniciarSessao();

// ============================================================
// 1. VERIFICA AUTENTICAÇÃO
// ============================================================
if (!estaAutenticado()) {
    header('Location: /login.php');
    exit;
}

// ============================================================
// 2. VALIDA ID DA MÚSICA
// ============================================================
$musicaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

if (!$musicaId) {
    http_response_code(400);
    die('ID de música inválido.');
}

// ============================================================
// 3. BUSCA A MÚSICA NO BANCO
// ============================================================
$db = getDB();
$stmt = $db->prepare("SELECT * FROM musicas WHERE id = ? AND ativo = 1");
$stmt->execute([$musicaId]);
$musica = $stmt->fetch();

if (!$musica) {
    http_response_code(404);
    die('Música não encontrada ou indisponível.');
}

// ============================================================
// 4. VERIFICA INSCRIÇÃO NO CANAL (revalidação server-side)
// ============================================================
$usuario = getUsuarioSessao();
$accessToken = $usuario['token'] ?? '';

$inscrito = false;

if (!empty($accessToken)) {
    // Verifica via YouTube API em tempo real
    $inscrito = checkYouTubeSubscription($accessToken);
} else {
    // Fallback: verifica no banco (menos seguro, mas funcional)
    $stmt = $db->prepare("SELECT inscrito FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario['id']]);
    $row = $stmt->fetch();
    $inscrito = !empty($row['inscrito']);
}

if (!$inscrito) {
    // Atualiza status no banco
    $db->prepare("UPDATE usuarios SET inscrito = 0 WHERE id = ?")
       ->execute([$usuario['id']]);

    // Retorna JSON se for requisição AJAX, ou redireciona
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Você precisa estar inscrito no canal para baixar.']);
        exit;
    }

    header('Location: /?erro=nao_inscrito');
    exit;
}

// ============================================================
// 5. ATUALIZA STATUS DE INSCRIÇÃO NA SESSÃO/BANCO
// ============================================================
$_SESSION['usuario_inscrito'] = true;
$db->prepare("UPDATE usuarios SET inscrito = 1, ultimo_acesso = NOW() WHERE id = ?")
   ->execute([$usuario['id']]);

// ============================================================
// 6. VERIFICA SE É LINK EXTERNO OU ARQUIVO LOCAL
// ============================================================
$arquivo = $musica['arquivo'];
$isLinkExterno = (
    strpos($arquivo, 'http://') === 0 || 
    strpos($arquivo, 'https://') === 0 ||
    strpos($arquivo, 'drive.google.com') !== false ||
    strpos($arquivo, 'mediafire.com') !== false ||
    strpos($arquivo, 'gofile.io') !== false
);

if ($isLinkExterno) {
    // É um link externo - registra e redireciona
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    // Registra o download
    $stmtCheck = $db->prepare("
        SELECT id FROM downloads
        WHERE usuario_id = ? AND musica_id = ? AND data_download > DATE_SUB(NOW(), INTERVAL 10 SECOND)
    ");
    $stmtCheck->execute([$usuario['id'], $musicaId]);
    
    if (!$stmtCheck->fetch()) {
        $db->prepare("
            INSERT INTO downloads (usuario_id, musica_id, data_download, ip)
            VALUES (?, ?, NOW(), ?)
        ")->execute([$usuario['id'], $musicaId, $ip]);
        
        $db->prepare("UPDATE musicas SET downloads = downloads + 1 WHERE id = ?")
           ->execute([$musicaId]);
    }
    
    // Redireciona para o link externo
    header('Location: ' . $arquivo);
    exit;
}

// É arquivo local - verifica existência
$caminhoArquivo = __DIR__ . '/' . $arquivo;

if (!file_exists($caminhoArquivo) || !is_readable($caminhoArquivo)) {
    http_response_code(404);
    die('Arquivo de música não encontrado no servidor.');
}

// ============================================================
// 7. REGISTRA O DOWNLOAD NO BANCO
// ============================================================
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// Evita duplicatas em menos de 10 segundos (prevenção básica)
$stmtCheck = $db->prepare("
    SELECT id FROM downloads
    WHERE usuario_id = ? AND musica_id = ? AND data_download > DATE_SUB(NOW(), INTERVAL 10 SECOND)
");
$stmtCheck->execute([$usuario['id'], $musicaId]);

if (!$stmtCheck->fetch()) {
    // Registra o download
    $db->prepare("
        INSERT INTO downloads (usuario_id, musica_id, data_download, ip)
        VALUES (?, ?, NOW(), ?)
    ")->execute([$usuario['id'], $musicaId, $ip]);

    // Incrementa contador na tabela de músicas
    $db->prepare("UPDATE musicas SET downloads = downloads + 1 WHERE id = ?")
       ->execute([$musicaId]);
}

// ============================================================
// 8. SERVE O ARQUIVO PARA DOWNLOAD
// ============================================================
$nomeArquivo = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $musica['titulo']);
$extensao    = pathinfo($musica['arquivo'], PATHINFO_EXTENSION) ?: 'mp3';
$nomeDownload = "DJ_Bruno_" . $nomeArquivo . "." . $extensao;

// Headers para download
header('Content-Type: audio/mpeg');
header('Content-Disposition: attachment; filename="' . $nomeDownload . '"');
header('Content-Length: ' . filesize($caminhoArquivo));
header('Content-Transfer-Encoding: binary');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Limpa output buffer
ob_clean();
flush();

// Envia o arquivo
readfile($caminhoArquivo);
exit;
