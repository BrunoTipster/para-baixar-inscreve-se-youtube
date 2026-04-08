# 🔧 Guia de Configuração

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Conta Google Cloud Console
- Canal no YouTube

---

## 🚀 Instalação Passo a Passo

### 1. Clonar o Repositório

```bash
git clone https://github.com/BrunoTipster/para-baixar-inscreve-se-youtube.git
cd para-baixar-inscreve-se-youtube
```

### 2. Configurar Banco de Dados

Crie o arquivo `config/database.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dj_bruno_pendrives');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

### 3. Criar Banco de Dados

Acesse no navegador:
```
http://seudominio.com/criar-banco.php
```

Ou importe manualmente:
```bash
mysql -u root -p < database.sql
```

### 4. Configurar YouTube API

#### 4.1. Criar Projeto no Google Cloud Console

1. Acesse: https://console.cloud.google.com/
2. Crie um novo projeto
3. Ative a YouTube Data API v3
4. Crie credenciais OAuth 2.0

#### 4.2. Configurar OAuth

1. Vá em "Credenciais" > "Criar credenciais" > "ID do cliente OAuth"
2. Tipo: Aplicativo da Web
3. Adicione URI de redirecionamento:
   ```
   http://seudominio.com/callback.php
   ```
4. Copie o Client ID e Client Secret

#### 4.3. Obter Channel ID

Acesse:
```
http://seudominio.com/obter-channel-id.php
```

Ou manualmente:
1. Acesse seu canal no YouTube
2. Clique em "Personalizar canal"
3. Na URL, copie o ID após `/channel/`

#### 4.4. Configurar Credenciais

Copie o arquivo de exemplo:
```bash
cp config/youtube.example.php config/youtube.php
```

Edite `config/youtube.php` e preencha:

```php
define('GOOGLE_CLIENT_ID',     'SEU_CLIENT_ID_AQUI');
define('GOOGLE_CLIENT_SECRET', 'SEU_CLIENT_SECRET_AQUI');
define('GOOGLE_REDIRECT_URI',  'http://seudominio.com/callback.php');

define('YOUTUBE_CHANNEL_ID',     'SEU_CHANNEL_ID_AQUI');
define('YOUTUBE_CHANNEL_HANDLE', '@SeuCanal');
define('YOUTUBE_CHANNEL_URL',    'https://www.youtube.com/@SeuCanal?sub_confirmation=1');
```

### 5. Configurar Permissões

```bash
chmod 755 uploads/musicas
chmod 755 uploads/capas
```

### 6. Adicionar Músicas

Acesse:
```
http://seudominio.com/adicionar-musicas.php
```

Ou use o painel admin:
```
http://seudominio.com/admin/
```

Credenciais padrão:
- Usuário: `admin`
- Senha: `admin123`

⚠️ **IMPORTANTE**: Altere a senha padrão em produção!

---

## 🔐 Segurança

### Alterar Senha do Admin

1. Acesse: `http://seudominio.com/admin/usuarios.php`
2. Edite o usuário admin
3. Defina uma senha forte

### Configurar HTTPS

Para produção, sempre use HTTPS:

1. Obtenha certificado SSL (Let's Encrypt gratuito)
2. Configure no servidor web
3. Atualize as URLs em `config/youtube.php`
4. Atualize redirect URI no Google Cloud Console

### Proteger Arquivos Sensíveis

O `.htaccess` já protege:
- `config/database.php`
- `config/youtube.php`
- Arquivos `.json`

---

## 📝 Verificação

### Verificar Banco de Dados

```
http://seudominio.com/verificar-banco.php
```

### Testar OAuth

1. Acesse a página principal
2. Clique em "Login com Google"
3. Autorize o aplicativo
4. Verifique se o login funciona

### Testar Verificação de Inscrição

1. Faça login
2. Se não estiver inscrito, aparecerá aviso
3. Inscreva-se no canal
4. Clique em "Já me Inscrevi"
5. Deve liberar o acesso

---

## 🎵 Adicionar Músicas

### Via Painel Admin

1. Acesse: `http://seudominio.com/admin/musicas.php`
2. Clique em "Adicionar Nova Música"
3. Preencha os campos:
   - Título
   - Artista
   - Gênero
   - Link de download (Google Drive, MediaFire, etc.)
   - URL da capa (opcional)

### Via Script

Edite `adicionar-musicas.php` e adicione:

```php
$musicas[] = [
    'titulo' => 'Nome da Música',
    'artista' => 'Nome do Artista',
    'genero' => 'Gênero',
    'link_download' => 'https://drive.google.com/...',
    'capa_url' => 'https://i.scdn.co/image/...'
];
```

Execute:
```
http://seudominio.com/adicionar-musicas.php
```

---

## 🌐 Produção

### Checklist para Deploy

- [ ] Alterar credenciais do banco
- [ ] Configurar HTTPS
- [ ] Atualizar redirect URIs no Google Cloud
- [ ] Alterar senha do admin
- [ ] Configurar domínio real
- [ ] Testar OAuth em produção
- [ ] Testar verificação de inscrição
- [ ] Adicionar músicas
- [ ] Configurar backup do banco

### Variáveis de Ambiente (Opcional)

Para maior segurança, use variáveis de ambiente:

```php
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET'));
```

---

## 🐛 Problemas Comuns

### Erro de Conexão com Banco

- Verifique se MySQL está rodando
- Confirme credenciais em `config/database.php`
- Execute `criar-banco.php`

### OAuth Não Funciona

- Verifique credenciais em `config/youtube.php`
- Confirme redirect URI no Google Cloud Console
- Certifique-se que YouTube Data API está ativa

### Músicas Não Aparecem

- Execute `adicionar-musicas.php`
- Verifique com `verificar-banco.php`
- Confira painel admin

### Download Não Funciona

- Verifique se está logado
- Confirme se está inscrito no canal
- Teste o link externo diretamente

---

## 📞 Suporte

Para dúvidas e problemas:
- Abra uma issue no GitHub
- Consulte a documentação completa
- Verifique os arquivos `.md` do projeto

---

## 📚 Documentação Adicional

- `INSTALACAO.txt` - Instruções básicas
- `COMO-ADICIONAR-MUSICAS.md` - Como adicionar músicas
- `COMO-ADICIONAR-LINKS.md` - Como adicionar links
- `ATUALIZAR-CAPAS.md` - Como atualizar capas
- `SOLUCAO-CAPAS.md` - Soluções para problemas com capas
- `RESUMO-SISTEMA.md` - Resumo geral do sistema

---

**Boa sorte com seu projeto! 🎉**
