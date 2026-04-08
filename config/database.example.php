<?php
/**
 * Configuração do Banco de Dados
 * DJ Bruno Pendrives - Sistema de Downloads
 * 
 * INSTRUÇÕES:
 * 1. Copie este arquivo para database.php
 * 2. Preencha com as credenciais do seu banco de dados
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'dj_bruno_pendrives');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log("Erro de conexão com o banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Verifique as configurações.");
}
