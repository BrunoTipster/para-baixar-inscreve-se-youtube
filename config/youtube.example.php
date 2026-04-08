<?php
/**
 * Configuração da YouTube Data API v3 e OAuth 2.0 Google
 * DJ Bruno Pendrives - Sistema de Downloads
 * 
 * INSTRUÇÕES:
 * 1. Copie este arquivo para youtube.php
 * 2. Preencha com suas credenciais do Google Cloud Console
 * 3. Configure o Channel ID do seu canal do YouTube
 */

define('GOOGLE_CLIENT_ID',     'SEU_CLIENT_ID_AQUI');
define('GOOGLE_CLIENT_SECRET', 'SEU_CLIENT_SECRET_AQUI');
define('GOOGLE_REDIRECT_URI',  'https://seudominio.com/callback.php');

define('YOUTUBE_CHANNEL_ID',     'SEU_CHANNEL_ID_AQUI');
define('YOUTUBE_CHANNEL_HANDLE', '@SeuCanal');
define('YOUTUBE_CHANNEL_URL',    'https://www.youtube.com/@SeuCanal?sub_confirmation=1');

define('GOOGLE_SCOPES', implode(' ', [
    'https://www.googleapis.com/auth/youtube.readonly',
    'https://www.googleapis.com/auth/userinfo.profile',
    'https://www.googleapis.com/auth/userinfo.email',
]));

define('GOOGLE_AUTH_URL',          'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL',         'https://oauth2.googleapis.com/token');
define('GOOGLE_USERINFO_URL',      'https://www.googleapis.com/oauth2/v3/userinfo');
define('YOUTUBE_SUBSCRIPTIONS_URL','https://www.googleapis.com/youtube/v3/subscriptions');

function getGoogleAuthUrl(string $state): string {
    $params = http_build_query([
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope'         => GOOGLE_SCOPES,
        'access_type'   => 'offline',
        'prompt'        => 'consent',
        'state'         => $state,
    ]);
    return GOOGLE_AUTH_URL . '?' . $params;
}

function exchangeCodeForToken(string $code) {
    $postData = http_build_query([
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code',
    ]);

    $ch = curl_init(GOOGLE_TOKEN_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postData,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response = curl_exec($ch);
    $erro     = curl_error($ch);
    curl_close($ch);

    if ($response === false || $erro) {
        error_log("exchangeCodeForToken cURL erro: " . $erro);
        return false;
    }

    return json_decode($response, true);
}

function getGoogleUserInfo(string $accessToken) {
    $ch = curl_init(GOOGLE_USERINFO_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response = curl_exec($ch);
    $erro     = curl_error($ch);
    curl_close($ch);

    if ($response === false || $erro) {
        error_log("getGoogleUserInfo cURL erro: " . $erro);
        return false;
    }

    return json_decode($response, true);
}

function checkYouTubeSubscription(string $accessToken): bool {
    $params = http_build_query([
        'part'         => 'snippet',
        'mine'         => 'true',
        'forChannelId' => YOUTUBE_CHANNEL_ID,
        'maxResults'   => 1,
    ]);

    $url = YOUTUBE_SUBSCRIPTIONS_URL . '?' . $params;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $response = curl_exec($ch);
    $erro     = curl_error($ch);
    curl_close($ch);

    if ($response === false || $erro) {
        error_log("checkYouTubeSubscription cURL erro: " . $erro);
        return false;
    }

    $data = json_decode($response, true);
    return isset($data['items']) && count($data['items']) > 0;
}
