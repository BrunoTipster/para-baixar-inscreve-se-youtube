-- ============================================================
-- DJ Bruno Pendrives - Script de Criação do Banco de Dados
-- ============================================================
-- Execute este script no phpMyAdmin ou MySQL CLI
-- Comando: mysql -u root -p < database.sql
-- ============================================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS `dj_bruno_pendrives`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `dj_bruno_pendrives`;

-- ============================================================
-- TABELA: usuarios
-- Armazena usuários autenticados via Google OAuth
-- ============================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `google_id`    VARCHAR(100) NOT NULL UNIQUE COMMENT 'ID único do Google',
    `nome`         VARCHAR(150) NOT NULL COMMENT 'Nome completo do usuário',
    `email`        VARCHAR(200) NOT NULL UNIQUE COMMENT 'Email do Google',
    `foto`         VARCHAR(500) DEFAULT NULL COMMENT 'URL da foto de perfil',
    `inscrito`     TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = inscrito no canal, 0 = não inscrito',
    `access_token` TEXT DEFAULT NULL COMMENT 'Token OAuth (criptografado)',
    `data_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ultimo_acesso` DATETIME DEFAULT NULL,
    INDEX `idx_google_id` (`google_id`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuários autenticados via Google OAuth';

-- ============================================================
-- TABELA: musicas
-- Catálogo de músicas disponíveis para download
-- ============================================================
CREATE TABLE IF NOT EXISTS `musicas` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `titulo`       VARCHAR(200) NOT NULL COMMENT 'Título da música',
    `artista`      VARCHAR(150) NOT NULL COMMENT 'Nome do artista',
    `genero`       VARCHAR(100) DEFAULT 'Eletrônica' COMMENT 'Gênero musical',
    `descricao`    TEXT DEFAULT NULL COMMENT 'Descrição opcional',
    `capa`         VARCHAR(500) DEFAULT NULL COMMENT 'Caminho da imagem de capa',
    `arquivo`      VARCHAR(500) NOT NULL COMMENT 'Caminho do arquivo de áudio',
    `tamanho`      INT UNSIGNED DEFAULT 0 COMMENT 'Tamanho do arquivo em bytes',
    `duracao`      VARCHAR(10) DEFAULT NULL COMMENT 'Duração no formato MM:SS',
    `downloads`    INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Contador de downloads',
    `ativo`        TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = ativo, 0 = desativado',
    `data`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de cadastro',
    INDEX `idx_ativo` (`ativo`),
    INDEX `idx_genero` (`genero`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de músicas para download';

-- ============================================================
-- TABELA: downloads
-- Registro de todos os downloads realizados
-- ============================================================
CREATE TABLE IF NOT EXISTS `downloads` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id`    INT UNSIGNED NOT NULL COMMENT 'ID do usuário que baixou',
    `musica_id`     INT UNSIGNED NOT NULL COMMENT 'ID da música baixada',
    `data_download` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ip`            VARCHAR(45) DEFAULT NULL COMMENT 'IP do usuário',
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`musica_id`) REFERENCES `musicas`(`id`) ON DELETE CASCADE,
    INDEX `idx_usuario` (`usuario_id`),
    INDEX `idx_musica` (`musica_id`),
    INDEX `idx_data` (`data_download`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Histórico de downloads dos usuários';

-- ============================================================
-- TABELA: admins
-- Administradores do sistema
-- ============================================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario`  VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nome de usuário admin',
    `senha`    VARCHAR(255) NOT NULL COMMENT 'Senha em bcrypt',
    `nome`     VARCHAR(150) DEFAULT NULL,
    `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Administradores do sistema';

-- ============================================================
-- DADOS INICIAIS
-- ============================================================

-- Admin padrão: usuário=admin, senha=admin123
-- Hash bcrypt gerado com password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO `admins` (`usuario`, `senha`, `nome`) VALUES
(
    'admin',
    '$2y$12$wMGjSf7QZvK7ZflHFfxubuNnBZ7LnN3KFzh8JCCIkSF3VCd3RI8yu',
    'Administrador DJ Bruno'
);

-- Músicas de exemplo para demonstração
INSERT INTO `musicas` (`titulo`, `artista`, `genero`, `descricao`, `capa`, `arquivo`, `duracao`, `ativo`) VALUES
('Summer Vibes 2024', 'DJ Bruno', 'House', 'Uma faixa eletrônica vibrante para o verão', 'uploads/capas/default.jpg', 'uploads/musicas/sample1.mp3', '5:23', 1),
('Neon Nights', 'DJ Bruno', 'Techno', 'Batidas pesadas para a madrugada', 'uploads/capas/default.jpg', 'uploads/musicas/sample2.mp3', '6:45', 1),
('Bass Drop Revolution', 'DJ Bruno', 'Dubstep', 'O drop mais esperado do ano', 'uploads/capas/default.jpg', 'uploads/musicas/sample3.mp3', '4:12', 1),
('Midnight Groove', 'DJ Bruno', 'Deep House', 'Groove profundo para dançar a noite toda', 'uploads/capas/default.jpg', 'uploads/musicas/sample4.mp3', '7:30', 1),
('Electric Storm', 'DJ Bruno', 'Trance', 'Uma tempestade elétrica sonora', 'uploads/capas/default.jpg', 'uploads/musicas/sample5.mp3', '5:55', 1),
('Urban Beats Vol.3', 'DJ Bruno', 'Hip-Hop', 'Os melhores beats urbanos', 'uploads/capas/default.jpg', 'uploads/musicas/sample6.mp3', '4:30', 1);
