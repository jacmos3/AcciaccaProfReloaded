<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$authState = current_auth_state();
if (!$authState['authenticated']) {
    send_json(403, ['error' => 'Accesso richiesto']);
}

$dataDir = app_root() . DIRECTORY_SEPARATOR . 'data';
$commentsFile = $dataDir . DIRECTORY_SEPARATOR . 'comments.json';

function historic_game_comments(): array
{
    return [
        [
            'id' => 'historic-2008-02-25-francesca',
            'target' => 'game',
            'userName' => 'francesca',
            'sessionType' => 'legacy',
            'message' => 'bulissimo...!!sei un genio come al solito...ciao ciao..',
            'createdAt' => '2008-02-25T17:21:48+01:00',
        ],
        [
            'id' => 'historic-2008-02-25-nton',
            'target' => 'game',
            'userName' => 'nton',
            'sessionType' => 'legacy',
            'message' => 'eh magari ancora il liceo a cazzeggiare "tutto" il di',
            'createdAt' => '2008-02-25T23:12:49+01:00',
        ],
        [
            'id' => 'historic-2008-02-26-mandy',
            'target' => 'game',
            'userName' => '*Mandy*',
            'sessionType' => 'legacy',
            'message' => 'moscio ti giuro 6 il mio idolo.... GRANDEEEEEE!! =*',
            'createdAt' => '2008-02-26T18:24:59+01:00',
        ],
        [
            'id' => 'historic-2008-02-27-andriy',
            'target' => 'game',
            'userName' => 'andriy',
            'sessionType' => 'legacy',
            'message' => 'senza la genialata delle circolari (mia proposta) era un gioco senza sale!',
            'createdAt' => '2008-02-27T19:32:41+01:00',
        ],
        [
            'id' => 'historic-2008-02-29-jul',
            'target' => 'game',
            'userName' => 'JULLLLLLLLLL',
            'sessionType' => 'legacy',
            'message' => 'Io volevo la perla e il pentathlon!!!!!',
            'createdAt' => '2008-02-29T20:23:25+01:00',
        ],
        [
            'id' => 'historic-2008-11-22-mte90',
            'target' => 'game',
            'userName' => 'Mte90',
            'sessionType' => 'legacy',
            'message' => 'direi che i livello dovrebbero durare di meno e che migliorata la grafica',
            'createdAt' => '2008-11-22T19:59:11+01:00',
        ],
        [
            'id' => 'historic-2009-10-09-death',
            'target' => 'game',
            'userName' => '_______death',
            'sessionType' => 'legacy',
            'message' => 'Splendido!',
            'createdAt' => '2009-10-09T19:23:30+02:00',
        ],
    ];
}

function historic_media_comments(): array
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

function ensure_comments_store(string $dataDir, string $commentsFile): void
{
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0775, true);
    }
    if (!is_file($commentsFile)) {
        file_put_contents($commentsFile, "[]\n", LOCK_EX);
    }
}

function read_comments(string $commentsFile): array
{
    ensure_comments_store(dirname($commentsFile), $commentsFile);
    $handle = fopen($commentsFile, 'c+');
    if (!$handle) {
        return [];
    }
    flock($handle, LOCK_SH);
    $contents = stream_get_contents($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
    $decoded = json_decode($contents ?: '[]', true);
    return is_array($decoded) ? $decoded : [];
}

function with_historic_seed(array $entries): array
{
    $byId = [];
    foreach ($entries as $entry) {
        if (is_array($entry) && isset($entry['id'])) {
            $byId[(string)$entry['id']] = $entry;
        }
    }
    foreach (historic_game_comments() as $entry) {
        $byId[$entry['id']] = $byId[$entry['id']] ?? $entry;
    }
    foreach (historic_media_comments() as $entry) {
        $byId[$entry['id']] = $byId[$entry['id']] ?? $entry;
    }
    return array_values($byId);
}

function write_comments(string $commentsFile, array $entries): void
{
    ensure_comments_store(dirname($commentsFile), $commentsFile);
    $handle = fopen($commentsFile, 'c+');
    if (!$handle) {
        throw new RuntimeException('Impossibile aprire il file commenti.');
    }
    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        throw new RuntimeException('Impossibile bloccare il file commenti.');
    }
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
}

function allowed_comment_targets(array $authState): array
{
    $targets = ['game'];
    foreach (media_catalog_for_session((string)($authState['sessionType'] ?? 'generic'), (bool)($authState['isVa'] ?? false)) as $item) {
        $id = truncate_text($item['id'] ?? '', 64);
        if ($id !== '') {
            $targets[] = $id;
        }
    }
    return array_values(array_unique($targets));
}

function require_comment_target(array $authState, string $target): string
{
    $normalized = truncate_text($target, 64);
    if ($normalized === '') {
        send_json(400, ['error' => 'Target commenti mancante']);
    }
    if (!in_array($normalized, allowed_comment_targets($authState), true)) {
        send_json(403, ['error' => 'Target commenti non disponibile in questa sessione']);
    }
    return $normalized;
}

function video_comment_targets(array $authState): array
{
    return array_values(array_filter(
        allowed_comment_targets($authState),
        static fn(string $target): bool => $target !== 'game'
    ));
}

function comments_for_target(array $entries, string $target, array $authState): array
{
    if ($target === 'game') {
        return dedupe_comments(array_values(array_filter(
            $entries,
            static fn(array $entry): bool => ($entry['target'] ?? '') === 'game'
        )));
    }
    $videoTargets = video_comment_targets($authState);
    return dedupe_comments(array_values(array_filter(
        $entries,
        static fn(array $entry): bool => ($entry['target'] ?? '') === 'videos' || in_array((string)($entry['target'] ?? ''), $videoTargets, true)
    )));
}

function sort_comments(array $entries): array
{
    usort($entries, static function (array $a, array $b): int {
        return strtotime((string)($b['createdAt'] ?? '')) <=> strtotime((string)($a['createdAt'] ?? ''));
    });
    return $entries;
}

function dedupe_comments(array $entries): array
{
    $unique = [];
    foreach ($entries as $entry) {
        $key = implode('|', [
            (string)($entry['sessionType'] ?? ''),
            mb_strtolower(trim((string)($entry['userName'] ?? ''))),
            trim((string)($entry['message'] ?? '')),
            (string)($entry['createdAt'] ?? ''),
        ]);
        $unique[$key] = $entry;
    }
    return array_values($unique);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $target = require_comment_target($authState, (string)($_GET['target'] ?? ''));
    $entries = comments_for_target(with_historic_seed(read_comments($commentsFile)), $target, $authState);
    send_json(200, ['entries' => array_slice(sort_comments($entries), 0, 100)]);
}

if ($method !== 'POST') {
    send_json(405, ['error' => 'Method not allowed']);
}

$body = request_json();
$target = require_comment_target($authState, (string)($body['target'] ?? ''));
$message = truncate_text($body['message'] ?? '', 1200);
if ($message === '') {
    send_json(400, ['error' => 'Commento vuoto']);
}

$entry = [
    'id' => sprintf('%d-%s', time(), bin2hex(random_bytes(3))),
    'target' => $target,
    'userName' => truncate_text($authState['userName'] ?? '', 24),
    'sessionType' => truncate_text($authState['sessionType'] ?? '', 16),
    'message' => $message,
    'createdAt' => gmdate('c'),
];

try {
    $entries = with_historic_seed(read_comments($commentsFile));
    $entries[] = $entry;
    if (count($entries) > 5000) {
        $entries = array_slice($entries, -5000);
    }
    write_comments($commentsFile, $entries);
    $filtered = comments_for_target($entries, $target, $authState);
    send_json(200, ['ok' => true, 'entry' => $entry, 'entries' => array_slice(sort_comments($filtered), 0, 100)]);
} catch (Throwable $error) {
    send_json(500, ['error' => $error->getMessage()]);
}
