<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

header('Cache-Control: no-store');

$dataDir = app_root() . DIRECTORY_SEPARATOR . 'data';
$commentsFile = $dataDir . DIRECTORY_SEPARATOR . 'comments.json';
$leaderboardFile = $dataDir . DIRECTORY_SEPARATOR . 'leaderboard.json';

function godpanel_json_response(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function godpanel_ensure_store(string $path): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    if (!is_file($path)) {
        file_put_contents($path, "[]\n", LOCK_EX);
    }
}

function godpanel_read_json(string $path): array
{
    godpanel_ensure_store($path);
    $decoded = json_decode((string)file_get_contents($path), true);
    return is_array($decoded) ? $decoded : [];
}

function godpanel_write_json(string $path, array $entries): void
{
    godpanel_ensure_store($path);
    file_put_contents($path, json_encode(array_values($entries), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n", LOCK_EX);
}

function godpanel_sort_comments(array $entries): array
{
    usort($entries, static fn(array $a, array $b): int => strtotime((string)($b['createdAt'] ?? '')) <=> strtotime((string)($a['createdAt'] ?? '')));
    return array_values($entries);
}

function godpanel_historic_game_comments(): array
{
    return [
        ['id' => 'historic-2008-02-25-francesca', 'target' => 'game', 'userName' => 'francesca', 'sessionType' => 'legacy', 'message' => 'bulissimo...!!sei un genio come al solito...ciao ciao..', 'createdAt' => '2008-02-25T17:21:48+01:00'],
        ['id' => 'historic-2008-02-25-nton', 'target' => 'game', 'userName' => 'nton', 'sessionType' => 'legacy', 'message' => 'eh magari ancora il liceo a cazzeggiare "tutto" il di', 'createdAt' => '2008-02-25T23:12:49+01:00'],
        ['id' => 'historic-2008-02-26-mandy', 'target' => 'game', 'userName' => '*Mandy*', 'sessionType' => 'legacy', 'message' => 'moscio ti giuro 6 il mio idolo.... GRANDEEEEEE!! =*', 'createdAt' => '2008-02-26T18:24:59+01:00'],
        ['id' => 'historic-2008-02-27-andriy', 'target' => 'game', 'userName' => 'andriy', 'sessionType' => 'legacy', 'message' => 'senza la genialata delle circolari (mia proposta) era un gioco senza sale!', 'createdAt' => '2008-02-27T19:32:41+01:00'],
        ['id' => 'historic-2008-02-29-jul', 'target' => 'game', 'userName' => 'JULLLLLLLLLL', 'sessionType' => 'legacy', 'message' => 'Io volevo la perla e il pentathlon!!!!!', 'createdAt' => '2008-02-29T20:23:25+01:00'],
        ['id' => 'historic-2008-11-22-mte90', 'target' => 'game', 'userName' => 'Mte90', 'sessionType' => 'legacy', 'message' => 'direi che i livello dovrebbero durare di meno e che migliorata la grafica', 'createdAt' => '2008-11-22T19:59:11+01:00'],
        ['id' => 'historic-2009-10-09-death', 'target' => 'game', 'userName' => '_______death', 'sessionType' => 'legacy', 'message' => 'Splendido!', 'createdAt' => '2009-10-09T19:23:30+02:00'],
    ];
}

function godpanel_historic_media_comments(): array
{
    $baseComments = [
        ['slug' => 'andriy', 'userName' => 'Andriy', 'createdAt' => '2008-03-26', 'message' => 'sei il prossimo candidato al premio oscar come miglior regista.... ok ho detto la mia cazzata!'],
        ['slug' => 'jul', 'userName' => 'Jul', 'createdAt' => '2008-03-26', 'message' => 'Il moscio è il più grande regista sceneggiatore montatore e musicologo del mondo!!!!!'],
        ['slug' => 'marco-1', 'userName' => 'Marco', 'createdAt' => '2008-03-27', 'message' => 'Meno male che jacopo non ha niente da fare, sennò come avremmo potuto vedere il nostro film dei 100 giorni?'],
        ['slug' => 'matteo', 'userName' => 'Matteo', 'createdAt' => '2008-03-27', 'message' => "Auhahuahuhahua spettacolare veramente spettacolare, veramente bravo ja !!! Ora tocca fa un film come uqello dell'anno XD per chiudere inbellezza !!!"],
        ['slug' => 'arianna', 'userName' => 'Arianna', 'createdAt' => '2008-03-29', 'message' => 'Bravo moccichinooooo.. tu sei che sei un genio u.ù ma se non ti metti a studiare storia e filosofia ti picchiooooo!!!'],
        ['slug' => 'gabrjiskijineoq', 'userName' => 'Gabrjiskijineoq', 'createdAt' => '2008-03-30', 'message' => 'Che talento moscio! Bravo per il montaggio ... ma soprattutto per le musichette dei cartoni'],
        ['slug' => 'nico', 'userName' => 'nico', 'createdAt' => '2008-04-02', 'message' => 'nonostante nn abbia potuto partecipare venedo a mancare un grande attoreXD...bellissimo video moscio..direi perfetto...'],
        ['slug' => 'sunday', 'userName' => 'Sunday', 'createdAt' => '2008-05-05', 'message' => 'bello moscio...solo un piccolo particolare: a te non ti si vede mai in faccia. e poi tutte ste valige per una sola notte?!?!?!'],
        ['slug' => 'marco-2', 'userName' => 'Marco', 'createdAt' => '2018-03-09', 'message' => 'porca troia vacca schifa moscio, dieci merda di anni sono già passati'],
        ['slug' => 'richi', 'userName' => 'Richi', 'createdAt' => '2026-02-15', 'message' => 'Cazzo i capelli...'],
    ];
    return array_map(
        static fn(array $comment): array => [
            'id' => sprintf('historic-site-videos-%s', $comment['slug']),
            'target' => 'videos',
            'userName' => $comment['userName'],
            'sessionType' => 'legacy_site',
            'message' => $comment['message'],
            'createdAt' => $comment['createdAt'],
        ],
        $baseComments
    );
}

function godpanel_seeded_comments(array $entries): array
{
    $byId = [];
    foreach ($entries as $entry) {
        if (is_array($entry) && isset($entry['id'])) {
            $byId[(string)$entry['id']] = $entry;
        }
    }
    foreach (godpanel_historic_game_comments() as $entry) {
        $byId[$entry['id']] = $byId[$entry['id']] ?? $entry;
    }
    foreach (godpanel_historic_media_comments() as $entry) {
        $byId[$entry['id']] = $byId[$entry['id']] ?? $entry;
    }
    return array_values($byId);
}

function godpanel_sort_leaderboard(array $entries): array
{
    usort($entries, static function (array $a, array $b): int {
        $pointsCompare = ((float)($b['points'] ?? 0)) <=> ((float)($a['points'] ?? 0));
        if ($pointsCompare !== 0) {
            return $pointsCompare;
        }
        $voteCompare = ((float)($b['vote'] ?? 0)) <=> ((float)($a['vote'] ?? 0));
        if ($voteCompare !== 0) {
            return $voteCompare;
        }
        return strtotime((string)($b['endedAt'] ?? '')) <=> strtotime((string)($a['endedAt'] ?? ''));
    });
    return array_values($entries);
}

function godpanel_comment_payload(array $body): array
{
    $id = truncate_text($body['id'] ?? '', 120);
    return [
        'id' => $id !== '' ? $id : sprintf('godpanel-%d-%s', time(), bin2hex(random_bytes(3))),
        'target' => truncate_text($body['target'] ?? 'game', 64) ?: 'game',
        'userName' => truncate_text($body['userName'] ?? 'Anonimo', 48) ?: 'Anonimo',
        'sessionType' => truncate_text($body['sessionType'] ?? 'generic', 32) ?: 'generic',
        'message' => truncate_text($body['message'] ?? '', 4000),
        'createdAt' => truncate_text($body['createdAt'] ?? gmdate('c'), 80) ?: gmdate('c'),
    ];
}

function godpanel_leaderboard_payload(array $body): array
{
    $id = truncate_text($body['id'] ?? '', 120);
    return [
        'id' => $id !== '' ? $id : sprintf('godpanel-score-%d-%s', time(), bin2hex(random_bytes(3))),
        'userName' => truncate_text($body['userName'] ?? 'Anonimo', 48) ?: 'Anonimo',
        'sessionType' => truncate_text($body['sessionType'] ?? 'generic', 16) ?: 'generic',
        'reportTitle' => truncate_text($body['reportTitle'] ?? 'Report sessione', 80) ?: 'Report sessione',
        'endedAt' => truncate_text($body['endedAt'] ?? gmdate('c'), 80) ?: gmdate('c'),
        'status' => truncate_text($body['status'] ?? '', 80),
        'points' => round((float)($body['points'] ?? 0), 2),
        'vote' => round((float)($body['vote'] ?? 1), 2),
        'reportText' => mb_substr((string)($body['reportText'] ?? ''), 0, 30000),
        'rows' => is_array($body['rows'] ?? null) ? array_values($body['rows']) : [],
    ];
}

function godpanel_upsert_by_id(array $entries, array $payload): array
{
    foreach ($entries as $index => $entry) {
        if ((string)($entry['id'] ?? '') === (string)$payload['id']) {
            $entries[$index] = $payload;
            return $entries;
        }
    }
    $entries[] = $payload;
    return $entries;
}

function godpanel_delete_by_id(array $entries, string $id): array
{
    return array_values(array_filter(
        $entries,
        static fn(array $entry): bool => (string)($entry['id'] ?? '') !== $id
    ));
}

function godpanel_dashboard(string $commentsFile, string $leaderboardFile): array
{
    return [
        'comments' => godpanel_sort_comments(godpanel_seeded_comments(godpanel_read_json($commentsFile))),
        'leaderboard' => godpanel_sort_leaderboard(godpanel_read_json($leaderboardFile)),
    ];
}

start_app_session();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = truncate_text($_GET['action'] ?? '', 40);
$body = $method === 'POST' ? request_json() : [];
if ($action === '') {
    $action = truncate_text($body['action'] ?? 'status', 40);
}

if ($action === 'login') {
    $password = (string)($body['password'] ?? '');
    $config = app_config();
    if (!hash_equals((string)($config['godpanel_password'] ?? ''), $password)) {
        godpanel_json_response(403, ['error' => 'Password godpanel non corretta']);
    }
    $_SESSION['godpanel_admin'] = true;
    godpanel_json_response(200, ['ok' => true, 'admin' => current_admin_state()]);
}

if ($action === 'logout') {
    unset($_SESSION['godpanel_admin']);
    godpanel_json_response(200, ['ok' => true, 'admin' => current_admin_state()]);
}

if ($action === 'status') {
    godpanel_json_response(200, ['ok' => true, 'admin' => current_admin_state()]);
}

require_admin_session();

if ($action === 'dashboard') {
    godpanel_json_response(200, ['ok' => true] + godpanel_dashboard($commentsFile, $leaderboardFile));
}

if ($action === 'save-comment') {
    $payload = godpanel_comment_payload($body);
    if ($payload['message'] === '') {
        godpanel_json_response(422, ['error' => 'Il commento non puo essere vuoto']);
    }
    $entries = godpanel_upsert_by_id(godpanel_read_json($commentsFile), $payload);
    godpanel_write_json($commentsFile, $entries);
    godpanel_json_response(200, ['ok' => true, 'comments' => godpanel_sort_comments(godpanel_seeded_comments($entries))]);
}

if ($action === 'delete-comment') {
    $id = truncate_text($body['id'] ?? '', 120);
    if ($id === '') {
        godpanel_json_response(422, ['error' => 'ID commento mancante']);
    }
    $entries = godpanel_delete_by_id(godpanel_read_json($commentsFile), $id);
    godpanel_write_json($commentsFile, $entries);
    godpanel_json_response(200, ['ok' => true, 'comments' => godpanel_sort_comments(godpanel_seeded_comments($entries))]);
}

if ($action === 'save-leaderboard') {
    $payload = godpanel_leaderboard_payload($body);
    $entries = godpanel_upsert_by_id(godpanel_read_json($leaderboardFile), $payload);
    $entries = godpanel_sort_leaderboard($entries);
    godpanel_write_json($leaderboardFile, $entries);
    godpanel_json_response(200, ['ok' => true, 'leaderboard' => $entries]);
}

if ($action === 'delete-leaderboard') {
    $id = truncate_text($body['id'] ?? '', 120);
    if ($id === '') {
        godpanel_json_response(422, ['error' => 'ID punteggio mancante']);
    }
    $entries = godpanel_delete_by_id(godpanel_read_json($leaderboardFile), $id);
    $entries = godpanel_sort_leaderboard($entries);
    godpanel_write_json($leaderboardFile, $entries);
    godpanel_json_response(200, ['ok' => true, 'leaderboard' => $entries]);
}

if ($action === 'clear-leaderboard') {
    $sessionType = truncate_text($body['sessionType'] ?? '', 16);
    $entries = godpanel_read_json($leaderboardFile);
    if ($sessionType !== '') {
        $entries = array_values(array_filter(
            $entries,
            static fn(array $entry): bool => (string)($entry['sessionType'] ?? '') !== $sessionType
        ));
    } else {
        $entries = [];
    }
    $entries = godpanel_sort_leaderboard($entries);
    godpanel_write_json($leaderboardFile, $entries);
    godpanel_json_response(200, ['ok' => true, 'leaderboard' => $entries]);
}

godpanel_json_response(404, ['error' => 'Azione non valida']);
