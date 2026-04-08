# 🎨 Como Atualizar as Capas das Músicas

## Método 1: Script Automático (Recomendado)

### Passo 1: Acesse o script
```
http://localhost/youtube/atualizar-capas.php
```

### Passo 2: Aguarde a atualização
O script vai:
- Gerar capas SVG personalizadas para cada gênero
- Atualizar todas as músicas no banco
- Mostrar preview de todas as capas

### Passo 3: Verifique o resultado
```
http://localhost/youtube/
```

---

## Capas Personalizadas por Gênero

Cada gênero tem sua própria cor e ícone:

| Gênero | Cor | Ícone |
|--------|-----|-------|
| Arrocha | Vermelho Escuro | 💔 |
| Axé | Laranja | 🎉 |
| FlashBack | Roxo | 📻 |
| Forró | Laranja Claro | 🪗 |
| Vaquejada | Marrom | 🤠 |
| Swingueira | Rosa | 💃 |
| Sertanejo | Verde | 🎸 |
| Reggae | Amarelo/Verde | 🌴 |
| Diversos | Roxo Neon | 🎵 |

---

## Método 2: Capas Personalizadas (URLs Externas)

Se preferir usar imagens reais, edite o arquivo `adicionar-musicas.php`:

```php
$capas = [
    'Arrocha' => 'https://sua-url-da-imagem.jpg',
    'Axé' => 'https://sua-url-da-imagem.jpg',
    // ...
];
```

Depois execute:
```
http://localhost/youtube/adicionar-musicas.php
```

---

## Método 3: Via Painel Admin

1. Acesse: `http://localhost/youtube/admin/`
2. Login: admin / admin123
3. Vá em "Músicas"
4. Clique em "Editar" na música
5. Cole a URL da capa no campo "URL da Capa"
6. Salve

---

## Fontes de Imagens Gratuitas

### Unsplash (Fotos de Alta Qualidade)
```
https://unsplash.com/s/photos/music
```

### Pexels (Fotos Gratuitas)
```
https://www.pexels.com/search/music/
```

### Pixabay (Imagens Livres)
```
https://pixabay.com/images/search/music/
```

### Como usar:
1. Busque a imagem
2. Clique com botão direito
3. "Copiar endereço da imagem"
4. Cole no campo de capa

---

## Formato das Capas

### Recomendado:
- Tamanho: 400x400px ou maior
- Formato: JPG, PNG ou SVG
- Proporção: Quadrada (1:1)

### URLs Aceitas:
- ✅ `https://images.unsplash.com/...`
- ✅ `https://i.imgur.com/...`
- ✅ `data:image/svg+xml;base64,...` (SVG inline)
- ✅ Qualquer URL pública de imagem

---

## Solução de Problemas

### Capas não aparecem?
1. Verifique se a URL é válida
2. Teste a URL diretamente no navegador
3. Certifique-se que é HTTPS (não HTTP)
4. Limpe o cache do navegador (Ctrl+Shift+R)

### Capas aparecem quebradas?
1. A URL pode estar bloqueada por CORS
2. Use URLs do Unsplash, Imgur ou SVG inline
3. Execute `atualizar-capas.php` para usar SVG

### Quer voltar para as capas SVG?
```
http://localhost/youtube/atualizar-capas.php
```

---

## Dicas

💡 **SVG é melhor**: Não depende de serviços externos, sempre funciona

💡 **Unsplash é confiável**: URLs estáveis e rápidas

💡 **Evite hotlinking**: Alguns sites bloqueiam uso externo de imagens

💡 **Teste antes**: Abra a URL da imagem no navegador antes de usar

---

## Exemplo de Atualização Manual

```sql
-- Atualizar capa de uma música específica
UPDATE musicas 
SET capa = 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=400&h=400&fit=crop'
WHERE id = 1;

-- Atualizar todas as músicas de um gênero
UPDATE musicas 
SET capa = 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=400&h=400&fit=crop'
WHERE genero = 'Arrocha';
```

Execute no phpMyAdmin ou via script PHP.

---

**Capas atualizadas! Seu site ficará muito mais bonito! 🎨**
