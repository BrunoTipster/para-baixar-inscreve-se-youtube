<?php
/**
 * admin/usuarios.php — Listagem de Usuários
 * DJ Bruno Pendrives
 */

session_name('dj_bruno_session');
session_start();

if (empty($_SESSION['admin_logado'])) {
    header('Location: /admin/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$db = getDB();

// Paginação
$porPagina = 20;
$pagina = max(1, filter_input(INPUT_GET, 'p', FILTER_VALIDATE_INT) ?? 1);
$offset = ($pagina - 1) * $porPagina;

$total = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalPaginas = ceil($total / $porPagina);

$usuarios = $db->query("
    SELECT u.*, 
           (SELECT COUNT(*) FROM downloads d WHERE d.usuario_id = u.id) as qtd_downloads
    FROM usuarios u
    ORDER BY u.data_cadastro DESC
    LIMIT $porPagina OFFSET $offset
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuários — Admin DJ Bruno Pendrives</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<div class="admin-layout">
  <div class="admin-sidebar-overlay" id="adminOverlay"></div>
  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <div class="admin-main">
    <div class="admin-topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="admin-mobile-toggle" id="adminToggle" aria-label="Menu">
          <span class="toggle-icon"><span></span><span></span><span></span></span>
        </button>
        <h2>👥 Usuários Cadastrados</h2>
      </div>
      <a href="/admin/logout.php" class="btn btn-outline btn-sm">🚪 Sair</a>
    </div>

    <div class="admin-content">
      <div class="admin-table-wrap">
        <div class="admin-table-header">
          <h3>Total: <?= number_format($total) ?> usuários</h3>
          <div style="font-size:0.8rem;color:var(--cor-texto-dim);">
            Página <?= $pagina ?> de <?= max(1, $totalPaginas) ?>
          </div>
        </div>

        <div style="overflow-x:auto;">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Usuário</th>
                <th>Email</th>
                <th>Inscrito</th>
                <th>Downloads</th>
                <th>Cadastro</th>
                <th>Último Acesso</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $u): ?>
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:10px;">
                    <?php if (!empty($u['foto'])): ?>
                      <img src="<?= htmlspecialchars($u['foto']) ?>"
                           style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid var(--cor-borda);">
                    <?php else: ?>
                      <div style="width:36px;height:36px;border-radius:50%;background:rgba(155,48,255,0.15);display:flex;align-items:center;justify-content:center;">👤</div>
                    <?php endif; ?>
                    <span style="font-weight:600;font-size:0.88rem;"><?= htmlspecialchars($u['nome']) ?></span>
                  </div>
                </td>
                <td style="font-size:0.82rem;color:var(--cor-texto-dim);"><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <?php if ($u['inscrito']): ?>
                    <span class="badge badge-verde">✅ Inscrito</span>
                  <?php else: ?>
                    <span class="badge badge-vermelho">🔒 Não inscrito</span>
                  <?php endif; ?>
                </td>
                <td><span class="badge badge-roxo"><?= number_format($u['qtd_downloads']) ?></span></td>
                <td style="font-size:0.78rem;color:var(--cor-texto-dim);">
                  <?= date('d/m/Y', strtotime($u['data_cadastro'])) ?>
                </td>
                <td style="font-size:0.78rem;color:var(--cor-texto-dim);">
                  <?= $u['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acesso'])) : '—' ?>
                </td>
              </tr>
              <?php endforeach; ?>

              <?php if (empty($usuarios)): ?>
                <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--cor-texto-dim);">Nenhum usuário cadastrado ainda.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
        <div style="padding:16px 20px;display:flex;gap:8px;justify-content:center;border-top:1px solid var(--cor-borda);">
          <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
            <a href="?p=<?= $p ?>"
               style="padding:6px 12px;border-radius:6px;font-size:0.82rem;font-weight:600;
                      <?= $p === $pagina
                          ? 'background:var(--cor-primaria);color:#fff;'
                          : 'background:rgba(255,255,255,0.05);color:var(--cor-texto-dim);' ?>">
              <?= $p ?>
            </a>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  const adminToggle = document.getElementById('adminToggle');
  const adminSidebar = document.querySelector('.admin-sidebar');
  const adminOverlay = document.getElementById('adminOverlay');
  if (adminToggle && adminSidebar) {
    adminToggle.addEventListener('click', function() {
      adminSidebar.classList.toggle('active');
      if (adminOverlay) adminOverlay.classList.toggle('active');
    });
    if (adminOverlay) {
      adminOverlay.addEventListener('click', function() {
        adminSidebar.classList.remove('active');
        adminOverlay.classList.remove('active');
      });
    }
  }
</script>
</body>
</html>
