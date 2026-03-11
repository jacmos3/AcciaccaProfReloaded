<?php
declare(strict_types=1);

return [
    'session_name' => 'ACCIACCA_PROF_RELOADED',
    'va_password' => getenv('ACCIACCA_VA_PASSWORD') ?: 'va2008',
    'godpanel_password' => getenv('ACCIACCA_GODPANEL_PASSWORD') ?: 'godpanel2008',
    'media' => [
        'public' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'public',
        'va' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'va',
        'shared' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'media',
    ],
    'media_map' => [
        'cena1' => ['scope' => 'public', 'file' => 'cena_prima_parte.mp4', 'mime' => 'video/mp4'],
        'cena2' => ['scope' => 'public', 'file' => 'cena_seconda_parte.mp4', 'mime' => 'video/mp4'],
        'cento_giorni' => ['scope' => 'public', 'file' => '100_giorni.mp4', 'mime' => 'video/mp4'],
        'coppa' => ['scope' => 'public', 'file' => 'la_coppa_si_smonta.mp4', 'mime' => 'video/mp4'],
        'lisbona1' => ['scope' => 'public', 'file' => 'gita_lisbona_primo_tempo.mp4', 'mime' => 'video/mp4'],
        'lisbona2' => ['scope' => 'public', 'file' => 'gita_lisbona_secondo_tempo.mp4', 'mime' => 'video/mp4'],
        'lisbona3' => ['scope' => 'public', 'file' => 'gita_lisbona_terzo_tempo.mp4', 'mime' => 'video/mp4'],
        'lisbona4' => ['scope' => 'public', 'file' => 'gita_lisbona_quarto_tempo.mp4', 'mime' => 'video/mp4'],
        'va0708' => ['scope' => 'va', 'file' => 'va_galilei_perugia_0708.mp4', 'mime' => 'video/mp4'],
        'cena2026foto' => ['scope' => 'shared', 'file' => 'photo_2026-03-11_15-26-29.jpg', 'mime' => 'image/jpeg'],
    ],
];
