<?php
/**
 * Forçar atualização de TODAS as capas
 */

// Conecta direto ao banco
try {
    $pdo = new PDO('mysql:host=localhost;dbname=dj_bruno_pendrives;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Define capas por gênero
    $capas = [
        'Arrocha' => 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=400&h=400&fit=crop&q=80',
        'Axé' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=400&h=400&fit=crop&q=80',
        'FlashBack' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=400&h=400&fit=crop&q=80',
        'Forró' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=400&h=400&fit=crop&q=80',
        'Vaquejada' => 'https://images.unsplash.com/photo-1598387993441-a364f854c3e1?w=400&h=400&fit=crop&q=80',
        'Swingueira' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=400&fit=crop&q=80',
        'Sertanejo' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=400&h=400&fit=crop&q=80',
        'Reggae' => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=400&h=400&fit=crop&q=80',
        'Diversos' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=400&h=400&fit=crop&q=80'
    ];
    
    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Forçar Capas</title>
        <style>
            body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; }
            .box { background: #1a1a1a; border: 2px solid #00e676; border-radius: 10px; padding: 30px; max-width: 1200px; margin: 0 auto; }
            h1 { color: #00e676; }
            .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin: 30px 0; }
            .card { background: #2a2a2a; border: 1px solid #333; border-radius: 10px; padding: 15px; }
            .card img { width: 100%; height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
            .card h3 { font-size: 1rem; margin: 10px 0 5px; color: #00e676; }
            .card .genero { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; background: rgba(155, 48, 255, 0.3); color: #9b30ff; margin: 5px 0; }
            .card .artista { font-size: 0.85rem; color: #888; }
            .sucesso { color: #00e676; margin: 20px 0; font-size: 1.2rem; font-weight: bold; }
            .btn { background: #00e676; color: #000; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px 5px 0 0; font-weight: bold; }
            .btn:hover { background: #00ff88; }
        </style>
    </head>
    <body>
        <div class='box'>
            <h1>🎨 Atualizando Capas - TODAS as Músicas</h1>";
    
    // Busca todas as músicas
    $stmt = $pdo->query("SELECT id, titulo, artista, genero, capa FROM musicas WHERE ativo = 1 ORDER BY genero, titulo");
    $musicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total de músicas encontradas: <strong>" . count($musicas) . "</strong></p>";
    echo "<div class='grid'>";
    
    $contador = 0;
    foreach ($musicas as $musica) {
        $genero = $musica['genero'];
        $capaUrl = $capas[$genero] ?? $capas['Diversos'];
        
        // Atualiza no banco
        $update = $pdo->prepare("UPDATE musicas SET capa = ? WHERE id = ?");
        $update->execute([$capaUrl, $musica['id']]);
        
        echo "<div class='card'>
                <img src='{$capaUrl}' alt='{$musica['titulo']}' onerror=\"this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22400%22%3E%3Crect fill=%22%231a1a2e%22 width=%22400%22 height=%22400%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%22120%22 text-anchor=%22middle%22 dy=%22.3em%22%3E🎵%3C/text%3E%3C/svg%3E'\">
                <h3>{$musica['titulo']}</h3>
                <span class='genero'>{$genero}</span>
                <p class='artista'>🎤 {$musica['artista']}</p>
              </div>";
        
        $contador++;
    }
    
    echo "</div>";
    
    echo "
            <hr style='border-color: #333; margin: 30px 0;'>
            <p class='sucesso'>✅ {$contador} capas atualizadas com sucesso!</p>
            <p style='color: #888;'>Todas as músicas agora têm capas do Unsplash.</p>
            <a href='/' class='btn'>🏠 Ver no Site Agora</a>
            <a href='/verificar-banco.php' class='btn'>🔍 Verificar Banco</a>
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
            <p style='color: #888; margin-top: 20px;'>Verifique se o MySQL está rodando e o banco existe.</p>
        </div>
    </body>
    </html>";
}
