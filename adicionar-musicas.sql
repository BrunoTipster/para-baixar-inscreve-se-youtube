-- ============================================================
-- Adicionar Músicas com Links Externos
-- DJ Bruno Pendrives
-- ============================================================

USE `dj_bruno_pendrives`;

-- Limpar músicas de exemplo
DELETE FROM `musicas` WHERE `id` <= 6;

-- ============================================================
-- MÚSICAS COM LINKS EXTERNOS E CAPAS
-- ============================================================

-- 16GB Atualização Abril 2026
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('16GB Atualização Abril 2026 @kelcds', 'DJ Bruno Pendrives', 'Diversos', 'Pacote completo de músicas atualizadas - Abril 2026. Mais de 16GB de conteúdo exclusivo!', 'https://i.scdn.co/image/ab67616d0000b273a048415db06a5b6fa7ec4e1a', 'https://www.mediafire.com/file/zlqoq7k06ovgpef/16gb_atualizacao_abril_2026_@kelcds.rar/file', NULL, 1);

-- Pacote Especial Gofile
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Pacote Especial Gofile', 'DJ Bruno', 'Diversos', 'Pacote especial com músicas selecionadas', 'https://i.scdn.co/image/ab67616d0000b273a048415db06a5b6fa7ec4e1a', 'https://gofile.io/d/kvQ5rL', NULL, 1);

-- Pacote Principal Google Drive
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Pacote Principal Google Drive', 'DJ Bruno', 'Diversos', 'Pacote principal de músicas no Google Drive', 'https://i.scdn.co/image/ab67616d0000b273a048415db06a5b6fa7ec4e1a', 'https://drive.google.com/file/d/1ZQtu1QvMFTEdSD4Pl7xNLUdibD8sc3Mq/view', NULL, 1);

-- Músicas Diversas
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Músicas Diversas', 'Vários Artistas', 'Diversos', 'Pasta com músicas variadas de diversos gêneros', 'https://i.scdn.co/image/ab67616d0000b273a048415db06a5b6fa7ec4e1a', 'https://drive.google.com/drive/folders/1xzPJROO_bkxUZNlqajNKopc_CaLFwmte', NULL, 1);

-- Arrocha
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Arrocha - As Melhores', 'Vários Artistas', 'Arrocha', 'As melhores músicas de Arrocha para você curtir', 'https://i.scdn.co/image/ab67616d0000b273f7db43292a6a99b21b51d5b4', 'https://drive.google.com/file/d/1ezDChRpJdvBO6tPbGtDt4BLMNwGZh2vs/view?usp=drive_link', NULL, 1);

-- Axé
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Axé - Coletânea Completa', 'Vários Artistas', 'Axé', 'Coletânea completa de Axé para animar sua festa', 'https://i.scdn.co/image/ab67616d0000b273e8b066f70c206551210d902b', 'https://drive.google.com/file/d/1p_Z3waJEWsr_Fc5rnB0hpTcTNJ1kJjo-/view?usp=drive_link', NULL, 1);

-- FlashBack
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('FlashBack - Sucessos Inesquecíveis', 'Vários Artistas', 'FlashBack', 'Os maiores sucessos dos anos 80, 90 e 2000', 'https://i.scdn.co/image/ab67616d0000b273c8b444df094279e70d0ed856', 'https://drive.google.com/file/d/1p_Z3waJEWsr_Fc5rnB0hpTcTNJ1kJjo-/view?usp=drive_link', NULL, 1);

-- Forró
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Forró - Pé de Serra', 'Vários Artistas', 'Forró', 'O melhor do forró pé de serra e eletrônico', 'https://i.scdn.co/image/ab67616d0000b2732c1e8b3e5e5c8e5f8e5c8e5f', 'https://drive.google.com/file/d/1Hc0jQcr8DzAHTjYYLEAZjsv75BI37XZd/view?usp=drive_link', NULL, 1);

-- Vaquejada
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Vaquejada - Piseiro e Vaquejada', 'Vários Artistas', 'Vaquejada', 'As melhores músicas de vaquejada e piseiro', 'https://i.scdn.co/image/ab67616d0000b273a1b2c3d4e5f6a7b8c9d0e1f2', 'https://drive.google.com/file/d/1Hc0jQcr8DzAHTjYYLEAZjsv75BI37XZd/view?usp=drive_link', NULL, 1);

-- Swingueira
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Swingueira - Pagode Baiano', 'Vários Artistas', 'Swingueira', 'O melhor da swingueira e pagode baiano', 'https://i.scdn.co/image/ab67616d0000b273b2c3d4e5f6a7b8c9d0e1f2a3', 'https://drive.google.com/file/d/14MQmkuJECBUtEAaTaVlpKOGHdMqnu72S/view?usp=drive_link', NULL, 1);

-- Sertanejo
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Sertanejo - Top Hits', 'Vários Artistas', 'Sertanejo', 'Os maiores sucessos do sertanejo universitário e raiz', 'https://i.scdn.co/image/ab67616d0000b273d4e5f6a7b8c9d0e1f2a3b4c5', 'https://drive.google.com/file/d/1uiuJdVnxw9u3Q1iqDzorMV_FAUpf3oQf/view?usp=drive_link', NULL, 1);

-- Reggae
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Reggae - Roots & Dancehall', 'Vários Artistas', 'Reggae', 'O melhor do reggae roots e dancehall', 'https://i.scdn.co/image/ab67616d0000b273e5f6a7b8c9d0e1f2a3b4c5d6', 'https://drive.google.com/file/d/1fgkPylAx7Lo8dsU2BHIdZ7MInv9OcrEe/view?usp=drive_link', NULL, 1);
