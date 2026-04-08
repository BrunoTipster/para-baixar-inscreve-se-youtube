<?php
/**
 * admin/login.php — Login do Painel Administrativo
 * DJ Bruno Pendrives
 */

session_name('dj_bruno_session');
session_start();

// Já está logado? redireciona
if (!empty($_SESSION['admin_logado'])) {
    header('Location: /admin/');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);
    $senha   = $_POST['senha'] ?? '';

    if ($usuario && $senha) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admins WHERE usuario = ?");
        $stmt->execute([trim($usuario)]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($senha, $admin['senha'])) {
            // Login bem-sucedido
            session_regenerate_id(true);
            $_SESSION['admin_logado']  = true;
            $_SESSION['admin_usuario'] = $admin['usuario'];
            $_SESSION['admin_id']      = $admin['id'];

            header('Location: /admin/');
            exit;
        } else {
            $erro = 'Usuário ou senha inválidos.';
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — DJ Bruno Pendrives</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="login-page">

  <div class="login-card" style="max-width:400px;">
    <div class="login-logo">⚙️</div>
    <h1 style="font-size:1.5rem;">Painel <span class="text-gradient">Admin</span></h1>
    <p style="margin-bottom:24px;">DJ Bruno Pendrives — Acesso restrito</p>

    <?php if ($erro): ?>
    <div style="background:rgba(229,9,20,0.12);border:1px solid rgba(229,9,20,0.3);
                border-radius:10px;padding:12px 16px;margin-bottom:20px;
                color:#ff4757;font-size:0.88rem;text-align:left;">
      ❌ <?= htmlspecialchars($erro) ?>
    </div>
    <?php endif; ?>

    <form method="POST" id="form-admin-login">
      <div class="form-group" style="margin-bottom:16px;text-align:left;">
        <label for="usuario" style="font-size:0.8rem;font-weight:600;color:var(--cor-texto-dim);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:8px;">
          Usuário
        </label>
        <input type="text" id="usuario" name="usuario" class="form-control"
               placeholder="admin" required autocomplete="username"
               style="width:100%;">
      </div>

      <div class="form-group" style="margin-bottom:24px;text-align:left;">
        <label for="senha" style="font-size:0.8rem;font-weight:600;color:var(--cor-texto-dim);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:8px;">
          Senha
        </label>
        <input type="password" id="senha" name="senha" class="form-control"
               placeholder="••••••••" required autocomplete="current-password"
               style="width:100%;">
      </div>

      <button type="submit" class="btn btn-primario" style="width:100%;padding:13px;" id="btn-admin-entrar">
        🔑 Entrar no Painel
      </button>
    </form>

    <a href="/" style="display:block;margin-top:20px;font-size:0.82rem;color:var(--cor-texto-dim);">
      ← Voltar ao site
    </a>
  </div>

</body>
</html>
