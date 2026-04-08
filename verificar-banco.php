<?php
/**
 * Verificar músicas no banco de dados
 */

require_once __DIR__ . '/config/database.php';

try {
    $db = getDB();
    
    // Conta total de músicas
    $stmt = $db->query("SELECT COUNT(*) as total FROM musicas WHERE ativo = 1");
    $total = $stmt->fetchColumn();
    
    // Lista músicas por gênero
    $stmt = $db->query("SELECT titulo, artista, genero FROM musicas WHERE ativo = 1 ORDER BY genero, titulo");
    $musicas = $stmt->fetchAll();
    
    // Conta por gênero
    $stmt = $db->query("SELECT genero, COUNT(*) as qtd FROM musicas WHERE ativo = 1 GROUP BY genero ORDER BY genero");
    $porGenero = $stmt->fetchAll();
    
    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <title>Verificar Banco de Dados</title>
        <style>
            body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; }
            .box { background: #1a1a1a; border: 2px solid #9b30ff; border-radius: 10px; padding: 30px; max-width: 900px; margin: 0 auto; }
            h1 { color: #9b30ff; }
            h2 { color: #00e676; margin-top: 30px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #333; }
            th { background: #2a2a2a; color: #9b30ff; }
            tr:hover { background: #1a1a2a; }
            .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; background: rgba(155, 48, 255, 0.2); color: #9b30ff; }
            .total { font-size: 2rem; font-weight: bold; color: #00e676; margin: 20px 0; }
            .btn { background: #9b30ff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px 5px 0 0; }
        </style>
    </head>
    <body>
        <div class='box'>
            <h1>🎵 Banco de Dados: dj_bruno_pendrives</h1>
            
            <div class='total'>✅ Total de Músicas Ativas: $total</div>
            
            <h2>📊 Músicas por Gênero</h2>
            <table>
                <tr>
                    <th>Gênero</th>
                    <th>Quantidade</th>
                </tr>";
    
    foreach ($porGenero as $row) {
        echo "<tr>
                <td><span class='badge'>{$row['genero']}</span></td>
                <td>{$row['qtd']}</td>
              </tr>";
    }
    
    echo "</table>
            
            <h2>🎶 Lista Completa de Músicas</h2>
            <table>
                <tr>
                    <th>Título</th>
                    <th>Artista</th>
                    <th>Gênero</th>
                </tr>";
    
    foreach ($musicas as $musica) {
        echo "<tr>
                <td>{$musica['titulo']}</td>
                <td>{$musica['artista']}</td>
                <td><span class='badge'>{$musica['genero']}</span></td>
              </tr>";
    }
    
    echo "</table>
            
            <hr style='border-color: #333; margin: 30px 0;'>
            <a href='/' class='btn'>🏠 Voltar para o Site</a>
            <a href='/admin/' class='btn'>⚙️ Painel Admin</a>
            <a href='/adicionar-musicas.php' class='btn'>➕ Adicionar Músicas</a>
        </div>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Erro</title>
        <style>
            body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; text-align: center; }
            .error { background: #1a1a1a; border: 2px solid #e50914; border-radius: 10px; padding: 30px; max-width: 600px; margin: 0 auto; }
            h1 { color: #e50914; }
        </style>
    </head>
    <body>
        <div class='error'>
            <h1>❌ Erro ao Conectar ao Banco</h1>
            <p>{$e->getMessage()}</p>
            <p style='color: #888; margin-top: 20px;'>Verifique se o banco 'dj_bruno_pendrives' existe e se o MySQL está rodando.</p>
        </div>
    </body>
    </html>";
}
