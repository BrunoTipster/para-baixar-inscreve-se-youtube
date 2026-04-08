# Sistema de Download de Músicas do YouTube

Sistema web para baixar músicas do YouTube mediante inscrição no canal.

## 📋 Funcionalidades

- Download de músicas do YouTube
- Sistema de verificação de inscrição no canal
- Painel administrativo para gerenciar músicas
- Integração com API do YouTube
- Sistema de autenticação de usuários
- Atualização automática de capas

## 🚀 Instalação Rápida

```bash
# 1. Clone o repositório
git clone https://github.com/BrunoTipster/para-baixar-inscreve-se-youtube.git
cd para-baixar-inscreve-se-youtube

# 2. Configure o banco de dados
cp config/database.example.php config/database.php
# Edite config/database.php com suas credenciais

# 3. Configure YouTube API
cp config/youtube.example.php config/youtube.php
# Edite config/youtube.php com suas credenciais do Google Cloud

# 4. Crie o banco de dados
# Acesse: http://seudominio.com/criar-banco.php
```

📖 Para instruções detalhadas, consulte: [CONFIGURACAO.md](CONFIGURACAO.md)

## 📚 Documentação

- [CONFIGURACAO.md](CONFIGURACAO.md) - Guia completo de instalação e configuração
- [COMO-ADICIONAR-MUSICAS.md](COMO-ADICIONAR-MUSICAS.md) - Como adicionar músicas ao sistema
- [COMO-ADICIONAR-LINKS.md](COMO-ADICIONAR-LINKS.md) - Como adicionar links de músicas
- [ATUALIZAR-CAPAS.md](ATUALIZAR-CAPAS.md) - Como atualizar capas das músicas
- [SOLUCAO-CAPAS.md](SOLUCAO-CAPAS.md) - Soluções para problemas com capas
- [RESUMO-SISTEMA.md](RESUMO-SISTEMA.md) - Resumo geral do sistema
- [INSTALACAO.txt](INSTALACAO.txt) - Instruções básicas de instalação

## 🛠️ Tecnologias

- PHP
- MySQL
- JavaScript
- YouTube Data API v3
- Google OAuth 2.0

## 📁 Estrutura do Projeto

```
├── admin/              # Painel administrativo
├── api/                # Endpoints da API
├── assets/             # CSS e JavaScript
├── config/             # Arquivos de configuração
├── uploads/            # Músicas e capas
└── *.php               # Páginas principais
```

## ⚙️ Configuração

### Obter Credenciais do Google Cloud

1. Acesse [Google Cloud Console](https://console.cloud.google.com/)
2. Crie um novo projeto
3. Ative a YouTube Data API v3
4. Crie credenciais OAuth 2.0
5. Configure o arquivo `config/youtube.php` com suas credenciais

### Obter Channel ID do YouTube

Execute: `http://seudominio.com/obter-channel-id.php`

Ou manualmente:
1. Acesse seu canal no YouTube
2. Clique em "Personalizar canal"
3. Na URL, copie o ID após `/channel/`

📖 Guia completo: [CONFIGURACAO.md](CONFIGURACAO.md)

## 📝 Licença

Este projeto é de código aberto para fins educacionais.

## ⚠️ Aviso Legal

Este sistema foi desenvolvido para fins educacionais. Certifique-se de:
- Respeitar os termos de serviço do YouTube
- Ter direitos sobre o conteúdo distribuído
- Cumprir as leis de direitos autorais do seu país

## 👤 Autor

BrunoTipster - [GitHub](https://github.com/BrunoTipster)

## 🤝 Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para:
- Reportar bugs
- Sugerir novas funcionalidades
- Enviar pull requests

## ⭐ Apoie o Projeto

Se este projeto foi útil para você, considere:
- Dar uma estrela no GitHub
- Compartilhar com outros desenvolvedores
- Contribuir com melhorias
