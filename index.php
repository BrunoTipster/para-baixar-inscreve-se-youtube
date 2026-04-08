<?php
/**
 * index.php — Página Principal
 * DJ Bruno Pendrives — Sistema de Downloads com verificação de inscrição YouTube
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

iniciarSessao();

// Dados do usuário na sessão
$usuario = getUsuarioSessao();
$logado = estaAutenticado();
$inscrito = $logado && ($usuario['inscrito'] ?? false);

// Busca músicas ativas do banco de dados
$db = getDB();
$stmt = $db->prepare("SELECT * FROM musicas WHERE ativo = 1 ORDER BY data DESC");
$stmt->execute();
$musicas = $stmt->fetchAll();

// Gêneros únicos para filtro
$generos = array_unique(array_filter(array_column($musicas, 'genero')));
sort($generos);

// Total de downloads gerais
$stmtDl = $db->query("SELECT COALESCE(SUM(downloads), 0) as total FROM musicas WHERE ativo = 1");
$totalDownloads = $stmtDl->fetchColumn();

// Total de usuários
$stmtUs = $db->query("SELECT COUNT(*) FROM usuarios");
$totalUsuarios = $stmtUs->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Baixe as melhores músicas do DJ Bruno Pendrives. Inscreva-se no canal do YouTube e acesse downloads exclusivos gratuitos.">
  <meta property="og:title" content="DJ Bruno Pendrives — Downloads Exclusivos">
  <meta property="og:description" content="Músicas exclusivas para assinantes do canal @Hosttimerkelvin">
  <title>DJ Bruno Pendrives — Downloads Exclusivos</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- ======================================================
     NAVBAR
     ====================================================== -->
<header>
  <nav class="navbar">
    <div class="navbar-inner">
      <!-- Logo -->
      <a href="/" class="logo">
        <div class="logo-icon">🎵</div>
        <span>DJ Bruno</span>
      </a>

      <!-- Hamburger Menu (mobile) -->
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Abrir menu">
        <span></span><span></span><span></span>
      </button>

      <!-- Links de navegação -->
      <ul class="nav-links" id="navLinks">
        <li><a href="/" class="ativo">🏠 Início</a></li>
        <li><a href="https://www.youtube.com/@Hosttimerkelvin" target="_blank" rel="noopener">▶️ Canal YouTube</a></li>
        <?php if (!$logado): ?>
          <li><a href="/jamendo.php">▶️ Buscar YouTube</a></li>
        <li><a href="/login.php">🔑 Login</a></li>
        <?php else: ?>
          <li><a href="/jamendo.php">▶️ Buscar YouTube</a></li>
          <li><a href="/logout.php">🚪 Sair</a></li>
        <?php endif; ?>
      </ul>

      <!-- Usuário logado ou botão de login -->
      <div class="nav-user">
        <?php if ($logado): ?>
          <?php if ($inscrito): ?>
            <span class="status-inscricao status-inscrito" id="status-inscricao">✅ Inscrito</span>
          <?php else: ?>
            <span class="status-inscricao status-nao-inscrito" id="status-inscricao">🔒 Não inscrito</span>
          <?php endif; ?>
          <?php if (!empty($usuario['foto'])): ?>
            <img src="<?= htmlspecialchars($usuario['foto']) ?>" alt="Foto de perfil" class="nav-avatar">
          <?php endif; ?>
          <span class="nav-user-name"><?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?></span>
        <?php else: ?>
          <a href="/login.php" class="btn btn-primario btn-sm">🔑 Login com Google</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</header>

<!-- Main Content -->
<main>
  <!-- ======================================================
       HERO SECTION
       ====================================================== -->
  <section class="hero">
    <div class="hero-badge">
      <span class="dot"></span>
      Downloads Exclusivos — Canal Verificado
    </div>

    <h1>
      🎧 <span class="text-gradient">DJ Bruno</span><br>Pendrives
    </h1>

    <p>
      As melhores tracks eletrônicas, disponíveis exclusivamente para inscritos do canal.
      Inscreva-se e baixe gratuitamente!
    </p>

    <?php if (!$logado): ?>
      <a href="/login.php" class="btn btn-primario" id="btn-login-hero">
        🔑 Login com Google para Baixar
      </a>
    <?php elseif (!$inscrito): ?>
      <a href="https://www.youtube.com/@Hosttimerkelvin?sub_confirmation=1" target="_blank" rel="noopener" class="btn btn-vermelho" id="btn-inscrever-hero">
        ▶️ Inscrever-se no Canal
      </a>
    <?php else: ?>
      <span class="btn btn-verde" style="cursor:default;">✅ Downloads Liberados!</span>
    <?php endif; ?>

    <!-- Estatísticas -->
    <div class="hero-stats">
      <div class="hero-stat">
        <span class="hero-stat-num"><?= count($musicas) ?>+</span>
        <span class="hero-stat-label">Músicas</span>
      </div>
      <div class="hero-stat">
        <span class="hero-stat-num"><?= number_format($totalDownloads, 0, ',', '.') ?></span>
        <span class="hero-stat-label">Downloads</span>
      </div>
      <div class="hero-stat">
        <span class="hero-stat-num"><?= number_format($totalUsuarios, 0, ',', '.') ?></span>
        <span class="hero-stat-label">Membros</span>
      </div>
    </div>
  </section>

  <!-- ======================================================
       AVISO DE INSCRIÇÃO (apenas se logado e NÃO inscrito)
       ====================================================== -->
  <?php if ($logado && !$inscrito): ?>
  <div class="container">
    <div class="secao-inscricao" id="aviso-inscricao">
      <h3>🔒 Inscreva-se para liberar os downloads</h3>
      <p>
        Olá, <strong><?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?></strong>!
        Você está logado, mas ainda não está inscrito no canal <strong>@Hosttimerkelvin</strong>.
        Inscreva-se gratuitamente e volte aqui para liberar todos os downloads!
      </p>
      <div class="btns-grupo">
        <a href="https://www.youtube.com/@Hosttimerkelvin?sub_confirmation=1"
           target="_blank" rel="noopener"
           class="btn btn-vermelho" id="btn-inscrever-canal">
          ▶️ Inscrever no Canal
        </a>
        <button class="btn btn-outline" id="btn-ja-inscrevi" onclick="jaInscrevi()">
          ✅ Já me Inscrevi
        </button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ======================================================
       SEÇÃO DE MÚSICAS
       ====================================================== -->
  <div class="container">
    <!-- Busca e filtros -->
    <div class="secao-busca">
      <div class="campo-busca">
        <span class="icone-busca">🔍</span>
        <input type="text" id="busca-musica" placeholder="Buscar músicas..." autocomplete="off">
      </div>

      <div class="filtros-genero">
        <button class="filtro-btn ativo" data-genero="todos" onclick="filtrarPorGenero('todos')">Todos</button>
        <?php foreach ($generos as $genero): ?>
          <button class="filtro-btn" data-genero="<?= htmlspecialchars($genero) ?>"
                  onclick="filtrarPorGenero('<?= htmlspecialchars($genero) ?>')">
            <?= htmlspecialchars($genero) ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Grid de Músicas -->
    <?php if (empty($musicas)): ?>
      <div style="text-align:center; padding: 80px 20px; color: var(--cor-texto-dim);">
        <div style="font-size:3rem;margin-bottom:16px;">🎵</div>
        <h3>Nenhuma música cadastrada ainda</h3>
        <p style="margin-top:8px;">Acesse o painel admin para cadastrar músicas.</p>
      </div>
    <?php else: ?>
    <div class="musicas-grid" id="musicas-grid">
      <?php foreach ($musicas as $musica): ?>
      <?php
        // Define URL da capa (usa data URI se não tiver imagem)
        if (!empty($musica['capa']) && file_exists(__DIR__ . '/' . $musica['capa'])) {
          $capa = '/' . htmlspecialchars($musica['capa']);
        } elseif (!empty($musica['capa']) && (strpos($musica['capa'], 'http://') === 0 || strpos($musica['capa'], 'https://') === 0)) {
          // É uma URL externa
          $capa = htmlspecialchars($musica['capa']);
        } else {
          // SVG placeholder inline
          $capa = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="400"%3E%3Crect fill="%231a1a2e" width="400" height="400"/%3E%3Ctext x="50%25" y="50%25" font-size="120" text-anchor="middle" dy=".3em"%3E🎵%3C/text%3E%3C/svg%3E';
        }
        
        // Verifica se é link externo
        $isLinkExterno = (
          strpos($musica['arquivo'], 'http://') === 0 || 
          strpos($musica['arquivo'], 'https://') === 0 ||
          strpos($musica['arquivo'], 'drive.google.com') !== false ||
          strpos($musica['arquivo'], 'mediafire.com') !== false ||
          strpos($musica['arquivo'], 'gofile.io') !== false
        );
      ?>
      <div class="card-musica"
           data-genero="<?= htmlspecialchars($musica['genero']) ?>"
           data-titulo="<?= htmlspecialchars($musica['titulo']) ?>"
           data-artista="<?= htmlspecialchars($musica['artista']) ?>">

        <!-- Capa -->
        <div class="card-capa-wrapper">
          <img src="<?= $capa ?>"
               alt="Capa: <?= htmlspecialchars($musica['titulo']) ?>"
               class="card-capa"
               loading="lazy">
          <div class="card-overlay"></div>
          <span class="card-badge"><?= htmlspecialchars($musica['genero']) ?></span>
          <?php if (!$inscrito): ?>
            <div style="position:absolute;bottom:12px;left:12px;font-size:1.4rem;">🔒</div>
          <?php endif; ?>
        </div>

        <!-- Conteúdo do card -->
        <div class="card-body">
          <h2 class="card-titulo" title="<?= htmlspecialchars($musica['titulo']) ?>">
            <?= htmlspecialchars($musica['titulo']) ?>
          </h2>
          <div class="card-artista">
            🎤 <?= htmlspecialchars($musica['artista']) ?>
          </div>

          <div class="card-meta">
            <?php if ($isLinkExterno): ?>
            <span>🔗 Link Externo</span>
            <?php elseif (!empty($musica['duracao'])): ?>
            <span>⏱️ <?= htmlspecialchars($musica['duracao']) ?></span>
            <?php endif; ?>
            <span>
              ⬇️ <span data-downloads="<?= $musica['id'] ?>"><?= number_format($musica['downloads'], 0, ',', '.') ?></span>
            </span>
          </div>

          <!-- Botão de download (lógica conforme estado do usuário) -->
          <?php if (!$logado): ?>
            <!-- Não logado: pede login -->
            <button class="btn-download btn-bloqueado"
                    data-musica-id="<?= $musica['id'] ?>"
                    onclick="abrirModalLogin()"
                    id="download-btn-<?= $musica['id'] ?>">
              🔒 Login para Baixar
            </button>
          <?php elseif (!$inscrito): ?>
            <!-- Logado mas não inscrito -->
            <button class="btn-download btn-bloqueado"
                    data-musica-id="<?= $musica['id'] ?>"
                    onclick="abrirModalInscricao(<?= $musica['id'] ?>)"
                    id="download-btn-<?= $musica['id'] ?>">
              🔒 Inscreva-se para Baixar
            </button>
          <?php else: ?>
            <!-- Inscrito: download liberado -->
            <button class="btn-download btn-liberado"
                    data-musica-id="<?= $musica['id'] ?>"
                    onclick="baixarMusica(<?= $musica['id'] ?>, this)"
                    id="download-btn-<?= $musica['id'] ?>">
              <?= $isLinkExterno ? '🔗 Acessar Link' : '✅ Baixar MP3' ?>
            </button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</main>

<!-- ======================================================
     FOOTER
     ====================================================== -->
<footer class="footer">
  <div class="footer-logo text-gradient">DJ Bruno Pendrives</div>
  <p class="footer-text">Downloads exclusivos para inscritos do canal</p>
  <div class="footer-links">
    <a href="https://www.youtube.com/@Hosttimerkelvin" target="_blank" rel="noopener">▶️ YouTube</a>
    <a href="/login.php">🔑 Login</a>
    <a href="/admin/">⚙️ Admin</a>
  </div>
  <p class="footer-copy">© <?= date('Y') ?> DJ Bruno Pendrives — @Hosttimerkelvin. Todos os direitos reservados.</p>
</footer>

<!-- ======================================================
     MODAL: LOGIN OBRIGATÓRIO
     ====================================================== -->
<div id="modal-login" class="modal-overlay" style="display:none;">
  <div class="modal-box">
    <span class="modal-icon">🔒</span>
    <h2>Acesso Restrito</h2>
    <p>
      Você precisa fazer login com sua conta Google para baixar as músicas.
      Após o login, basta se inscrever no canal e os downloads serão liberados automaticamente!
    </p>
    <div class="modal-btns">
      <a href="/login.php" class="btn btn-google" id="modal-btn-login">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
        Entrar com Google
      </a>
      <button class="btn btn-outline" onclick="fecharModalLogin()" id="modal-btn-fechar-login">
        Fechar
      </button>
    </div>
  </div>
</div>

<!-- ======================================================
     MODAL: PEDE INSCRIÇÃO NO CANAL
     ====================================================== -->
<div id="modal-inscricao" class="modal-overlay" style="display:none;" data-musica-id="">
  <div class="modal-box">
    <span class="modal-icon">▶️</span>
    <h2>Inscreva-se no Canal!</h2>
    <p>
      Para baixar as músicas, você precisa estar inscrito no canal
      <strong>@Hosttimerkelvin</strong> no YouTube. A inscrição é gratuita!
    </p>
    <div class="modal-btns">
      <a href="https://www.youtube.com/@Hosttimerkelvin?sub_confirmation=1"
         target="_blank" rel="noopener"
         class="btn btn-vermelho" id="modal-btn-canal">
        ▶️ Inscrever no Canal
      </a>
      <button class="btn btn-verde" id="btn-ja-inscrevi" onclick="jaInscrevi()">
        ✅ Já me Inscrevi — Verificar
      </button>
      <button class="btn btn-outline" onclick="fecharModalInscricao()" id="modal-btn-fechar-inscricao">
        Fechar
      </button>
    </div>
  </div>
</div>

<!-- ======================================================
     CONTAINER DE TOASTS
     ====================================================== -->
<div id="toast-container" class="toast-container"></div>

<!-- Variáveis PHP → JS -->
<script>
  window.usuarioLogado  = <?= $logado ? 'true' : 'false' ?>;
  window.usuarioInscrito = <?= $inscrito ? 'true' : 'false' ?>;
</script>
<script src="/assets/js/main.js"></script>
<script>
  // Hamburger menu toggle
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const navLinks = document.getElementById('navLinks');
  if (hamburgerBtn && navLinks) {
    hamburgerBtn.addEventListener('click', function() {
      this.classList.toggle('active');
      navLinks.classList.toggle('active');
    });
    // Close menu when clicking a link
    navLinks.querySelectorAll('a').forEach(function(link) {
      link.addEventListener('click', function() {
        hamburgerBtn.classList.remove('active');
        navLinks.classList.remove('active');
      });
    });
  }
</script>

</body>
</html>
