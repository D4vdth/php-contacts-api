<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use App\Application\UseCase\CreateContactUseCase;
use App\Application\UseCase\DeleteContactUseCase;
use App\Application\UseCase\GetContactUseCase;
use App\Application\UseCase\ListContactsUseCase;
use App\Application\UseCase\UpdateContactUseCase;
use App\Infrastructure\Http\Controller\ContactController;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Persistence\SQLite\SqliteConnection;
use App\Infrastructure\Persistence\SQLite\SqliteContactRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class ContactApiTest extends TestCase
{
    private ContactController $controller;

    protected function setUp(): void
    {
        $pdo = $this->createInMemoryPdo();
        $repository = new SqliteContactRepository($pdo);

        $this->controller = new ContactController(
            new CreateContactUseCase($repository),
            new DeleteContactUseCase($repository),
            new GetContactUseCase($repository),
            new ListContactsUseCase($repository),
            new UpdateContactUseCase($repository),
        );
    }

    private function createInMemoryPdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:', options: [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');

        SqliteConnection::runMigrations($pdo, dirname(__DIR__, 3) . '/database/migrations');

        return $pdo;
    }

    private function createContact(array $overrides = []): array
    {
        $request = Request::create('POST', '/api/v1/contacts', body: array_merge([
            'name'      => 'Maria',
            'last_name' => 'Perez',
            'email'     => 'maria@example.com',
            'phones'    => ['+573001234567'],
        ], $overrides));

        $response = $this->controller->store($request);

        return [$response, $response->body()];
    }

    public function testCreateAndGetContact(): void
    {
        [$createResponse, $created] = $this->createContact();

        $this->assertSame(201, $createResponse->statusCode());
        $this->assertArrayHasKey('id', $created);
        $this->assertSame('Maria', $created['name']);
        $this->assertSame('maria@example.com', $created['email']);

        $showRequest = Request::create(
            'GET',
            "/api/v1/contacts/{$created['id']}",
            routeParams: ['id' => $created['id']],
        );
        $showResponse = $this->controller->show($showRequest);

        $this->assertSame(200, $showResponse->statusCode());
        $this->assertSame($created['id'], $showResponse->body()['id']);
    }

    public function testListContactsWithPagination(): void
    {
        $this->createContact(['email' => 'one@example.com']);
        $this->createContact(['email' => 'two@example.com']);
        $this->createContact(['email' => 'three@example.com']);

        $listRequest = Request::create('GET', '/api/v1/contacts', queryParams: [
            'page'     => '1',
            'per_page' => '2',
        ]);
        $listResponse = $this->controller->index($listRequest);
        $body = $listResponse->body();

        $this->assertSame(200, $listResponse->statusCode());
        $this->assertCount(2, $body['data']);
        $this->assertSame(3, $body['meta']['total']);
    }

    public function testCreateWithDuplicateEmailReturnsConflict(): void
    {
        $this->createContact(['email' => 'duplicate@example.com']);

        [$secondResponse] = $this->createContact(['email' => 'duplicate@example.com']);

        $this->assertSame(409, $secondResponse->statusCode());
    }
}
