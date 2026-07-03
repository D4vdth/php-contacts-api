<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Http\Router\Router;


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $request = Request::fromGlobals();

    $container = require __DIR__ . '/../config/container.php';

    $router = new Router();
    (require __DIR__ . '/../config/routes.php')($router, $container);

    $response = $router->dispatch($request);
} catch (\Throwable $e) {
    $response = Response::internalError($e->getMessage());
}

$response->send();