/**
 * DJ Bruno Pendrives — JavaScript Principal
 * Funções: Verificação de inscrição, download, filtros, UI
 */

/* ===========================================================
 * VERIFICAÇÃO DE INSCRIÇÃO VIA AJAX
 * =========================================================== */

/**
 * Verifica se o usuário está inscrito no canal via API
 * @param {Function} callback - Chamado com resultado {inscrito: bool}
 */
async function verificarInscricao(callback) {
  try {
    const res = await fetch('/api/check-subscription.php', {
      method: 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });

    if (!res.ok) throw new Error('Erro HTTP: ' + res.status);

    const data = await res.json();
    if (typeof callback === 'function') callback(data);
    return data;
  } catch (err) {
    console.error('Erro ao verificar inscrição:', err);
    return { inscrito: false, erro: true };
  }
}

/* ===========================================================
 * DOWNLOAD DE MÚSICAS
 * =========================================================== */

/**
 * Inicia o download de uma música
 * @param {number} musicaId - ID da música
 * @param {HTMLElement} botao - Botão clicado
 */
async function baixarMusica(musicaId, botao) {
  // Se não está logado, abre modal de login
  if (!window.usuarioLogado) {
    abrirModalLogin();
    return;
  }

  // Mostra loading
  const textoOriginal = botao.innerHTML;
  botao.disabled = true;
  botao.innerHTML = '<span class="spinner"></span> Verificando...';

  try {
    // Verifica inscrição antes de liberar download
    const resultado = await verificarInscricao();

    if (resultado.inscrito) {
      // Usuário inscrito — inicia download
      botao.innerHTML = '<span class="spinner"></span> Baixando...';
      window.location.href = `/download.php?id=${musicaId}`;

      // Atualiza UI
      setTimeout(() => {
        botao.disabled = false;
        botao.innerHTML = textoOriginal;
        mostrarToast('Download iniciado! ✅', 'sucesso');
        // Atualiza contador
        atualizarContadorDownloads(musicaId);
      }, 2000);
    } else {
      // Não inscrito — mostra modal
      botao.disabled = false;
      botao.innerHTML = textoOriginal;
      abrirModalInscricao(musicaId);
    }
  } catch (err) {
    botao.disabled = false;
    botao.innerHTML = textoOriginal;
    mostrarToast('Erro inesperado. Tente novamente.', 'erro');
  }
}

/**
 * Atualiza o contador de downloads visível no card
 * @param {number} musicaId
 */
function atualizarContadorDownloads(musicaId) {
  const el = document.querySelector(`[data-downloads="${musicaId}"]`);
  if (el) {
    const atual = parseInt(el.textContent) || 0;
    el.textContent = atual + 1;
  }
}

/* ===========================================================
 * MODAIS
 * =========================================================== */

/**
 * Abre o modal de login obrigatório
 */
function abrirModalLogin() {
  const modal = document.getElementById('modal-login');
  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

/**
 * Fecha o modal de login
 */
function fecharModalLogin() {
  const modal = document.getElementById('modal-login');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}

/**
 * Abre o modal pedindo para se inscrever
 * @param {number} musicaId - ID da música para tentar após inscrição
 */
function abrirModalInscricao(musicaId) {
  const modal = document.getElementById('modal-inscricao');
  if (modal) {
    // Armazena o ID da música para depois
    modal.dataset.musicaId = musicaId;
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

/**
 * Fecha o modal de inscrição
 */
function fecharModalInscricao() {
  const modal = document.getElementById('modal-inscricao');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}

/**
 * Usuário clicou em "Já me inscrevi" — verifica novamente
 */
async function jaInscrevi() {
  const modal = document.getElementById('modal-inscricao');
  const musicaId = modal ? modal.dataset.musicaId : null;
  const btn = document.getElementById('btn-ja-inscrevi');

  if (btn) {
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Verificando...';

    try {
      const resultado = await verificarInscricao();

      if (resultado.inscrito) {
        // Atualiza estado global
        window.usuarioInscrito = true;

        // Atualiza todos os botões de download
        atualizarBotoesDownload(true);

        // Fecha modal
        fecharModalInscricao();
        mostrarToast('Inscrição confirmada! Todos os downloads estão liberados. ✅', 'sucesso');
      } else {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        mostrarToast('Inscrição não detectada ainda. Inscreva-se e tente novamente.', 'erro');
      }
    } catch (err) {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
      mostrarToast('Erro ao verificar. Tente novamente.', 'erro');
    }
  }
}

/* ===========================================================
 * ATUALIZAÇÃO DE BOTÕES DE DOWNLOAD
 * =========================================================== */

/**
 * Atualiza o estado de todos os botões de download
 * @param {boolean} inscrito
 */
function atualizarBotoesDownload(inscrito) {
  const botoes = document.querySelectorAll('.btn-download[data-musica-id]');
  botoes.forEach(btn => {
    const musicaId = btn.dataset.musicaId;
    if (inscrito) {
      btn.className = 'btn-download btn-liberado';
      btn.innerHTML = '✅ Baixar MP3';
      btn.onclick = () => baixarMusica(musicaId, btn);
    } else if (!window.usuarioLogado) {
      btn.className = 'btn-download btn-bloqueado';
      btn.innerHTML = '🔒 Faça Login';
      btn.onclick = abrirModalLogin;
    } else {
      btn.className = 'btn-download btn-bloqueado';
      btn.innerHTML = '🔒 Inscreva-se';
      btn.onclick = () => abrirModalInscricao(musicaId);
    }
  });

  // Atualiza badge de status
  atualizarStatusInscricao(inscrito);
}

/**
 * Atualiza badge de status de inscrição no header
 * @param {boolean} inscrito
 */
function atualizarStatusInscricao(inscrito) {
  const badge = document.getElementById('status-inscricao');
  if (badge) {
    if (inscrito) {
      badge.className = 'status-inscricao status-inscrito';
      badge.innerHTML = '✅ Canal assinado';
    } else {
      badge.className = 'status-inscricao status-nao-inscrito';
      badge.innerHTML = '🔒 Não inscrito';
    }
  }

  // Mostra/oculta seção de aviso de inscrição
  const secao = document.getElementById('aviso-inscricao');
  if (secao) {
    secao.style.display = inscrito ? 'none' : 'block';
  }
}

/* ===========================================================
 * FILTROS E BUSCA
 * =========================================================== */

/**
 * Filtra cards de músicas por gênero
 * @param {string} genero - Gênero para filtrar, ou 'todos'
 */
function filtrarPorGenero(genero) {
  const cards = document.querySelectorAll('.card-musica');
  const botoesFiltro = document.querySelectorAll('.filtro-btn');

  // Atualiza botão ativo
  botoesFiltro.forEach(btn => {
    btn.classList.toggle('ativo', btn.dataset.genero === genero);
  });

  // Filtra os cards
  cards.forEach(card => {
    const cardGenero = card.dataset.genero || '';
    const mostrar = genero === 'todos' || cardGenero.toLowerCase() === genero.toLowerCase();
    card.style.display = mostrar ? '' : 'none';
    if (mostrar) {
      card.style.animation = 'none';
      card.offsetHeight; // reflow
      card.style.animation = 'fadeIn 0.4s ease';
    }
  });
}

/**
 * Busca músicas por título ou artista
 * @param {string} termo - Termo de busca
 */
function buscarMusicas(termo) {
  const cards = document.querySelectorAll('.card-musica');
  const termoLower = termo.toLowerCase().trim();

  cards.forEach(card => {
    const titulo = (card.dataset.titulo || '').toLowerCase();
    const artista = (card.dataset.artista || '').toLowerCase();
    const mostrar = !termoLower || titulo.includes(termoLower) || artista.includes(termoLower);
    card.style.display = mostrar ? '' : 'none';
  });
}

/* ===========================================================
 * SISTEMA DE TOASTS / NOTIFICAÇÕES
 * =========================================================== */

/**
 * Exibe um toast de notificação
 * @param {string} mensagem
 * @param {string} tipo - 'sucesso' | 'erro' | 'info'
 * @param {number} duracao - em milissegundos
 */
function mostrarToast(mensagem, tipo = 'info', duracao = 4000) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const icones = { sucesso: '✅', erro: '❌', info: 'ℹ️' };
  const classes = { sucesso: 'toast-sucesso', erro: 'toast-erro', info: 'toast-info' };

  const toast = document.createElement('div');
  toast.className = `toast ${classes[tipo] || 'toast-info'}`;
  toast.innerHTML = `<span>${icones[tipo] || 'ℹ️'}</span> ${mensagem}`;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.transition = 'all 0.3s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(40px)';
    setTimeout(() => toast.remove(), 350);
  }, duracao);
}

/* ===========================================================
 * FECHAR MODAIS NO CLIQUE FORA
 * =========================================================== */
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    fecharModalLogin();
    fecharModalInscricao();
  }
});

/* ===========================================================
 * INICIALIZAÇÃO
 * =========================================================== */
document.addEventListener('DOMContentLoaded', () => {
  // Campo de busca
  const campoBusca = document.getElementById('busca-musica');
  if (campoBusca) {
    campoBusca.addEventListener('input', (e) => buscarMusicas(e.target.value));
  }

  // Inicializa filtro de gênero
  const primeiroBotao = document.querySelector('.filtro-btn');
  if (primeiroBotao) primeiroBotao.classList.add('ativo');

  // Se usuário está logado mas não inscrito — verifica ao carregar
  if (window.usuarioLogado && !window.usuarioInscrito) {
    verificarInscricao(data => {
      if (data.inscrito && !window.usuarioInscrito) {
        window.usuarioInscrito = true;
        atualizarBotoesDownload(true);
        // Salva no servidor
        fetch('/api/update-subscription.php', {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).catch(() => {});
      }
    });
  }
});
