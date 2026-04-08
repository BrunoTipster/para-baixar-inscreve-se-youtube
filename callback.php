<?php
/**
 * callback.php — Retorno do OAuth Google
 * DJ Bruno Pendrives
 * 
 * Fluxo:
 * 1. Valida o state (CSRF)
 * 2. Troca o code pelo access_token
 * 3. Busca dados do usuário no Google
 * 4. Verifica inscrição no YouTube
 * 5. Cria ou atualiza usuário no banco
 * 6. Cria sessão e redireciona
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/youtube.php';

iniciarSessao();

// ============================================================
// 1. VALIDA PARÂMETROS E TOKEN CSRF (state)
// ============================================================
$code  = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_SPECIAL_CHARS);
$state = filter_input(INPUT_GET, 'state', FILTER_SANITIZE_SPECIAL_CHARS);
$erro  = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_SPECIAL_CHARS);

// Se o usuário cancelou ou houve erro
if ($erro) {
    header('Location: /login.php?erro=acesso_negado');
    exit;
}

// Valida: code e state obrigatórios
if (empty($code) || empty($state)) {
    header('Location: /login.php?erro=parametros_invalidos');
    exit;
}

// Valida token CSRF
if (!isset($_SESSION['oauth_state']) || !hash_equals($_SESSION['oauth_state'], $state)) {
    header('Location: /login.php?erro=token_invalido');
    exit;
}

// Remove o state da sessão para não reutilizar
unset($_SESSION['oauth_state']);

// ============================================================
// 2. TROCA O CODE PELO ACCESS TOKEN
// ============================================================
$tokenData = exchangeCodeForToken($code);

if (!$tokenData || empty($tokenData['access_token'])) {
    header('Location: /login.php?erro=token_falhou');
    exit;
}

$accessToken  = $tokenData['access_token'];
$refreshToken = $tokenData['refresh_token'] ?? null;

// ============================================================
// 3. BUSCA DADOS DO USUÁRIO NO GOOGLE
// ============================================================
$userInfo = getGoogleUserInfo($accessToken);

if (!$userInfo || empty($userInfo['sub'])) {
    header('Location: /login.php?erro=usuario_nao_encontrado');
    exit;
}

$googleId = $userInfo['sub'];
$nome     = $userInfo['name']    ?? 'Usuário';
$email    = $userInfo['email']   ?? '';
$foto     = $userInfo['picture'] ?? '';

// Sanitiza inputs
$nome  = htmlspecialchars(strip_tags($nome), ENT_QUOTES, 'UTF-8');
$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';

// ============================================================
// 4. VERIFICA INSCRIÇÃO NO YOUTUBE
// ============================================================
$inscrito = checkYouTubeSubscription($accessToken);

// ============================================================
// 5. CRIA OU ATUALIZA USUÁRIO NO BANCO
// ============================================================
$db = getDB();

// Verifica se usuário já existe
$stmt = $db->prepare("SELECT * FROM usuarios WHERE google_id = ?");
$stmt->execute([$googleId]);
$usuarioDB = $stmt->fetch();

if ($usuarioDB) {
    // Atualiza dados existentes
    $stmt = $db->prepare("
        UPDATE usuarios
        SET nome = ?, email = ?, foto = ?, inscrito = ?, access_token = ?, ultimo_acesso = NOW()
        WHERE google_id = ?
    ");
    $stmt->execute([
        $nome,
        $email,
        $foto,
        $inscrito ? 1 : 0,
        $accessToken,
        $googleId
    ]);
    $usuarioId = $usuarioDB['id'];
} else {
    // Cria novo usuário
    $stmt = $db->prepare("
        INSERT INTO usuarios (google_id, nome, email, foto, inscrito, access_token, data_cadastro, ultimo_acesso)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([
        $googleId,
        $nome,
        $email,
        $foto,
        $inscrito ? 1 : 0,
        $accessToken
    ]);
    $usuarioId = $db->lastInsertId();
}

// ============================================================
// 6. CRIA SESSÃO SEGURA
// ============================================================
regenerarSessao(); // Previne session fixation

$_SESSION['usuario_id']      = $usuarioId;
$_SESSION['usuario_nome']    = $nome;
$_SESSION['usuario_email']   = $email;
$_SESSION['usuario_foto']    = $foto;
$_SESSION['usuario_inscrito']= $inscrito;
$_SESSION['access_token']    = $accessToken;

// Redireciona para a página principal
header('Location: /?login=sucesso');
exit;
