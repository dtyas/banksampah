<?php

declare(strict_types=1);

$backendPublic = __DIR__ . '/backend/bank-sampah-app/public/index.php';

if (php_sapi_name() === 'cli-server') {
    return false;
}

if (! file_exists($backendPublic)) {
    http_response_code(500);
    echo 'Folder backend Laravel tidak ditemukan di /backend/bank-sampah-app/public';
    exit;
}

header('Location: /backend/bank-sampah-app/public', true, 302);
exit;
