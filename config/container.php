<?php

declare(strict_types=1);

use App\Domain\Repository\ContactRepositoryInterface;
use App\Infrastructure\Config\Container;
use App\Infrastructure\Http\Controller\ContactController;
use App\Infrastructure\Persistence\SQLite\SqliteConnection;
use App\Infrastructure\Persistence\SQLite\SqliteContactRepository;

use App\Application\UseCase\CreateContactUseCase;
use App\Application\UseCase\DeleteContactUseCase;
use App\Application\UseCase\GetContactUseCase;
use App\Application\UseCase\ListContactsUseCase;
use App\Application\UseCase\UpdateContactUseCase;

return (function (): Container {
    $container = new Container();

    $container->set(PDO::class, function () {
        $pdo = SqliteConnection::connect(__DIR__ . '/../database/contacts.db');
        SqliteConnection::runMigrations($pdo, __DIR__ . '/../database/migrations');

        return $pdo;
    });

    $container->set(ContactRepositoryInterface::class, function (Container $c) {
        return new SqliteContactRepository($c->get(PDO::class));
    });

    $container->set(CreateContactUseCase::class, function (Container $c) {
        return new CreateContactUseCase($c->get(ContactRepositoryInterface::class));
    });

    $container->set(DeleteContactUseCase::class, function (Container $c) {
        return new DeleteContactUseCase($c->get(ContactRepositoryInterface::class));
    });

    $container->set(GetContactUseCase::class, function (Container $c) {
        return new GetContactUseCase($c->get(ContactRepositoryInterface::class));
    });

    $container->set(ListContactsUseCase::class, function (Container $c) {
        return new ListContactsUseCase($c->get(ContactRepositoryInterface::class));
    });

    $container->set(UpdateContactUseCase::class, function (Container $c) {
        return new UpdateContactUseCase($c->get(ContactRepositoryInterface::class));
    });

    $container->set(ContactController::class, function (Container $c) {
        return new ContactController(
            $c->get(CreateContactUseCase::class),
            $c->get(DeleteContactUseCase::class),
            $c->get(GetContactUseCase::class),
            $c->get(ListContactsUseCase::class),
            $c->get(UpdateContactUseCase::class),
        );
    });

    return $container;
})();
