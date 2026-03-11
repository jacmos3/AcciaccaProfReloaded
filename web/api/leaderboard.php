<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$dataDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data';
$leaderboardFile = $dataDir . DIRECTORY_SEPARATOR . 'leaderboard.json';

function ensureDataStore(string $dataDir, string $leaderboardFile): void
{
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0775, true);
    }
    if (!is_file($leaderboardFile)) {
        file_put_contents($leaderboardFile, "[]\n", LOCK_EX);
    }
}

function sendJson(int $statusCode, array $payload): never
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function truncateString(mixed $value, int $maxLength): string
{
    $text = trim((string)($value ?? ''));
    return mb_substr($text, 0, $maxLength);
}

function readLeaderboard(string $leaderboardFile): array
{
    ensureDataStore(dirname($leaderboardFile), $leaderboardFile);
    $handle = fopen($leaderboardFile, 'c+');
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

function writeLeaderboard(string $leaderboardFile, array $entries): void
{
    ensureDataStore(dirname($leaderboardFile), $leaderboardFile);
    $handle = fopen($leaderboardFile, 'c+');
    if (!$handle) {
        throw new RuntimeException('Impossibile aprire il file classifica.');
    }
    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        throw new RuntimeException('Impossibile bloccare il file classifica.');
    }
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
}

function sanitizeRows(mixed $rows): array
{
    if (!is_array($rows)) {
        return [];
    }
    $sanitized = [];
    foreach (array_slice($rows, 0, 200) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $sanitized[] = [
            'section' => truncateString($row['section'] ?? '', 80),
            'label' => truncateString($row['label'] ?? '', 120),
            'value' => truncateString($row['value'] ?? '', 120),
            'points' => truncateString($row['points'] ?? '', 120),
            'detail' => truncateString($row['detail'] ?? '', 400),
            'group' => truncateString($row['group'] ?? '', 80),
        ];
    }
    return $sanitized;
}

function sanitizeEntry(array $body): ?array
{
    $userName = truncateString($body['userName'] ?? '', 24);
    if ($userName === '') {
        return null;
    }

    $points = round((float)($body['points'] ?? 0), 2);
    $vote = max(1, min(10, (float)($body['vote'] ?? 1)));
    $sessionId = truncateString($body['sessionId'] ?? '', 80);
    $sessionType = truncateString($body['sessionType'] ?? '', 16);
    if ($sessionType !== 'generic' && $sessionType !== 'va') {
        return null;
    }

    return [
        'id' => $sessionId !== '' ? $sessionId : sprintf('%d-%s', time(), bin2hex(random_bytes(3))),
        'userName' => $userName,
        'sessionType' => $sessionType,
        'reportTitle' => truncateString($body['reportTitle'] ?? '', 80),
        'endedAt' => truncateString($body['endedAt'] ?? gmdate('c'), 80),
        'status' => truncateString($body['status'] ?? '', 40),
        'points' => $points,
        'vote' => round($vote, 2),
        'reportText' => mb_substr((string)($body['reportText'] ?? ''), 0, 30000),
        'rows' => sanitizeRows($body['rows'] ?? []),
    ];
}

function sortLeaderboard(array $entries): array
{
    usort($entries, static function (array $a, array $b): int {
        $pointsCompare = ($b['points'] ?? 0) <=> ($a['points'] ?? 0);
        if ($pointsCompare !== 0) {
            return $pointsCompare;
        }
        $voteCompare = ($b['vote'] ?? 0) <=> ($a['vote'] ?? 0);
        if ($voteCompare !== 0) {
            return $voteCompare;
        }
        return strtotime((string)($b['endedAt'] ?? '')) <=> strtotime((string)($a['endedAt'] ?? ''));
    });
    return array_slice($entries, 0, 100);
}

ensureDataStore($dataDir, $leaderboardFile);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$authState = current_auth_state();

if ($method === 'GET') {
    $requestedSessionType = truncateString($_GET['sessionType'] ?? '', 16);
    if ($requestedSessionType !== 'generic' && $requestedSessionType !== 'va') {
        $requestedSessionType = (string)($authState['sessionType'] ?? 'none');
    }
    $entries = array_values(array_filter(
        readLeaderboard($leaderboardFile),
        static fn(array $entry): bool => ($entry['sessionType'] ?? '') === $requestedSessionType
    ));
    sendJson(200, ['entries' => sortLeaderboard($entries)]);
}

if ($method !== 'POST') {
    sendJson(405, ['error' => 'Method not allowed']);
}

$rawBody = file_get_contents('php://input');
$decoded = json_decode($rawBody ?: '{}', true);
if (!is_array($decoded)) {
    sendJson(400, ['error' => 'Invalid payload']);
}

$entry = sanitizeEntry($decoded);
if ($entry === null) {
    sendJson(400, ['error' => 'Invalid payload']);
}
if (($authState['sessionType'] ?? 'none') !== $entry['sessionType']) {
    sendJson(403, ['error' => 'Sessione classifica non coerente']);
}

try {
    $entries = readLeaderboard($leaderboardFile);
    $existingIndex = -1;
    foreach ($entries as $index => $existingEntry) {
        if (($existingEntry['id'] ?? '') === $entry['id']) {
            $existingIndex = $index;
            break;
        }
    }
    if ($existingIndex >= 0) {
        $entries[$existingIndex] = $entry;
    } else {
        $entries[] = $entry;
    }
    $sorted = sortLeaderboard($entries);
    writeLeaderboard($leaderboardFile, $sorted);
    sendJson(200, ['ok' => true, 'entry' => $entry, 'entries' => $sorted]);
} catch (Throwable $error) {
    sendJson(500, ['error' => $error->getMessage()]);
}
