<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-TypeL application/json');

echo json_encode([
    "status" => "ok",
    "php_version" => PHP_VERSION
])

?>