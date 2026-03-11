<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

function stream_file(string $filePath, string $mime): never
{
    if (!is_file($filePath)) {
        http_response_code(404);
        exit('File non trovato');
    }
    $size = filesize($filePath);
    $start = 0;
    $end = $size - 1;
    header('Content-Type: ' . $mime);
    header('Accept-Ranges: bytes');
    header('Cache-Control: no-store');

    if (isset($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
        if ($matches[1] !== '') {
            $start = (int)$matches[1];
        }
        if ($matches[2] !== '') {
            $end = (int)$matches[2];
        }
        if ($end >= $size) {
            $end = $size - 1;
        }
        if ($start > $end || $start < 0) {
            header('Content-Range: bytes */' . $size);
            http_response_code(416);
            exit;
        }
        http_response_code(206);
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
    }

    $length = $end - $start + 1;
    header('Content-Length: ' . (string)$length);
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') {
        exit;
    }

    $handle = fopen($filePath, 'rb');
    if (!$handle) {
        http_response_code(500);
        exit('Errore apertura file');
    }
    set_time_limit(0);
    fseek($handle, $start);
    $remaining = $length;
    while ($remaining > 0 && !feof($handle)) {
        $chunk = fread($handle, min(1024 * 1024, $remaining));
        if ($chunk === false) {
            break;
        }
        echo $chunk;
        flush();
        $remaining -= strlen($chunk);
    }
    fclose($handle);
    exit;
}

$id = truncate_text($_GET['id'] ?? '', 40);
$config = app_config();
$map = $config['media_map'] ?? [];
if ($id === '' || !isset($map[$id]) || !is_array($map[$id])) {
    http_response_code(404);
    exit('Media non trovato');
}
$entry = $map[$id];
$scope = $entry['scope'] ?? 'public';
if ($scope === 'va') {
    require_va_session();
}
$baseDir = $config['media'][$scope] ?? null;
if (!is_string($baseDir)) {
    http_response_code(500);
    exit('Configurazione media non valida');
}
$fileName = basename((string)($entry['file'] ?? ''));
$mime = (string)($entry['mime'] ?? 'application/octet-stream');
stream_file($baseDir . DIRECTORY_SEPARATOR . $fileName, $mime);
