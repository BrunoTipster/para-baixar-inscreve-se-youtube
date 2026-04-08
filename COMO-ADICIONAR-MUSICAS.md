# 🎵 Como Adicionar Músicas ao Sistema

## Método 1: Script Automático (Recomendado)

1. Acesse no navegador:
   ```
   http://localhost/youtube/adicionar-musicas.php
   ```

2. O script vai:
   - Remover músicas de exemplo
   - Adicionar todas as músicas com links externos
   - Buscar capas automaticamente da internet
   - Mostrar resultado na tela

3. Pronto! As músicas estarão disponíveis no site.

---

## Método 2: SQL Manual

1. Abra o phpMyAdmin: `http://localhost/phpmyadmin`

2. Selecione o banco `dj_bruno_pendrives`

3. Vá na aba "SQL"

4. Cole o conteúdo do arquivo `adicionar-musicas.sql`

5. Clique em "Executar"

---

## Método 3: Painel Admin

1. Acesse: `http://localhost/youtube/admin/`

2. Login:
   - Usuário: `admin`
   - Senha: `admin123`

3. Vá em "Músicas" no menu lateral

4. Clique em "Adicionar Nova Música"

5. Preencha os campos:
   - **Título**: Nome da música/pacote
   - **Artista**: Nome do artista
   - **Gênero**: Arrocha, Axé, Diversos, etc.
   - **Descrição**: Descrição opcional
   - **URL da Capa**: Link da imagem (ex: https://i.scdn.co/image/...)
   - **Arquivo**: Cole o link externo (Google Drive, MediaFire, Gofile)
   - **Duração**: Deixe vazio para links externos

6. Clique em "Salvar"

---

## 📋 Links das Músicas

### Pacote Principal (16GB)
```
https://www.mediafire.com/file/zlqoq7k06ovgpef/16gb_atualizacao_abril_2026_@kelcds.rar/file
```

### Gofile
```
https://gofile.io/d/kvQ5rL
```

### Google Drive Principal
```
https://drive.google.com/file/d/1ZQtu1QvMFTEdSD4Pl7xNLUdibD8sc3Mq/view
```

### Arrocha
```
https://drive.google.com/file/d/1ezDChRpJdvBO6tPbGtDt4BLMNwGZh2vs/view?usp=drive_link
```

### Axé
```
https://drive.google.com/file/d/1p_Z3waJEWsr_Fc5rnB0hpTcTNJ1kJjo-/view?usp=drive_link
```

### FlashBack
```
https://drive.google.com/file/d/1p_Z3waJEWsr_Fc5rnB0hpTcTNJ1kJjo-/view?usp=drive_link
```

### Forró
```
https://drive.google.com/file/d/1Hc0jQcr8DzAHTjYYLEAZjsv75BI37XZd/view?usp=drive_link
```

### Vaquejada
```
https://drive.google.com/file/d/1Hc0jQcr8DzAHTjYYLEAZjsv75BI37XZd/view?usp=drive_link
```

### Swingueira
```
https://drive.google.com/file/d/14MQmkuJECBUtEAaTaVlpKOGHdMqnu72S/view?usp=drive_link
```

### Sertanejo
```
https://drive.google.com/file/d/1uiuJdVnxw9u3Q1iqDzorMV_FAUpf3oQf/view?usp=drive_link
```

### Reggae
```
https://drive.google.com/file/d/1fgkPylAx7Lo8dsU2BHIdZ7MInv9OcrEe/view?usp=drive_link
```

### Diversas
```
https://drive.google.com/drive/folders/1xzPJROO_bkxUZNlqajNKopc_CaLFwmte
```

---

## 🖼️ Capas de Álbuns

O sistema busca capas automaticamente do Spotify. Você também pode usar:

- **Imgur**: https://imgur.com/upload
- **Spotify**: Busque o álbum e copie a URL da capa
- **Google Images**: Busque "album cover [nome]" e copie o link da imagem

### Exemplo de URLs de Capas:
```
Arrocha: https://i.scdn.co/image/ab67616d0000b273f7db43292a6a99b21b51d5b4
Axé: https://i.scdn.co/image/ab67616d0000b273e8b066f70c206551210d902b
Diversos: https://i.scdn.co/image/ab67616d0000b273a048415db06a5b6fa7ec4e1a
```

---

## ✅ Verificar se Funcionou

1. Acesse: `http://localhost/youtube/`

2. Você deve ver os cards das músicas com:
   - Capa do álbum
   - Título e artista
   - Badge "🔗 Link Externo"
   - Botão de download

3. Faça login e teste o download - deve redirecionar para o link externo

---

## 🔧 Solução de Problemas

### Músicas não aparecem?
- Verifique se o campo `ativo` está como `1` no banco
- Limpe o cache do navegador (Ctrl+Shift+R)

### Capas não carregam?
- Verifique se a URL da capa é válida
- Teste a URL diretamente no navegador
- Use URLs HTTPS (não HTTP)

### Download não funciona?
- Verifique se você está logado
- Verifique se está inscrito no canal
- Teste o link externo diretamente

---

## 📝 Notas

- Links externos são redirecionados automaticamente
- O sistema registra os downloads mesmo sendo links externos
- Capas podem ser URLs externas (não precisa fazer upload)
- Você pode misturar arquivos locais e links externos
