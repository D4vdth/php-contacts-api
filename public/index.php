<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Http\Router\Router;

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