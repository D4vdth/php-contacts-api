<?php

declare(strict_types=1);

use App\Infrastructure\Http\Router\Router;
use App\Infrastructure\Persistence\SQLite\SqliteConnection;
use App\Infrastructure\Persistence\SQLite\SqliteContactRepository;
use App\Infrastructure\Http\Controller\ContactController;

use App\Application\UseCase\GetContactUseCase;
use App\Application\UseCase\CreateContactUseCase;
use App\Application\UseCase\DeleteContactUseCase;
use App\Application\UseCase\ListContactsUseCase;
use App\Application\UseCase\UpdateContactUseCase;

return function (Router $router): void {

    $pdo = SqliteConnection::connect(__DIR__ . '/../database/contacts.db');
    SqliteConnection::runMigrations($pdo, __DIR__ . '/../database/migrations');

    $repository = new SqliteContactRepository($pdo);
    $controller = new ContactController(
        new CreateContactUseCase($repository),
        new DeleteContactUseCase($repository),
        new GetContactUseCase($repository),
        new ListContactsUseCase($repository),
        new UpdateContactUseCase($repository),  
    );

    $router->get('/api/v1/contacts',      [$controller, 'index']);
    $router->get('/api/v1/contacts/{id}',  [$controller, 'show']);
    $router->post('/api/v1/contacts',      [$controller, 'store']);
    $router->put('/api/v1/contacts/{id}',  [$controller, 'update']);
    $router->delete('/api/v1/contacts/{id}', [$controller, 'destroy']);
};