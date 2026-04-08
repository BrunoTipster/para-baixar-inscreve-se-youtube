<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
iniciarSessao();
$usuario  = getUsuarioSessao();
$logado   = estaAutenticado();
$inscrito = $logado && ($usuario['inscrito'] ?? false);

// COLOQUE SUA CHAVE DE API DO YOUTUBE AQUI
define('YOUTUBE_API_KEY', 'AIzaSyDBWr_apNhFjc8xoEx3sDlcEy14InRFARg');
define('YOUTUBE_CHANNEL_HANDLE', '@Hosttimerkelvin');

// Busca server-side via PHP (evita expor chave no JS)
$resultados = [];
$erro_busca = '';
$total = 0;

if (!empty($_GET['q'])) {
    $termo = trim(strip_tags($_GET['q']));
    $maxResults = min((int)($_GET['limit'] ?? 20), 50);
    $order = in_array($_GET['order'] ?? '', ['relevance','date','rating','viewCount','title'])
             ? $_GET['order'] : 'relevance';

    $url = 'https://www.googleapis.com/youtube/v3/search?' . http_build_query([
        'part'       => 'snippet',
        'q'          => $termo,
        'type'       => 'video',
        'maxResults' => $maxResults,
        'order'      => $order,
        'key'        => YOUTUBE_API_KEY,
        'relevanceLanguage' => 'pt',
        'regionCode' => 'BR',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    $curl_err  = curl_error($ch);
    curl_close($ch);

    if ($curl_err) {
        $erro_busca = 'Erro de conexão: ' . $curl_err;
    } elseif ($response) {
        $data = json_decode($response, true);
        if (isset($data['error'])) {
            $erro_busca = $data['error']['message'] ?? 'Erro na API do YouTube';
        } else {
            $resultados = $data['items'] ?? [];
            $total = $data['pageInfo']['totalResults'] ?? count($resultados);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buscar Músicas — DJ Bruno Pendrives</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    .yt-hero{text-align:center;padding:50px 20px 20px}
    @media (max-width:768px){.yt-hero{padding:40px 16px 16px}.yt-hero h1{font-size:clamp(1.4rem,4vw,2rem)}}
    @media (max-width:480px){.yt-hero{padding:32px 12px 12px}.yt-hero h1{font-size:1.3rem;margin-bottom:6px}}
    .yt-hero h1{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:800;margin-bottom:8px}
    .yt-hero p{color:var(--cor-texto-dim);margin-bottom:20px}
    .badge{display:inline-block;background:rgba(255,0,0,.12);border:1px solid rgba(255,0,0,.3);color:#ff4444;padding:4px 14px;border-radius:20px;font-size:.8rem;margin-bottom:14px}

    .busca-wrap{max-width:720px;margin:0 auto 20px;padding:0 20px}
    .busca-form{display:flex;gap:10px;margin-bottom:12px}
    @media (max-width:640px){.busca-form{flex-direction:column;gap:8px}.busca-input{width:100%}.btn-buscar{width:100%}}
    .busca-input{flex:1;background:var(--cor-fundo-card);border:1.5px solid var(--cor-borda);border-radius:var(--radius-sm);color:var(--cor-texto);font-family:'Poppins',sans-serif;font-size:1rem;padding:13px 18px;outline:none;transition:var(--transicao)}
    .busca-input:focus{border-color:#ff0000;box-shadow:0 0 0 3px rgba(255,0,0,.1)}
    .btn-buscar{background:#ff0000;color:#fff;border:none;border-radius:var(--radius-sm);padding:13px 22px;font-family:'Poppins',sans-serif;font-size:1rem;font-weight:700;cursor:pointer;white-space:nowrap;transition:var(--transicao)}
    .btn-buscar:hover{background:#cc0000;transform:translateY(-1px)}

    .filtros{display:flex;gap:8px;flex-wrap:wrap}
    @media (max-width:480px){.filtros{flex-direction:column;gap:6px}.filtros select{width:100%}}
    .filtros select{background:var(--cor-fundo-card);border:1px solid var(--cor-borda);color:var(--cor-texto);font-family:'Poppins',sans-serif;font-size:.88rem;padding:8px 12px;border-radius:var(--radius-sm);cursor:pointer;outline:none}

    .aviso{max-width:720px;margin:0 auto 20px;padding:10px 20px;background:rgba(255,200,0,.06);border:1px solid rgba(255,200,0,.25);border-radius:var(--radius-sm);font-size:.82rem;color:#ffcc00;text-align:center}
    @media (max-width:480px){.aviso{margin:0 auto 12px;padding:8px 14px;font-size:.76rem}}

    .erro-msg{max-width:720px;margin:0 auto 20px;padding:12px 20px;background:rgba(229,9,20,.08);border:1px solid rgba(229,9,20,.3);border-radius:var(--radius-sm);color:#e50914;text-align:center;font-size:.88rem}
    @media (max-width:480px){.erro-msg{margin:0 auto 12px;padding:10px 14px;font-size:.8rem}}

    .resultados{max-width:1200px;margin:0 auto;padding:0 20px 60px}
    .total-txt{color:var(--cor-texto-dim);font-size:.88rem;margin-bottom:18px}
    .yt-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:20px}
    @media (max-width:768px){.yt-grid{grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px}}
    @media (max-width:640px){.yt-grid{grid-template-columns:1fr 1fr;gap:12px}}
    @media (max-width:480px){.yt-grid{grid-template-columns:1fr 1fr;gap:10px}}
    @media (max-width:360px){.yt-grid{grid-template-columns:1fr;gap:10px}}

    .card-yt{background:var(--cor-fundo-card);border:1px solid var(--cor-borda);border-radius:var(--radius);overflow:hidden;transition:var(--transicao)}
    .card-yt:hover{border-color:rgba(255,0,0,.4);transform:translateY(-4px);box-shadow:0 8px 32px rgba(255,0,0,.15)}
    .thumb-wrap{position:relative;aspect-ratio:16/9;overflow:hidden;background:#111}
    .thumb-wrap img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s}
    .card-yt:hover .thumb-wrap img{transform:scale(1.04)}
    .thumb-overlay{position:absolute;inset:0;background:rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .3s}
    .card-yt:hover .thumb-overlay{opacity:1}
    .play-icon{width:52px;height:52px;background:rgba(255,0,0,.9);border-radius:50%;display:flex;align-items:center;justify-content:center}
    .play-icon svg{width:22px;height:22px;fill:#fff;margin-left:3px}
    .card-yt-body{padding:14px}
    @media (max-width:640px){.card-yt-body{padding:10px}.card-yt-titulo{font-size:.84rem}.card-yt-canal{font-size:.72rem;margin-bottom:4px}.btn-yt{padding:8px;font-size:.82rem}.meta-tag{font-size:.65rem;padding:2px 5px}}
    @media (max-width:480px){.card-yt-body{padding:8px}.card-yt-titulo{font-size:.8rem;-webkit-line-clamp:1}.card-yt-canal{font-size:.68rem}.btn-yt{padding:7px;font-size:.76rem}}
    .card-yt-titulo{font-size:.92rem;font-weight:700;margin-bottom:5px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.4}
    .card-yt-canal{font-size:.78rem;color:var(--cor-texto-dim);margin-bottom:6px}
    .card-yt-meta{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}
    .meta-tag{background:rgba(255,0,0,.1);color:#ff6666;font-size:.7rem;padding:2px 7px;border-radius:20px;border:1px solid rgba(255,0,0,.2)}
    .btn-yt{display:block;width:100%;text-align:center;padding:10px;border-radius:var(--radius-sm);font-family:'Poppins',sans-serif;font-size:.88rem;font-weight:700;cursor:pointer;border:none;transition:var(--transicao);text-decoration:none}
    .btn-yt-livre{background:linear-gradient(135deg,#ff0000,#cc0000);color:#fff}
    .btn-yt-livre:hover{transform:translateY(-2px);box-shadow:0 4px 14px rgba(255,0,0,.35)}
    .btn-yt-lock{background:var(--cor-fundo-hover);color:var(--cor-texto-dim);border:1px solid var(--cor-borda);cursor:not-allowed}

    .vazio{text-align:center;padding:60px 20px;color:var(--cor-texto-dim)}
    .vazio .ic{font-size:3rem;margin-bottom:12px}
    @media (max-width:480px){.vazio{padding:40px 16px}.vazio .ic{font-size:2.2rem}}

    .api-info{max-width:720px;margin:0 auto 20px;padding:12px 20px;background:var(--cor-fundo-card);border:1px solid var(--cor-borda);border-radius:var(--radius-sm);font-size:.8rem;color:var(--cor-texto-dim)}
    @media (max-width:480px){.api-info{margin:0 auto 12px;padding:10px 14px;font-size:.75rem}}
    .api-info strong{color:var(--cor-texto)}
  </style>
</head>
<body>

<header>
  <nav class="navbar">
    <div class="navbar-inner">
      <a href="/" class="logo"><div class="logo-icon">🎵</div><span>DJ Bruno</span></a>
      <!-- Hamburger Menu (mobile) -->
      <button class="hamburger-btn" id="hamburgerBtn" aria-label="Abrir menu">
        <span></span><span></span><span></span>
      </button>
      <ul class="nav-links" id="navLinks">
        <li><a href="/">🏠 Início</a></li>
        <li><a href="/jamendo.php" class="ativo">▶️ Buscar YouTube</a></li>
        <li><a href="https://www.youtube.com/<?= YOUTUBE_CHANNEL_HANDLE ?>" target="_blank" rel="noopener">📺 Canal</a></li>
        <?php if(!$logado): ?>
          <li><a href="/login.php">🔑 Login</a></li>
        <?php else: ?>
          <li><a href="/logout.php">🚪 Sair</a></li>
        <?php endif; ?>
      </ul>
      <div class="nav-user">
        <?php if($logado): ?>
          <?php if($inscrito): ?>
            <span class="status-inscricao status-inscrito">✅ Inscrito</span>
          <?php else: ?>
            <span class="status-inscricao status-nao-inscrito">🔒 Não inscrito</span>
          <?php endif; ?>
          <?php if(!empty($usuario['foto'])): ?>
            <img src="<?=htmlspecialchars($usuario['foto'])?>" alt="Foto" class="nav-avatar">
          <?php endif; ?>
          <span class="nav-user-name"><?=htmlspecialchars(explode(' ',$usuario['nome'])[0])?></span>
        <?php else: ?>
          <a href="/login.php" class="btn btn-primario btn-sm">🔑 Login com Google</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</header>

<main>
  <div class="yt-hero">
    <div class="badge">▶️ YouTube Data API v3 — Fins Educacionais</div>
    <h1>🔍 <span class="text-gradient">Buscar no YouTube</span></h1>
    <p>Busque vídeos e músicas do YouTube — inscritos acessam o link direto</p>
  </div>

  <!-- Aviso -->
  <div class="aviso">
    ⚠️ Esta página é para fins de aprendizado. O YouTube não fornece download de MP3 — apenas link para o vídeo.
    <?php if(!$inscrito): ?><br>🔒 <strong>Inscreva-se no canal <?= YOUTUBE_CHANNEL_HANDLE ?> para acessar os links</strong><?php endif; ?>
  </div>

  <!-- Formulário de busca -->
  <div class="busca-wrap">
    <form class="busca-form" method="GET" action="/jamendo.php">
      <input type="text"
             name="q"
             class="busca-input"
             placeholder="🔍 Ex: funk 2026, sertanejo, pagode..."
             value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
             autocomplete="off"
             maxlength="100">
      <button type="submit" class="btn-buscar">▶️ Buscar</button>
    </form>
    <div class="filtros">
      <select name="order" form="busca-form" onchange="this.form.submit()">
        <option value="relevance" <?= ($_GET['order']??'')=='relevance'?'selected':'' ?>>📊 Relevância</option>
        <option value="date"      <?= ($_GET['order']??'')=='date'     ?'selected':'' ?>>🆕 Mais Recentes</option>
        <option value="viewCount" <?= ($_GET['order']??'')=='viewCount'?'selected':'' ?>>🔥 Mais Vistos</option>
        <option value="rating"    <?= ($_GET['order']??'')=='rating'   ?'selected':'' ?>>⭐ Melhor Avaliados</option>
        <option value="title"     <?= ($_GET['order']??'')=='title'    ?'selected':'' ?>>🔤 Título A-Z</option>
      </select>
      <select name="limit" form="busca-form" onchange="this.form.submit()">
        <option value="12" <?= ($_GET['limit']??'')=='12'?'selected':'' ?>>12 resultados</option>
        <option value="20" <?= ($_GET['limit']??20)=='20'?'selected':'' ?>>20 resultados</option>
        <option value="40" <?= ($_GET['limit']??'')=='40'?'selected':'' ?>>40 resultados</option>
      </select>
    </div>
  </div>

  <!-- Info da API -->
  <?php if(YOUTUBE_API_KEY === 'SUA_CHAVE_AQUI'): ?>
  <div class="erro-msg">
    ⚙️ <strong>Configure sua API Key!</strong> Abra o arquivo <code>jamendo.php</code> e substitua <code>SUA_CHAVE_AQUI</code> pela sua chave da YouTube Data API v3.
    <br>Obtenha gratuitamente em: <a href="https://console.cloud.google.com" target="_blank" style="color:#ff6666">console.cloud.google.com</a>
  </div>
  <?php elseif($erro_busca): ?>
  <div class="erro-msg">❌ <?= htmlspecialchars($erro_busca) ?></div>
  <?php endif; ?>

  <!-- Resultados -->
  <?php if(!empty($resultados)): ?>
  <div class="resultados">
    <p class="total-txt">
      Mostrando <?= count($resultados) ?> resultados para
      "<strong><?= htmlspecialchars($_GET['q']) ?></strong>"
    </p>
    <div class="yt-grid">
      <?php foreach($resultados as $i => $item):
        $videoId  = $item['id']['videoId'] ?? '';
        $snippet  = $item['snippet'] ?? [];
        $titulo   = $snippet['title'] ?? 'Sem título';
        $canal    = $snippet['channelTitle'] ?? '';
        $data     = isset($snippet['publishedAt'])
                    ? date('d/m/Y', strtotime($snippet['publishedAt'])) : '';
        $thumb    = $snippet['thumbnails']['high']['url']
                 ?? $snippet['thumbnails']['medium']['url']
                 ?? $snippet['thumbnails']['default']['url'] ?? '';
        $desc     = mb_substr($snippet['description'] ?? '', 0, 80);
        $ytLink   = "https://www.youtube.com/watch?v={$videoId}";
      ?>
      <div class="card-yt">
        <!-- Thumbnail -->
        <div class="thumb-wrap">
          <img src="<?= htmlspecialchars($thumb) ?>"
               alt="<?= htmlspecialchars($titulo) ?>"
               loading="<?= $i < 4 ? 'eager' : 'lazy' ?>">
          <div class="thumb-overlay">
            <div class="play-icon">
              <svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
          </div>
        </div>

        <!-- Corpo -->
        <div class="card-yt-body">
          <div class="card-yt-titulo" title="<?= htmlspecialchars($titulo) ?>">
            <?= htmlspecialchars($titulo) ?>
          </div>
          <div class="card-yt-canal">📺 <?= htmlspecialchars($canal) ?></div>
          <div class="card-yt-meta">
            <?php if($data): ?>
              <span class="meta-tag">📅 <?= $data ?></span>
            <?php endif; ?>
            <span class="meta-tag">▶️ YouTube</span>
          </div>

          <!-- Botão -->
          <?php if(!$logado): ?>
            <button class="btn-yt btn-yt-lock"
                    onclick="document.getElementById('modal-login').style.display='flex'">
              🔒 Login para Acessar
            </button>
          <?php elseif(!$inscrito): ?>
            <button class="btn-yt btn-yt-lock"
                    onclick="document.getElementById('modal-inscricao').style.display='flex'">
              🔒 Inscreva-se para Acessar
            </button>
          <?php else: ?>
            <a href="<?= htmlspecialchars($ytLink) ?>"
               target="_blank" rel="noopener"
               class="btn-yt btn-yt-livre">
              ▶️ Assistir no YouTube
            </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php elseif(isset($_GET['q']) && empty($erro_busca) && YOUTUBE_API_KEY !== 'SUA_CHAVE_AQUI'): ?>
  <div class="vazio">
    <div class="ic">🔍</div>
    <h3>Nenhum resultado para "<?= htmlspecialchars($_GET['q']) ?>"</h3>
    <p>Tente outro termo de busca</p>
  </div>

  <?php elseif(!isset($_GET['q'])): ?>
  <div class="vazio">
    <div class="ic">▶️</div>
    <h3>Digite algo para buscar</h3>
    <p>Ex: "funk 2026", "Wesley Safadão", "pagode ao vivo"</p>
  </div>
  <?php endif; ?>

  <!-- Info da API para aprendizado -->
  <div class="api-info" style="margin-top:20px">
    <strong>📚 Para aprendizado:</strong>
    Essa página usa a <strong>YouTube Data API v3</strong> — search.list.
    Cada busca consome <strong>100 unidades</strong> da cota diária gratuita de <strong>10.000 unidades</strong>.
    Isso permite ~100 buscas/dia. A API retorna apenas metadados (título, thumbnail, canal) — não fornece áudio ou download.
  </div>
</main>

<!-- MODAL LOGIN -->
<div id="modal-login" class="modal-overlay" style="display:none">
  <div class="modal-box">
    <span class="modal-icon">🔒</span>
    <h2>Faça Login para Acessar</h2>
    <p>Você precisa estar logado e inscrito no canal <strong><?= YOUTUBE_CHANNEL_HANDLE ?></strong>.</p>
    <div class="modal-btns">
      <a href="/login.php" class="btn btn-google">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="G"> Entrar com Google
      </a>
      <button class="btn btn-outline" onclick="document.getElementById('modal-login').style.display='none'">Fechar</button>
    </div>
  </div>
</div>

<!-- MODAL INSCRIÇÃO -->
<div id="modal-inscricao" class="modal-overlay" style="display:none">
  <div class="modal-box">
    <span class="modal-icon">▶️</span>
    <h2>Inscreva-se no Canal!</h2>
    <p>Inscreva-se no canal <strong><?= YOUTUBE_CHANNEL_HANDLE ?></strong> para acessar os links.</p>
    <div class="modal-btns">
      <a href="https://www.youtube.com/<?= YOUTUBE_CHANNEL_HANDLE ?>?sub_confirmation=1"
         target="_blank" rel="noopener" class="btn btn-vermelho">▶️ Inscrever no Canal</a>
      <button class="btn btn-outline" onclick="document.getElementById('modal-inscricao').style.display='none'">Fechar</button>
    </div>
  </div>
</div>

<div id="toast-container" class="toast-container"></div>
<script>
  // Hamburger menu toggle
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const navLinks = document.getElementById('navLinks');
  if (hamburgerBtn && navLinks) {
    hamburgerBtn.addEventListener('click', function() {
      this.classList.toggle('active');
      navLinks.classList.toggle('active');
    });
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
