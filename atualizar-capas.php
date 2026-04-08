<?php
/**
 * Atualizar capas das músicas com imagens personalizadas
 */

require_once __DIR__ . '/config/database.php';

// Função para gerar capa SVG personalizada
function gerarCapaSVG($genero, $titulo) {
    $cores = [
        'Arrocha' => ['bg' => '#8B0000', 'text' => '#FFD700', 'icon' => '💔'],
        'Axé' => ['bg' => '#FF6B35', 'text' => '#FFEB3B', 'icon' => '🎉'],
        'FlashBack' => ['bg' => '#9C27B0', 'text' => '#E1BEE7', 'icon' => '📻'],
        'Forró' => ['bg' => '#FF9800', 'text' => '#FFF3E0', 'icon' => '🪗'],
        'Vaquejada' => ['bg' => '#795548', 'text' => '#FFEB3B', 'icon' => '🤠'],
        'Swingueira' => ['bg' => '#E91E63', 'text' => '#FCE4EC', 'icon' => '💃'],
        'Sertanejo' => ['bg' => '#4CAF50', 'text' => '#E8F5E9', 'icon' => '🎸'],
        'Reggae' => ['bg' => '#FFEB3B', 'text' => '#1B5E20', 'icon' => '🌴'],
        'Diversos' => ['bg' => '#9b30ff', 'text' => '#E1BEE7', 'icon' => '🎵']
    ];
    
    $config = $cores[$genero] ?? $cores['Diversos'];
    $bg = $config['bg'];
    $text = $config['text'];
    $icon = $config['icon'];
    
    // Encurta o título se for muito longo
    $tituloDisplay = strlen($titulo) > 30 ? substr($titulo, 0, 27) . '...' : $titulo;
    
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400">
  <defs>
    <linearGradient id="grad-{$genero}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$bg};stop-opacity:1" />
      <stop offset="100%" style="stop-color:{$bg};stop-opacity:0.7" />
    </linearGradient>
  </defs>
  <rect width="400" height="400" fill="url(#grad-{$genero})"/>
  <circle cx="200" cy="150" r="60" fill="{$text}" opacity="0.2"/>
  <text x="200" y="170" font-size="80" text-anchor="middle" fill="{$text}">{$icon}</text>
  <text x="200" y="280" font-size="24" font-weight="bold" text-anchor="middle" fill="{$text}" font-family="Arial, sans-serif">{$genero}</text>
  <text x="200" y="320" font-size="14" text-anchor="middle" fill="{$text}" opacity="0.8" font-family="Arial, sans-serif">DJ Bruno Pendrives</text>
</svg>
SVG;
    
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

try {
    $db = getDB();
    
    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <title>Atualizar Capas</title>
        <style>
            body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; }
            .box { background: #1a1a1a; border: 2px solid #9b30ff; border-radius: 10px; padding: 30px; max-width: 1000px; margin: 0 auto; }
            h1 { color: #9b30ff; }
            .sucesso { color: #00e676; margin: 10px 0; }
            .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin: 30px 0; }
            .card { background: #2a2a2a; border-radius: 10px; padding: 15px; text-align: center; }
            .card img { width: 100%; border-radius: 8px; margin-bottom: 10px; }
            .card h3 { font-size: 0.9rem; margin: 5px 0; color: #9b30ff; }
            .card p { font-size: 0.8rem; color: #888; }
            .btn { background: #9b30ff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 10px 5px 0 0; }
        </style>
    </head>
    <body>
        <div class='box'>
            <h1>🎨 Atualizando Capas das Músicas</h1>";
    
    // Busca todas as músicas
    $stmt = $db->query("SELECT id, titulo, genero FROM musicas WHERE ativo = 1 ORDER BY genero, titulo");
    $musicas = $stmt->fetchAll();
    
    $contador = 0;
    echo "<div class='grid'>";
    
    foreach ($musicas as $musica) {
        $capaSVG = gerarCapaSVG($musica['genero'], $musica['titulo']);
        
        // Atualiza a capa no banco
        $stmt = $db->prepare("UPDATE musicas SET capa = ? WHERE id = ?");
        $stmt->execute([$capaSVG, $musica['id']]);
        
        echo "<div class='card'>
                <img src='{$capaSVG}' alt='{$musica['titulo']}'>
                <h3>{$musica['genero']}</h3>
                <p>{$musica['titulo']}</p>
              </div>";
        
        $contador++;
    }
    
    echo "</div>";
    
    echo "
            <hr style='border-color: #333; margin: 30px 0;'>
            <p class='sucesso'><strong>✅ {$contador} capas atualizadas com sucesso!</strong></p>
            <a href='/' class='btn'>🏠 Ver no Site</a>
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
        </div>
    </body>
    </html>";
}
