<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$state = current_auth_state();
send_json(200, [
    'session' => $state,
    'media' => media_catalog_for_session((string)$state['sessionType'], (bool)$state['isVa']),
]);
