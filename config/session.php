<?php
/**
 * Configuração de sessão segura
 * DJ Bruno Pendrives - Sistema de Downloads
 */

// Configurações de segurança da sessão
ini_set('session.cookie_httponly', 1);      // Impede acesso via JavaScript
ini_set('session.cookie_secure', 0);        // Mude para 1 em produção com HTTPS
ini_set('session.use_strict_mode', 1);      // Rejeita IDs de sessão inválidos
ini_set('session.cookie_samesite', 'Lax'); // Proteção CSRF
ini_set('session.gc_maxlifetime', 3600);   // Sessão expira em 1 hora

// Nome da sessão personalizado
session_name('dj_bruno_session');

/**
 * Inicia a sessão com configurações de segurança
 */
function iniciarSessao(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Regenera o ID da sessão para prevenir session fixation
 */
function regenerarSessao(): void {
    session_regenerate_id(true);
}

/**
 * Verifica se o usuário está autenticado
 * @return bool
 */
function estaAutenticado(): bool {
    iniciarSessao();
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Verifica se o usuário logado é administrador
 * @return bool
 */
function eAdmin(): bool {
    iniciarSessao();
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Retorna os dados do usuário da sessão
 * @return array|null
 */
function getUsuarioSessao(): ?array {
    iniciarSessao();
    if (estaAutenticado()) {
        return [
            'id'       => $_SESSION['usuario_id'] ?? null,
            'nome'     => $_SESSION['usuario_nome'] ?? '',
            'email'    => $_SESSION['usuario_email'] ?? '',
            'foto'     => $_SESSION['usuario_foto'] ?? '',
            'inscrito' => $_SESSION['usuario_inscrito'] ?? false,
            'token'    => $_SESSION['access_token'] ?? '',
        ];
    }
    return null;
}

/**
 * Gera um token CSRF seguro
 * @return string
 */
function gerarTokenCsrf(): string {
    iniciarSessao();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida um token CSRF
 * @param string $token
 * @return bool
 */
function validarTokenCsrf(string $token): bool {
    iniciarSessao();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Destroi a sessão completamente
 */
function destruirSessao(): void {
    iniciarSessao();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}
