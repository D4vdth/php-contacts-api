<?php

declare(strict_types=1);

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Http\Router\Router;

return function (Router $router): void {

    $router->get('/api/v1/contacts', function (Request $request): Response {
        return Response::json(['message' => 'list contacts — pending']);
    });

    $router->get('/api/v1/contacts/{id}', function (Request $request): Response {
        return Response::json(['message' => 'show contact — pending', 'id' => $request->routeParam('id')]);
    });

    $router->post('/api/v1/contacts', function (Request $request): Response {
        return Response::created(['message' => 'create contact — pending', 'body' => $request->body()]);
    });

    $router->put('/api/v1/contacts/{id}', function (Request $request): Response {
        return Response::json(['message' => 'update contact — pending', 'id' => $request->routeParam('id')]);
    });

    $router->delete('/api/v1/contacts/{id}', function (Request $request): Response {
        return Response::noContent();
    });
};