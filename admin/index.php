<?php
/**
 * admin/index.php — Painel Admin com Estatísticas
 * DJ Bruno Pendrives
 */

session_name('dj_bruno_session');
session_start();

// Verifica se admin está logado
if (empty($_SESSION['admin_logado'])) {
    header('Location: /admin/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$db = getDB();

// ============================================================
// ESTATÍSTICAS
// ============================================================
$stats = [
    'musicas'    => $db->query("SELECT COUNT(*) FROM musicas WHERE ativo = 1")->fetchColumn(),
    'usuarios'   => $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
    'inscritos'  => $db->query("SELECT COUNT(*) FROM usuarios WHERE inscrito = 1")->fetchColumn(),
    'downloads'  => $db->query("SELECT COUNT(*) FROM downloads")->fetchColumn(),
];

// Downloads recentes
$stmtRecentes = $db->query("
    SELECT d.data_download, u.nome, u.foto, m.titulo, m.artista
    FROM downloads d
    JOIN usuarios u ON u.id = d.usuario_id
    JOIN musicas m ON m.id = d.musica_id
    ORDER BY d.data_download DESC
    LIMIT 15
");
$recentes = $stmtRecentes->fetchAll();

// Músicas mais baixadas
$stmtTop = $db->query("
    SELECT titulo, artista, downloads, genero
    FROM musicas
    WHERE ativo = 1
    ORDER BY downloads DESC
    LIMIT 10
");
$topMusicas = $stmtTop->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — DJ Bruno Pendrives</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<div class="admin-layout">
  <!-- Overlay para mobile -->
  <div class="admin-sidebar-overlay" id="adminOverlay"></div>

  <!-- Sidebar -->
  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <!-- Conteúdo Principal -->
  <div class="admin-main">
    <!-- Topbar -->
    <div class="admin-topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="admin-mobile-toggle" id="adminToggle" aria-label="Menu">
          <span class="toggle-icon"><span></span><span></span><span></span></span>
        </button>
        <h2>📊 Dashboard</h2>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:0.85rem;color:var(--cor-texto-dim);">
          Admin: <strong><?= htmlspecialchars($_SESSION['admin_usuario']) ?></strong>
        </span>
        <a href="/admin/logout.php" class="btn btn-outline btn-sm">🚪 Sair</a>
      </div>
    </div>

    <div class="admin-content">
      <!-- Cards de Estatísticas -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon roxo">🎵</div>
          <div>
            <div class="stat-info-num text-gradient"><?= number_format($stats['musicas']) ?></div>
            <div class="stat-info-label">Músicas Ativas</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon azul">👥</div>
          <div>
            <div class="stat-info-num" style="color:#007AFF;"><?= number_format($stats['usuarios']) ?></div>
            <div class="stat-info-label">Usuários Cadastrados</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon verde">✅</div>
          <div>
            <div class="stat-info-num" style="color:var(--cor-verde);"><?= number_format($stats['inscritos']) ?></div>
            <div class="stat-info-label">Inscritos no Canal</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon vermelho">⬇️</div>
          <div>
            <div class="stat-info-num" style="color:var(--cor-vermelho);"><?= number_format($stats['downloads']) ?></div>
            <div class="stat-info-label">Downloads Totais</div>
          </div>
        </div>
      </div>

      <!-- Dois painéis lado a lado -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;flex-wrap:wrap;">
        <!-- Downloads Recentes -->
        <div class="admin-table-wrap">
          <div class="admin-table-header">
            <h3>⬇️ Downloads Recentes</h3>
          </div>
          <?php if (empty($recentes)): ?>
            <p style="padding:24px;color:var(--cor-texto-dim);text-align:center;">Nenhum download ainda.</p>
          <?php else: ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Usuário</th>
                <th>Música</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentes as $dl): ?>
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <?php if (!empty($dl['foto'])): ?>
                      <img src="<?= htmlspecialchars($dl['foto']) ?>" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                    <?php endif; ?>
                    <span style="font-size:0.82rem;"><?= htmlspecialchars(explode(' ', $dl['nome'])[0]) ?></span>
                  </div>
                </td>
                <td style="font-size:0.82rem;"><?= htmlspecialchars($dl['titulo']) ?></td>
                <td style="font-size:0.75rem;color:var(--cor-texto-dim);">
                  <?= date('d/m H:i', strtotime($dl['data_download'])) ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>

        <!-- Top Músicas -->
        <div class="admin-table-wrap">
          <div class="admin-table-header">
            <h3>🏆 Mais Baixadas</h3>
          </div>
          <?php if (empty($topMusicas)): ?>
            <p style="padding:24px;color:var(--cor-texto-dim);text-align:center;">Nenhum dado ainda.</p>
          <?php else: ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Música</th>
                <th>Downloads</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($topMusicas as $i => $m): ?>
              <tr>
                <td>
                  <span style="font-weight:700;color:var(--cor-primaria);"><?= $i + 1 ?>º</span>
                </td>
                <td>
                  <div style="font-size:0.85rem;font-weight:600;"><?= htmlspecialchars($m['titulo']) ?></div>
                  <div style="font-size:0.75rem;color:var(--cor-texto-dim);"><?= htmlspecialchars($m['artista']) ?></div>
                </td>
                <td>
                  <span class="badge badge-roxo"><?= number_format($m['downloads']) ?></span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  // Admin sidebar toggle
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
