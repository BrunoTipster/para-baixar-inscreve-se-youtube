-- ============================================================
-- Atualizar TODAS as capas das músicas
-- Execute este SQL no phpMyAdmin se o script PHP não funcionar
-- ============================================================

USE `dj_bruno_pendrives`;

-- Atualizar capas por gênero com imagens do Unsplash

-- Arrocha
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'Arrocha';

-- Axé
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'Axé';

-- FlashBack
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'FlashBack';

-- Forró
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'Forró';

-- Vaquejada
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1598387993441-a364f854c3e1?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'Vaquejada';

-- Swingueira
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'Swingueira';

-- Sertanejo
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'Sertanejo';

-- Reggae
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'Reggae';

-- Diversos
UPDATE `musicas` 
SET `capa` = 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=400&h=400&fit=crop&q=80'
WHERE `genero` = 'Diversos';

-- Verificar resultado
SELECT id, titulo, genero, 
       CASE 
           WHEN capa IS NULL THEN '❌ SEM CAPA'
           WHEN capa = '' THEN '❌ VAZIO'
           ELSE '✅ OK'
       END as status_capa
FROM musicas 
WHERE ativo = 1
ORDER BY genero, titulo;
