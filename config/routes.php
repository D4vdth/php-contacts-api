<?php

declare(strict_types=1);

use App\Infrastructure\Config\Container;
use App\Infrastructure\Http\Controller\ContactController;
use App\Infrastructure\Http\Router\Router;

return function (Router $router, Container $container): void {

    $controller = $container->get(ContactController::class);

    $router->get('/api/v1/contacts',      [$controller, 'index']);
    $router->get('/api/v1/contacts/{id}',  [$controller, 'show']);
    $router->post('/api/v1/contacts',      [$controller, 'store']);
    $router->put('/api/v1/contacts/{id}',  [$controller, 'update']);
    $router->delete('/api/v1/contacts/{id}', [$controller, 'destroy']);
};
