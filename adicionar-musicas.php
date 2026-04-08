<?php
/**
 * Script para adicionar músicas com capas da internet
 * DJ Bruno Pendrives
 */

require_once __DIR__ . '/config/database.php';

// Conecta ao banco
$db = getDB();

// Função para buscar capa de álbum
function buscarCapaAlbum($genero, $artista = '') {
    // Usando capas reais do Unsplash e outras fontes públicas
    $capas = [
        'Arrocha' => 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=400&h=400&fit=crop',
        'Axé' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=400&h=400&fit=crop',
        'FlashBack' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=400&h=400&fit=crop',
        'Forró' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=400&h=400&fit=crop',
        'Vaquejada' => 'https://images.unsplash.com/photo-1598387993441-a364f854c3e1?w=400&h=400&fit=crop',
        'Swingueira' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=400&fit=crop',
        'Sertanejo' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=400&h=400&fit=crop',
        'Reggae' => 'https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=400&h=400&fit=crop',
        'Diversos' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=400&h=400&fit=crop',
        'House' => 'https://images.unsplash.com/photo-1571330735066-03aaa9429d89?w=400&h=400&fit=crop',
        'Techno' => 'https://images.unsplash.com/photo-1571266028243-d220c6e2e2e5?w=400&h=400&fit=crop',
        'Eletrônica' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=400&h=400&fit=crop'
    ];
    
    return $capas[$genero] ?? 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=400&h=400&fit=crop';
}

// Lista de músicas para adicionar
$musicas = [
    [
        'titulo' => '16GB Atualização Abril 2026 @kelcds',
        'artista' => 'DJ Bruno Pendrives',
        'genero' => 'Diversos',
        'descricao' => 'Pacote completo de músicas atualizadas - Abril 2026. Mais de 16GB de conteúdo exclusivo!',
        'arquivo' => 'https://www.mediafire.com/file/zlqoq7k06ovgpef/16gb_atualizacao_abril_2026_@kelcds.rar/file',
        'duracao' => null
    ],
    [
        'titulo' => 'Pacote Especial Gofile',
        'artista' => 'DJ Bruno',
        'genero' => 'Diversos',
        'descricao' => 'Pacote especial com músicas selecionadas',
        'arquivo' => 'https://gofile.io/d/kvQ5rL',
        'duracao' => null
    ],
    [
        'titulo' => 'Pacote Principal Google Drive',
        'artista' => 'DJ Bruno',
        'genero' => 'Diversos',
        'descricao' => 'Pacote principal de músicas no Google Drive',
        'arquivo' => 'https://drive.google.com/file/d/1ZQtu1QvMFTEdSD4Pl7xNLUdibD8sc3Mq/view',
        'duracao' => null
    ],
    [
        'titulo' => 'Músicas Diversas',
        'artista' => 'Vários Artistas',
        'genero' => 'Diversos',
        'descricao' => 'Pasta com músicas variadas de diversos gêneros',
        'arquivo' => 'https://drive.google.com/drive/folders/1xzPJROO_bkxUZNlqajNKopc_CaLFwmte',
        'duracao' => null
    ],
    [
        'titulo' => 'Arrocha - As Melhores',
        'artista' => 'Vários Artistas',
        'genero' => 'Arrocha',
        'descricao' => 'As melhores músicas de Arrocha para você curtir',
        'arquivo' => 'https://drive.google.com/file/d/1ezDChRpJdvBO6tPbGtDt4BLMNwGZh2vs/view?usp=drive_link',
        'duracao' => null
    ],
    [
        'titulo' => 'Axé - Coletânea Completa',
        'artista' => 'Vários Artistas',
        'genero' => 'Axé',
        'descricao' => 'Coletânea completa de Axé para animar sua festa',
        'arquivo' => 'https://drive.google.com/file/d/1p_Z3waJEWsr_Fc5rnB0hpTcTNJ1kJjo-/view?usp=drive_link',
        'duracao' => null
    ],
    [
        'titulo' => 'FlashBack - Sucessos Inesquecíveis',
        'artista' => 'Vários Artistas',
        'genero' => 'FlashBack',
        'descricao' => 'Os maiores sucessos dos anos 80, 90 e 2000',
        'arquivo' => 'https://drive.google.com/file/d/1p_Z3waJEWsr_Fc5rnB0hpTcTNJ1kJjo-/view?usp=drive_link',
        'duracao' => null
    ],
    [
        'titulo' => 'Forró - Pé de Serra',
        'artista' => 'Vários Artistas',
        'genero' => 'Forró',
        'descricao' => 'O melhor do forró pé de serra e eletrônico',
        'arquivo' => 'https://drive.google.com/file/d/1Hc0jQcr8DzAHTjYYLEAZjsv75BI37XZd/view?usp=drive_link',
        'duracao' => null
    ],
    [
        'titulo' => 'Vaquejada - Piseiro e Vaquejada',
        'artista' => 'Vários Artistas',
        'genero' => 'Vaquejada',
        'descricao' => 'As melhores músicas de vaquejada e piseiro',
        'arquivo' => 'https://drive.google.com/file/d/1Hc0jQcr8DzAHTjYYLEAZjsv75BI37XZd/view?usp=drive_link',
        'duracao' => null
    ],
    [
        'titulo' => 'Swingueira - Pagode Baiano',
        'artista' => 'Vários Artistas',
        'genero' => 'Swingueira',
        'descricao' => 'O melhor da swingueira e pagode baiano',
        'arquivo' => 'https://drive.google.com/file/d/14MQmkuJECBUtEAaTaVlpKOGHdMqnu72S/view?usp=drive_link',
        'duracao' => null
    ],
    [
        'titulo' => 'Sertanejo - Top Hits',
        'artista' => 'Vários Artistas',
        'genero' => 'Sertanejo',
        'descricao' => 'Os maiores sucessos do sertanejo universitário e raiz',
        'arquivo' => 'https://drive.google.com/file/d/1uiuJdVnxw9u3Q1iqDzorMV_FAUpf3oQf/view?usp=drive_link',
        'duracao' => null
    ],
    [
        'titulo' => 'Reggae - Roots & Dancehall',
        'artista' => 'Vários Artistas',
        'genero' => 'Reggae',
        'descricao' => 'O melhor do reggae roots e dancehall',
        'arquivo' => 'https://drive.google.com/file/d/1fgkPylAx7Lo8dsU2BHIdZ7MInv9OcrEe/view?usp=drive_link',
        'duracao' => null
    ]
];

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <title>Adicionando Músicas</title>
    <style>
        body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; }
        .box { background: #1a1a1a; border: 2px solid #9b30ff; border-radius: 10px; padding: 30px; max-width: 800px; margin: 0 auto; }
        h1 { color: #9b30ff; }
        .sucesso { color: #00e676; margin: 10px 0; }
        .erro { color: #e50914; margin: 10px 0; }
        .info { color: #888; margin: 5px 0; font-size: 0.9rem; }
        .btn { background: #9b30ff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='box'>
        <h1>🎵 Adicionando Músicas ao Sistema</h1>";

// Limpa músicas de exemplo antigas
try {
    $db->exec("DELETE FROM musicas WHERE id <= 6");
    echo "<p class='sucesso'>✅ Músicas de exemplo removidas</p>";
} catch (Exception $e) {
    echo "<p class='info'>ℹ️ Nenhuma música antiga para remover</p>";
}

// Adiciona novas músicas
$contador = 0;
foreach ($musicas as $musica) {
    try {
        // Busca capa baseada no gênero
        $capa = buscarCapaAlbum($musica['genero'], $musica['artista']);
        
        $stmt = $db->prepare("
            INSERT INTO musicas (titulo, artista, genero, descricao, capa, arquivo, duracao, ativo)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        
        $stmt->execute([
            $musica['titulo'],
            $musica['artista'],
            $musica['genero'],
            $musica['descricao'],
            $capa,
            $musica['arquivo'],
            $musica['duracao']
        ]);
        
        $contador++;
        echo "<p class='sucesso'>✅ Adicionada: <strong>{$musica['titulo']}</strong></p>";
        echo "<p class='info'>   Gênero: {$musica['genero']} | Artista: {$musica['artista']}</p>";
        
    } catch (Exception $e) {
        echo "<p class='erro'>❌ Erro ao adicionar '{$musica['titulo']}': {$e->getMessage()}</p>";
    }
}

echo "
        <hr style='border-color: #333; margin: 30px 0;'>
        <p class='sucesso'><strong>✅ Processo concluído!</strong></p>
        <p class='info'>Total de músicas adicionadas: <strong>$contador</strong></p>
        <a href='/' class='btn'>🏠 Voltar para o Site</a>
        <a href='/admin/' class='btn'>⚙️ Ir para Admin</a>
    </div>
</body>
</html>";
