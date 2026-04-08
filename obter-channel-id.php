<?php
/**
 * Ferramenta para obter Channel ID
 * Acesse esta página e faça login para ver seu Channel ID
 */

require_once 'config/session.php';
require_once 'config/youtube.php';

// Se recebeu código OAuth
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Troca código por token
    $data = [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => 'http://localhost/obter-channel-id.php',
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $tokenData = json_decode($response, true);
    
    if (isset($tokenData['access_token'])) {
        $accessToken = $tokenData['access_token'];
        
        // Busca informações do canal
        $ch = curl_init('https://www.googleapis.com/v3/channels?part=snippet&mine=true');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $channelData = json_decode($response, true);
        
        if (isset($channelData['items'][0])) {
            $channel = $channelData['items'][0];
            $channelId = $channel['id'];
            $channelTitle = $channel['snippet']['title'];
            
            echo "<!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <title>Channel ID Encontrado!</title>
                <style>
                    body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; text-align: center; }
                    .box { background: #1a1a1a; border: 2px solid #e50914; border-radius: 10px; padding: 30px; max-width: 600px; margin: 0 auto; }
                    h1 { color: #e50914; }
                    .channel-id { background: #2a2a2a; padding: 20px; border-radius: 5px; font-size: 18px; margin: 20px 0; word-break: break-all; }
                    .btn { background: #e50914; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
                </style>
            </head>
            <body>
                <div class='box'>
                    <h1>✅ Channel ID Encontrado!</h1>
                    <p><strong>Canal:</strong> $channelTitle</p>
                    <div class='channel-id'>
                        <strong>Channel ID:</strong><br>
                        <span id='channelId'>$channelId</span>
                    </div>
                    <button class='btn' onclick='copyChannelId()'>📋 Copiar Channel ID</button>
                    <p style='margin-top: 20px; color: #999;'>
                        Cole este ID em <code>config/youtube.php</code> na linha:<br>
                        <code>define('YOUTUBE_CHANNEL_ID', '$channelId');</code>
                    </p>
                </div>
                <script>
                    function copyChannelId() {
                        const text = document.getElementById('channelId').textContent;
                        navigator.clipboard.writeText(text).then(() => {
                            alert('Channel ID copiado!');
                        });
                    }
                </script>
            </body>
            </html>";
            exit();
        }
    }
    
    echo "Erro ao obter Channel ID. Tente novamente.";
    exit();
}

// Gera URL de login
$params = [
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => 'http://localhost/obter-channel-id.php',
    'response_type' => 'code',
    'scope' => 'https://www.googleapis.com/auth/youtube.readonly',
    'access_type' => 'offline',
    'prompt' => 'consent'
];

$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Obter Channel ID</title>
    <style>
        body { font-family: Arial; background: #0a0a0a; color: #fff; padding: 40px; text-align: center; }
        .box { background: #1a1a1a; border: 2px solid #e50914; border-radius: 10px; padding: 30px; max-width: 600px; margin: 0 auto; }
        h1 { color: #e50914; }
        .btn { background: #e50914; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 18px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #ff0000; }
    </style>
</head>
<body>
    <div class="box">
        <h1>🔍 Obter Channel ID</h1>
        <p>Faça login com a conta do seu canal do YouTube<br>para obter o Channel ID automaticamente.</p>
        <a href="<?php echo htmlspecialchars($authUrl); ?>" class="btn">🔐 Fazer Login com Google</a>
    </div>
</body>
</html>
