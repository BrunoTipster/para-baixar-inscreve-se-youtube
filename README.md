# Sistema de Download de Músicas do YouTube

Sistema web para baixar músicas do YouTube mediante inscrição no canal.

## 📋 Funcionalidades

- Download de músicas do YouTube
- Sistema de verificação de inscrição no canal
- Painel administrativo para gerenciar músicas
- Integração com API do YouTube
- Sistema de autenticação de usuários
- Atualização automática de capas

## 🚀 Instalação

1. Clone o repositório
2. Configure o banco de dados em `config/database.php`
3. Importe o arquivo `database.sql`
4. Configure as credenciais do YouTube em `config/youtube.php`
5. Ajuste as permissões das pastas `uploads/musicas` e `uploads/capas`

Para instruções detalhadas, consulte o arquivo `INSTALACAO.txt`

## 📚 Documentação

- `COMO-ADICIONAR-MUSICAS.md` - Como adicionar músicas ao sistema
- `COMO-ADICIONAR-LINKS.md` - Como adicionar links de músicas
- `ATUALIZAR-CAPAS.md` - Como atualizar capas das músicas
- `SOLUCAO-CAPAS.md` - Soluções para problemas com capas
- `RESUMO-SISTEMA.md` - Resumo geral do sistema

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

1. Obtenha credenciais da API do YouTube no Google Cloud Console
2. Configure o arquivo `config/youtube.php` com suas credenciais
3. Configure o banco de dados em `config/database.php`

## 📝 Licença

Este projeto é de código aberto.

## 👤 Autor

BrunoTipster
