<?php
/**
 * admin/musicas.php — CRUD de Músicas
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
$mensagem = '';
$tipoMensagem = '';

// ============================================================
// PROCESSAR FORMULÁRIOS (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = filter_input(INPUT_POST, 'acao', FILTER_SANITIZE_SPECIAL_CHARS);

    // ---- ADICIONAR / EDITAR MÚSICA ----
    if (in_array($acao, ['adicionar', 'editar'])) {
        $id      = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $titulo  = trim(htmlspecialchars(strip_tags($_POST['titulo'] ?? ''), ENT_QUOTES, 'UTF-8'));
        $artista = trim(htmlspecialchars(strip_tags($_POST['artista'] ?? ''), ENT_QUOTES, 'UTF-8'));
        $genero  = trim(htmlspecialchars(strip_tags($_POST['genero'] ?? 'Eletrônica'), ENT_QUOTES, 'UTF-8'));
        $duracao = trim(htmlspecialchars(strip_tags($_POST['duracao'] ?? ''), ENT_QUOTES, 'UTF-8'));
        $ativo   = isset($_POST['ativo']) ? 1 : 0;

        if (empty($titulo) || empty($artista)) {
            $mensagem    = 'Título e artista são obrigatórios.';
            $tipoMensagem = 'erro';
        } else {
            // Upload de capa (imagem) OU URL externa
            $caminhoCapaAtual = $_POST['capa_atual'] ?? '';
            $caminhoCapa = $caminhoCapaAtual;
            $capaUrl = trim($_POST['capa_url'] ?? '');

            // Prioriza URL externa se fornecida
            if (!empty($capaUrl)) {
                if (filter_var($capaUrl, FILTER_VALIDATE_URL)) {
                    $caminhoCapa = $capaUrl;
                } else {
                    $mensagem = 'URL da capa inválida.';
                    $tipoMensagem = 'erro';
                }
            }
            // Se não tem URL, tenta upload de arquivo
            elseif (!empty($_FILES['capa']['name'])) {
                $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower(pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $extPermitidas)) {
                    $mensagem = 'Formato de imagem inválido. Use JPG, PNG ou WebP.';
                    $tipoMensagem = 'erro';
                } elseif ($_FILES['capa']['size'] > 5 * 1024 * 1024) {
                    $mensagem = 'Imagem muito grande. Máximo 5MB.';
                    $tipoMensagem = 'erro';
                } else {
                    $nomeCapa = 'capa_' . uniqid() . '.' . $ext;
                    $destino = __DIR__ . '/../uploads/capas/' . $nomeCapa;

                    if (!is_dir(dirname($destino))) {
                        mkdir(dirname($destino), 0755, true);
                    }

                    if (move_uploaded_file($_FILES['capa']['tmp_name'], $destino)) {
                        $caminhoCapa = 'uploads/capas/' . $nomeCapa;
                    }
                }
            }

            // Upload do arquivo de música OU link externo
            $caminhoArquivoAtual = $_POST['arquivo_atual'] ?? '';
            $caminhoArquivo = $caminhoArquivoAtual;
            $linkExterno = trim($_POST['link_externo'] ?? '');

            // Prioriza link externo se fornecido
            if (!empty($linkExterno)) {
                // Valida se é uma URL válida
                if (filter_var($linkExterno, FILTER_VALIDATE_URL)) {
                    $caminhoArquivo = $linkExterno;
                } else {
                    $mensagem = 'Link externo inválido. Use uma URL completa (https://...).';
                    $tipoMensagem = 'erro';
                }
            }
            // Se não tem link externo, tenta upload de arquivo
            elseif (!empty($_FILES['arquivo']['name'])) {
                $extPermArquivo = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
                $extArq = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));

                if (!in_array($extArq, $extPermArquivo)) {
                    $mensagem = 'Formato de áudio inválido. Use MP3, WAV, OGG, M4A ou FLAC.';
                    $tipoMensagem = 'erro';
                } else {
                    $nomeArq = 'musica_' . uniqid() . '.' . $extArq;
                    $destArq = __DIR__ . '/../uploads/musicas/' . $nomeArq;

                    if (!is_dir(dirname($destArq))) {
                        mkdir(dirname($destArq), 0755, true);
                    }

                    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $destArq)) {
                        $caminhoArquivo = 'uploads/musicas/' . $nomeArq;
                    }
                }
            }

            if (empty($mensagem)) {
                if ($acao === 'adicionar') {
                    if (empty($caminhoArquivo) && empty($linkExterno)) {
                        $mensagem = 'Arquivo de música ou link externo é obrigatório ao adicionar.';
                        $tipoMensagem = 'erro';
                    } else {
                        $stmt = $db->prepare("
                            INSERT INTO musicas (titulo, artista, genero, capa, arquivo, duracao, ativo)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([$titulo, $artista, $genero, $caminhoCapa, $caminhoArquivo, $duracao, $ativo]);
                        $mensagem = 'Música "' . $titulo . '" adicionada com sucesso!';
                        $tipoMensagem = 'sucesso';
                    }
                } elseif ($acao === 'editar' && $id) {
                    $stmt = $db->prepare("
                        UPDATE musicas
                        SET titulo=?, artista=?, genero=?, capa=?, arquivo=?, duracao=?, ativo=?
                        WHERE id=?
                    ");
                    $stmt->execute([$titulo, $artista, $genero, $caminhoCapa, $caminhoArquivo, $duracao, $ativo, $id]);
                    $mensagem = 'Música atualizada com sucesso!';
                    $tipoMensagem = 'sucesso';
                }
            }
        }
    }

    // ---- DELETAR MÚSICA ----
    elseif ($acao === 'deletar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            // Busca para deletar arquivos físicos
            $stmt = $db->prepare("SELECT capa, arquivo FROM musicas WHERE id = ?");
            $stmt->execute([$id]);
            $m = $stmt->fetch();

            // Remove arquivos fisicos (opcional)
            if ($m) {
                @unlink(__DIR__ . '/../' . $m['arquivo']);
                @unlink(__DIR__ . '/../' . $m['capa']);
            }

            $db->prepare("DELETE FROM musicas WHERE id = ?")->execute([$id]);
            $mensagem = 'Música removida com sucesso.';
            $tipoMensagem = 'sucesso';
        }
    }

    // ---- TOGGLE ATIVO/INATIVO ----
    elseif ($acao === 'toggle') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $db->prepare("UPDATE musicas SET ativo = NOT ativo WHERE id = ?")
               ->execute([$id]);
            $mensagem = 'Status alterado.';
            $tipoMensagem = 'sucesso';
        }
    }
}

// ============================================================
// BUSCA TODAS AS MÚSICAS PARA EXIBIÇÃO
// ============================================================
$musicas = $db->query("SELECT * FROM musicas ORDER BY data DESC")->fetchAll();

// Música para editar (se ?editar=id na URL)
$editar = null;
$editarId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
if ($editarId) {
    $stmt = $db->prepare("SELECT * FROM musicas WHERE id = ?");
    $stmt->execute([$editarId]);
    $editar = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Músicas — Admin DJ Bruno Pendrives</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<div class="admin-layout">
  <div class="admin-sidebar-overlay" id="adminOverlay"></div>
  <!-- Sidebar -->
  <?php include __DIR__ . '/partials/sidebar.php'; ?>

  <!-- Conteúdo Principal -->
  <div class="admin-main">
    <div class="admin-topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button class="admin-mobile-toggle" id="adminToggle" aria-label="Menu">
          <span class="toggle-icon"><span></span><span></span><span></span></span>
        </button>
        <h2>🎵 Gerenciar Músicas</h2>
      </div>
      <a href="/admin/logout.php" class="btn btn-outline btn-sm">🚪 Sair</a>
    </div>

    <div class="admin-content">

      <!-- Mensagem de feedback -->
      <?php if ($mensagem): ?>
      <div class="toast-inline <?= $tipoMensagem === 'sucesso' ? 'toast-sucesso' : 'toast-erro' ?>"
           style="margin-bottom:24px;padding:14px 20px;border-radius:10px;display:flex;align-items:center;gap:10px;">
        <?= $tipoMensagem === 'sucesso' ? '✅' : '❌' ?>
        <?= htmlspecialchars($mensagem) ?>
      </div>
      <?php endif; ?>

      <!-- ============= FORMULÁRIO ADICIONAR / EDITAR ============= -->
      <div class="form-card" style="margin-bottom:32px;">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:24px;">
          <?= $editar ? '✏️ Editar Música' : '➕ Adicionar Nova Música' ?>
        </h3>

        <form method="POST" enctype="multipart/form-data" id="form-musica">
          <input type="hidden" name="acao" value="<?= $editar ? 'editar' : 'adicionar' ?>">
          <?php if ($editar): ?>
            <input type="hidden" name="id" value="<?= $editar['id'] ?>">
            <input type="hidden" name="capa_atual" value="<?= htmlspecialchars($editar['capa'] ?? '') ?>">
            <input type="hidden" name="arquivo_atual" value="<?= htmlspecialchars($editar['arquivo'] ?? '') ?>">
          <?php endif; ?>

          <div class="form-grid">
            <!-- Título -->
            <div class="form-group">
              <label for="titulo">Título da Música *</label>
              <input type="text" id="titulo" name="titulo" class="form-control"
                     placeholder="Ex: Summer Vibes 2024"
                     value="<?= htmlspecialchars($editar['titulo'] ?? '') ?>" required>
            </div>

            <!-- Artista -->
            <div class="form-group">
              <label for="artista">Artista / DJ *</label>
              <input type="text" id="artista" name="artista" class="form-control"
                     placeholder="Ex: DJ Bruno"
                     value="<?= htmlspecialchars($editar['artista'] ?? 'DJ Bruno') ?>" required>
            </div>

            <!-- Gênero -->
            <div class="form-group">
              <label for="genero">Gênero</label>
              <select id="genero" name="genero" class="form-control">
                <?php
                $generos = ['Arrocha', 'Axé', 'FlashBack', 'Forró', 'Vaquejada', 'Swingueira', 'Sertanejo', 'Reggae', 'Diversos', 'House', 'Techno', 'Trance', 'Dubstep', 'Deep House', 'Hip-Hop', 'Trap', 'Eletrônica', 'Funk', 'Pagode'];
                foreach ($generos as $g):
                  $sel = ($editar['genero'] ?? 'Diversos') === $g ? 'selected' : '';
                ?>
                  <option value="<?= $g ?>" <?= $sel ?>><?= $g ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Duração -->
            <div class="form-group">
              <label for="duracao">Duração (MM:SS)</label>
              <input type="text" id="duracao" name="duracao" class="form-control"
                     placeholder="Ex: 5:30"
                     value="<?= htmlspecialchars($editar['duracao'] ?? '') ?>">
            </div>

            <!-- Capa - URL Externa -->
            <div class="form-group">
              <label for="capa_url">🖼️ URL da Capa (Imagem Externa)</label>
              <input type="url" id="capa_url" name="capa_url" class="form-control"
                     placeholder="https://images.unsplash.com/..."
                     value="<?= !empty($editar['capa']) && (strpos($editar['capa'], 'http') === 0) ? htmlspecialchars($editar['capa']) : '' ?>">
              <small style="color:var(--cor-texto-dim);display:block;margin-top:6px;">
                💡 Cole a URL de uma imagem (Unsplash, Imgur, etc)
              </small>
            </div>

            <!-- OU Upload de Capa -->
            <div class="form-group">
              <label for="capa">📁 OU Upload de Capa (JPG/PNG/WebP, máx 5MB)</label>
              <?php if (!empty($editar['capa']) && strpos($editar['capa'], 'http') !== 0 && file_exists(__DIR__ . '/../' . $editar['capa'])): ?>
                <img src="/<?= htmlspecialchars($editar['capa']) ?>"
                     style="width:60px;height:60px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
              <?php endif; ?>
              <input type="file" id="capa" name="capa" class="form-control" accept="image/jpeg,image/png,image/webp">
            </div>

            <!-- Arquivo de Música -->
            <div class="form-group" style="grid-column: 1 / -1;">
              <label for="link_externo">🔗 Link Externo (Google Drive, MediaFire, Gofile, etc)</label>
              <input type="url" id="link_externo" name="link_externo" class="form-control"
                     placeholder="https://drive.google.com/file/d/... ou https://www.mediafire.com/..."
                     value="<?= !empty($editar['arquivo']) && (strpos($editar['arquivo'], 'http') === 0) ? htmlspecialchars($editar['arquivo']) : '' ?>">
              <small style="color:var(--cor-texto-dim);display:block;margin-top:6px;">
                💡 Cole o link completo do Google Drive, MediaFire, Gofile ou outro serviço. Se usar link externo, não precisa fazer upload de arquivo.
              </small>
            </div>

            <!-- OU Upload de Arquivo Local -->
            <div class="form-group" style="grid-column: 1 / -1;">
              <label for="arquivo">📁 OU Fazer Upload de Arquivo Local (MP3/WAV/etc)</label>
              <?php if (!empty($editar['arquivo']) && strpos($editar['arquivo'], 'http') !== 0): ?>
                <small style="color:var(--cor-texto-dim);display:block;margin-bottom:6px;">
                  Atual: <?= basename(htmlspecialchars($editar['arquivo'])) ?>
                </small>
              <?php endif; ?>
              <input type="file" id="arquivo" name="arquivo" class="form-control"
                     accept="audio/mpeg,audio/wav,audio/ogg,audio/x-m4a">
              <small style="color:var(--cor-texto-dim);display:block;margin-top:6px;">
                💡 Use upload de arquivo apenas para músicas pequenas. Para pacotes grandes, use link externo acima.
              </small>
            </div>

            <!-- Ativo -->
            <div class="form-group" style="justify-content:flex-end;flex-direction:row;align-items:center;gap:10px;">
              <input type="checkbox" id="ativo" name="ativo" <?= !$editar || $editar['ativo'] ? 'checked' : '' ?>
                     style="width:18px;height:18px;accent-color:var(--cor-primaria);">
              <label for="ativo" style="text-transform:none;font-size:0.9rem;cursor:pointer;">Ativo (visível no site)</label>
            </div>
          </div>

          <div style="display:flex;gap:12px;">
            <button type="submit" class="btn btn-primario" id="btn-salvar-musica">
              <?= $editar ? '💾 Salvar Alterações' : '➕ Adicionar Música' ?>
            </button>
            <?php if ($editar): ?>
              <a href="/admin/musicas.php" class="btn btn-outline">✕ Cancelar</a>
            <?php endif; ?>
          </div>
        </form>
      </div>

      <!-- ============= LISTAGEM DE MÚSICAS ============= -->
      <div class="admin-table-wrap">
        <div class="admin-table-header">
          <h3>📋 Músicas Cadastradas (<?= count($musicas) ?>)</h3>
        </div>

        <?php if (empty($musicas)): ?>
          <p style="padding:40px;text-align:center;color:var(--cor-texto-dim);">Nenhuma música cadastrada ainda.</p>
        <?php else: ?>
        <div style="overflow-x:auto;">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Capa</th>
                <th>Título</th>
                <th>Artista</th>
                <th>Gênero</th>
                <th>Downloads</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($musicas as $m): ?>
              <tr>
                <td>
                  <?php if (!empty($m['capa']) && file_exists(__DIR__ . '/../' . $m['capa'])): ?>
                    <img src="/<?= htmlspecialchars($m['capa']) ?>"
                         style="width:44px;height:44px;object-fit:cover;border-radius:8px;">
                  <?php else: ?>
                    <div style="width:44px;height:44px;background:rgba(155,48,255,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">🎵</div>
                  <?php endif; ?>
                </td>
                <td style="font-weight:600;"><?= htmlspecialchars($m['titulo']) ?></td>
                <td style="color:var(--cor-texto-dim);"><?= htmlspecialchars($m['artista']) ?></td>
                <td><span class="badge badge-roxo"><?= htmlspecialchars($m['genero']) ?></span></td>
                <td><span class="badge badge-verde"><?= number_format($m['downloads']) ?></span></td>
                <td>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="acao" value="toggle">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="submit" class="badge <?= $m['ativo'] ? 'badge-verde' : 'badge-vermelho' ?>"
                            style="border:none;cursor:pointer;padding:5px 12px;">
                      <?= $m['ativo'] ? 'Ativo' : 'Inativo' ?>
                    </button>
                  </form>
                </td>
                <td>
                  <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="/admin/musicas.php?editar=<?= $m['id'] ?>"
                       class="btn btn-outline btn-sm">✏️ Editar</a>

                    <form method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta música?');">
                      <input type="hidden" name="acao" value="deletar">
                      <input type="hidden" name="id" value="<?= $m['id'] ?>">
                      <button type="submit" class="btn btn-vermelho btn-sm" id="btn-del-<?= $m['id'] ?>">
                        🗑️ Remover
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
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
