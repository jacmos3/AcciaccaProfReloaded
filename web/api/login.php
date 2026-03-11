<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$payload = request_json();
$sessionType = truncate_text($payload['sessionType'] ?? 'generic', 16);
$name = truncate_text($payload['name'] ?? '', 24);
$password = (string)($payload['password'] ?? '');
$config = app_config();

if ($sessionType !== 'generic' && $sessionType !== 'va') {
    send_json(400, ['error' => 'Sessione non valida']);
}

start_app_session();

if ($sessionType === 'generic') {
    $_SESSION['session_type'] = 'generic';
    if ($name === '') {
        send_json(422, ['error' => 'Inserisci il nome alunno per entrare in Okkupazione']);
    }
    $_SESSION['user_name'] = $name;
    send_json(200, [
        'ok' => true,
        'session' => current_auth_state(),
        'media' => media_catalog_for_session('generic', false),
    ]);
}

if ($name === '') {
    send_json(422, ['error' => 'Inserisci il nome alunno per accedere alla sessione VA']);
}
if (!hash_equals((string)$config['va_password'], $password)) {
    send_json(403, ['error' => 'Password VA non corretta']);
}
$_SESSION['session_type'] = 'va';
$_SESSION['user_name'] = $name;
send_json(200, [
    'ok' => true,
    'session' => current_auth_state(),
    'media' => media_catalog_for_session('va', true),
]);
