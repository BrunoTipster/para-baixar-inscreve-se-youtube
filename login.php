<?php
/**
 * login.php — Página de Login com Google OAuth
 * DJ Bruno Pendrives
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/youtube.php';

iniciarSessao();

// Se já está logado, redireciona para a página principal
if (estaAutenticado()) {
    header('Location: /');
    exit;
}

// Gera token CSRF e armazena na sessão
$csrfToken = bin2hex(random_bytes(32));
$_SESSION['oauth_state'] = $csrfToken;

// Gera URL de autenticação Google
$urlLogin = getGoogleAuthUrl($csrfToken);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Faça login com sua conta Google para acessar os downloads exclusivos do DJ Bruno Pendrives.">
  <title>Login — DJ Bruno Pendrives</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="login-page">

  <div class="login-card">
    <!-- Logo -->
    <div class="login-logo">🎵</div>

    <h1>DJ Bruno <span class="text-gradient">Pendrives</span></h1>
    <p>
      Faça login com sua conta Google para acessar os <strong>downloads exclusivos</strong>.
      Após o login, basta confirmar sua inscrição no canal e tudo é liberado!
    </p>

    <!-- Como funciona -->
    <div class="login-features">
      <div class="login-feature">
        <span class="icon">1️⃣</span>
        <span>Faça login com sua conta <strong>Google</strong></span>
      </div>
      <div class="login-feature">
        <span class="icon">2️⃣</span>
        <span>Se inscreva no canal <strong>@Hosttimerkelvin</strong></span>
      </div>
      <div class="login-feature">
        <span class="icon">3️⃣</span>
        <span>Baixe todas as músicas <strong>gratuitamente</strong> ✅</span>
      </div>
    </div>

    <!-- Botão de Login -->
    <a href="<?= htmlspecialchars($urlLogin) ?>" class="btn btn-google" id="btn-login-google">
      <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Logo Google">
      Entrar com Google
    </a>

    <div class="login-divider"><span>ou</span></div>

    <a href="/" class="btn btn-outline" id="btn-voltar">
      ← Voltar
    </a>

    <p style="margin-top:20px; font-size:0.75rem; color: var(--cor-texto-dim); line-height:1.6;">
      Ao fazer login, você concorda com nossos termos de uso.
      Apenas dados básicos do perfil e acesso de leitura ao YouTube são solicitados.
      Nunca postamos em seu nome.
    </p>
  </div>

</body>
</html>
