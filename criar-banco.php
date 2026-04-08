<?php
/**
 * Script para criar o banco de dados e tabelas
 * DJ Bruno Pendrives
 */

// Conecta ao MySQL sem selecionar banco
try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <title>Criar Banco de Dados</title>
        <style>
            body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; }
            .box { background: #1a1a1a; border: 2px solid #9b30ff; border-radius: 10px; padding: 30px; max-width: 800px; margin: 0 auto; }
            h1 { color: #9b30ff; }
            .sucesso { color: #00e676; margin: 10px 0; }
            .erro { color: #e50914; margin: 10px 0; }
            .info { color: #888; margin: 5px 0; font-size: 0.9rem; }
            .btn { background: #9b30ff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px 5px 0 0; }
        </style>
    </head>
    <body>
        <div class='box'>
            <h1>🗄️ Criando Banco de Dados</h1>";
    
    // Cria o banco
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `dj_bruno_pendrives` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p class='sucesso'>✅ Banco 'dj_bruno_pendrives' criado/verificado</p>";
    
    // Seleciona o banco
    $pdo->exec("USE `dj_bruno_pendrives`");
    
    // Cria tabela usuarios
    $pdo->exec("CREATE TABLE IF NOT EXISTS `usuarios` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `google_id` VARCHAR(100) NOT NULL UNIQUE,
        `nome` VARCHAR(150) NOT NULL,
        `email` VARCHAR(200) NOT NULL UNIQUE,
        `foto` VARCHAR(500) DEFAULT NULL,
        `inscrito` TINYINT(1) NOT NULL DEFAULT 0,
        `access_token` TEXT DEFAULT NULL,
        `data_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `ultimo_acesso` DATETIME DEFAULT NULL,
        INDEX `idx_google_id` (`google_id`),
        INDEX `idx_email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p class='sucesso'>✅ Tabela 'usuarios' criada</p>";
    
    // Cria tabela musicas
    $pdo->exec("CREATE TABLE IF NOT EXISTS `musicas` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `titulo` VARCHAR(200) NOT NULL,
        `artista` VARCHAR(150) NOT NULL,
        `genero` VARCHAR(100) DEFAULT 'Eletrônica',
        `descricao` TEXT DEFAULT NULL,
        `capa` VARCHAR(500) DEFAULT NULL,
        `arquivo` VARCHAR(500) NOT NULL,
        `tamanho` INT UNSIGNED DEFAULT 0,
        `duracao` VARCHAR(10) DEFAULT NULL,
        `downloads` INT UNSIGNED NOT NULL DEFAULT 0,
        `ativo` TINYINT(1) NOT NULL DEFAULT 1,
        `data` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_ativo` (`ativo`),
        INDEX `idx_genero` (`genero`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p class='sucesso'>✅ Tabela 'musicas' criada</p>";
    
    // Cria tabela downloads
    $pdo->exec("CREATE TABLE IF NOT EXISTS `downloads` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT UNSIGNED NOT NULL,
        `musica_id` INT UNSIGNED NOT NULL,
        `data_download` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `ip` VARCHAR(45) DEFAULT NULL,
        FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`musica_id`) REFERENCES `musicas`(`id`) ON DELETE CASCADE,
        INDEX `idx_usuario` (`usuario_id`),
        INDEX `idx_musica` (`musica_id`),
        INDEX `idx_data` (`data_download`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p class='sucesso'>✅ Tabela 'downloads' criada</p>";
    
    // Cria tabela admins
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `usuario` VARCHAR(100) NOT NULL UNIQUE,
        `senha` VARCHAR(255) NOT NULL,
        `nome` VARCHAR(150) DEFAULT NULL,
        `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "<p class='sucesso'>✅ Tabela 'admins' criada</p>";
    
    // Verifica se admin já existe
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE usuario = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        // Cria admin padrão
        $senhaHash = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO admins (usuario, senha, nome) VALUES (?, ?, ?)")
            ->execute(['admin', $senhaHash, 'Administrador DJ Bruno']);
        echo "<p class='sucesso'>✅ Admin padrão criado (usuário: admin, senha: admin123)</p>";
    } else {
        echo "<p class='info'>ℹ️ Admin já existe</p>";
    }
    
    echo "
            <hr style='border-color: #333; margin: 30px 0;'>
            <p class='sucesso'><strong>✅ Banco de dados configurado com sucesso!</strong></p>
            <p class='info'>Agora você pode adicionar as músicas.</p>
            <a href='/adicionar-musicas.php' class='btn'>➕ Adicionar Músicas</a>
            <a href='/verificar-banco.php' class='btn'>🔍 Verificar Banco</a>
            <a href='/' class='btn'>🏠 Ir para o Site</a>
        </div>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html>
    <html>
    <head><meta charset='UTF-8'><title>Erro</title>
    <style>
        body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; text-align: center; }
        .error { background: #1a1a1a; border: 2px solid #e50914; border-radius: 10px; padding: 30px; max-width: 600px; margin: 0 auto; }
        h1 { color: #e50914; }
    </style>
    </head>
    <body>
        <div class='error'>
            <h1>❌ Erro</h1>
            <p>{$e->getMessage()}</p>
        </div>
    </body>
    </html>";
}
