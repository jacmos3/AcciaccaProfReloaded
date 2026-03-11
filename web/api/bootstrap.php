<?php
declare(strict_types=1);

function app_root(): string
{
    return dirname(__DIR__, 2);
}

function app_config(): array
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }
    $base = require __DIR__ . '/config.php';
    $localFile = __DIR__ . '/config.local.php';
    if (is_file($localFile)) {
        $local = require $localFile;
        if (is_array($local)) {
            $base = array_replace_recursive($base, $local);
        }
    }
    $config = $base;
    return $config;
}

function start_app_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $config = app_config();
    if (!empty($config['session_name']) && is_string($config['session_name'])) {
        session_name($config['session_name']);
    }
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

function send_json(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function request_json(): array
{
    $rawBody = file_get_contents('php://input');
    $decoded = json_decode($rawBody ?: '{}', true);
    return is_array($decoded) ? $decoded : [];
}

function truncate_text(mixed $value, int $maxLength): string
{
    return mb_substr(trim((string)($value ?? '')), 0, $maxLength);
}

function current_auth_state(): array
{
    start_app_session();
    $sessionType = $_SESSION['session_type'] ?? '';
    $userName = $_SESSION['user_name'] ?? '';
    $isVa = $sessionType === 'va' && $userName !== '';
    $isGeneric = $sessionType === 'generic';
    return [
        'authenticated' => $isVa || $isGeneric,
        'sessionType' => $sessionType ?: 'none',
        'userName' => is_string($userName) ? $userName : '',
        'isVa' => $isVa,
        'isGeneric' => $isGeneric,
    ];
}

function require_va_session(): array
{
    $state = current_auth_state();
    if (!$state['isVa']) {
        send_json(403, ['error' => 'Contenuto riservato alla sessione VA']);
    }
    return $state;
}

function media_catalog_for_session(string $sessionType, bool $includePrivate): array
{
    if ($sessionType !== 'va') {
        return [];
    }
    $items = [
        ['id' => 'cena1', 'title' => 'Cena coi prof - Parte 1', 'subtitle' => 'Compito in classe', 'kind' => 'video', 'note' => 'Streaming locale MP4.'],
        ['id' => 'cena2', 'title' => 'Cena coi prof - Parte 2', 'subtitle' => 'Indovina cosa ho in testa', 'kind' => 'video', 'note' => 'Streaming locale MP4.'],
        ['id' => 'cento_giorni', 'title' => '100 Giorni 15 Marzo 2008 - Roma', 'subtitle' => 'Archivio principale', 'kind' => 'video', 'note' => 'Video locale estratto dall’archivio originale.'],
        ['id' => 'coppa', 'title' => 'La coppa si smonta', 'subtitle' => 'Corsa campestre', 'kind' => 'video', 'note' => 'Conversione locale da WMV a MP4.'],
        ['id' => 'lisbona1', 'title' => 'Gita a Lisbona - Primo tempo', 'subtitle' => '2008', 'kind' => 'video', 'note' => 'Conversione locale da WMV a MP4.'],
        ['id' => 'lisbona2', 'title' => 'Gita a Lisbona - Secondo tempo', 'subtitle' => '2008', 'kind' => 'video', 'note' => 'Conversione locale da WMV a MP4.'],
        ['id' => 'lisbona3', 'title' => 'Gita a Lisbona - Terzo tempo', 'subtitle' => '2008', 'kind' => 'video', 'note' => 'Conversione locale da WMV a MP4.'],
        ['id' => 'lisbona4', 'title' => 'Gita a Lisbona - Quarto tempo', 'subtitle' => 'Incompleto', 'kind' => 'video', 'note' => 'Conversione locale da WMV a MP4.'],
    ];
    if ($includePrivate) {
        $items[] = ['id' => 'va0708', 'title' => 'V A 2007/2008', 'subtitle' => 'Sessione riservata', 'kind' => 'video', 'note' => 'Contenuto disponibile solo dopo accesso VA.'];
    }
    return $items;
}
