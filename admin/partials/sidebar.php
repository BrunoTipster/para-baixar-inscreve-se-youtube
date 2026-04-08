<nav class="admin-sidebar">
  <div class="admin-sidebar-header">
    <a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
      <div class="logo-icon" style="width:34px;height:34px;font-size:1rem;">🎵</div>
      <div>
        <div style="font-size:0.78rem;font-weight:700;color:var(--cor-texto);">DJ Bruno</div>
        <div style="font-size:0.68rem;color:var(--cor-texto-dim);">Pendrives Admin</div>
      </div>
    </a>
  </div>

  <div class="admin-sidebar-title">Menu</div>

  <ul class="admin-sidebar-nav">
    <li>
      <a href="/admin/"
         class="<?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' && str_contains($_SERVER['SCRIPT_NAME'], '/admin/') ? 'ativo' : '' ?>">
        📊 Dashboard
      </a>
    </li>
    <li>
      <a href="/admin/musicas.php"
         class="<?= str_contains($_SERVER['SCRIPT_NAME'], 'musicas.php') ? 'ativo' : '' ?>">
        🎵 Gerenciar Músicas
      </a>
    </li>
    <li>
      <a href="/admin/usuarios.php"
         class="<?= str_contains($_SERVER['SCRIPT_NAME'], 'usuarios.php') ? 'ativo' : '' ?>">
        👥 Usuários
      </a>
    </li>
  </ul>

  <div style="padding:16px 12px;border-top:1px solid var(--cor-borda);margin-top:auto;">
    <a href="/" class="admin-sidebar-nav" style="display:flex;align-items:center;gap:8px;
       font-size:0.85rem;color:var(--cor-texto-dim);text-decoration:none;padding:10px;">
      ← Ver Site
    </a>
    <a href="/admin/logout.php" class="admin-sidebar-nav" style="display:flex;align-items:center;gap:8px;
       font-size:0.85rem;color:var(--cor-vermelho);text-decoration:none;padding:10px;">
      🚪 Fazer Logout
    </a>
  </div>
</nav>
