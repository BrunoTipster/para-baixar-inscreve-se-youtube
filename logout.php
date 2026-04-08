<?php
/**
 * logout.php — Encerra a sessão do usuário
 * DJ Bruno Pendrives
 */

require_once __DIR__ . '/config/session.php';

iniciarSessao();

// Destroi a sessão completamente
destruirSessao();

// Redireciona para a página inicial
header('Location: /?logout=ok');
exit;
