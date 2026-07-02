<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SQLite;

use App\Domain\Entity\Contact;
use App\Domain\Exception\ContactNotFoundException;
use App\Domain\Repository\ContactRepositoryInterface;
use App\Domain\ValueObject\Phone;

use DateTimeImmutable;
use PDO;
use PDOException;

final class SqliteContactRepository implements ContactRepositoryInterface
{

    private function __construct(
        private readonly PDO $pdo,
    ){}

    public function save(Contact $contact): void
    {
        try {
            $this->pdo->beginTransaction();
            $this->upsertContact($contact);
            $this->replacePhones($contact);
            $this->pdo->commit();

        } catch (PDOException $pe) {
            $this->pdo->rollBack();
            throw $pe;
        }
    }

    private function upsertContact(Contact $contact): void
    {
        $sql = <<<'SQL'
            INSERT INTO contacts (id, name, last_name, email, created_at, updated_at)
            VALUES (:id, :name, :last_name, :email, :created_at, :updated_at)
            ON CONFLICT(id) DO UPDATE SET
                name       = :name,
                last_name  = :last_name,
                email      = :email,
                updated_at = :updated_at
            SQL;

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':id'         => $contact->id()->value(),
            ':name'       => $contact->name(),
            ':last_name'  => $contact->lastName(),
            ':email'      => $contact->email()->value(),
            ':created_at' => $contact->createdAt()->format(DateTimeImmutable::ATOM),
            ':updated_at' => $contact->updatedAt()->format(DateTimeImmutable::ATOM),
        ]);
    }

    private function replacePhones(Contact $contact): void
    {
        $contactId = $contact->id()->value();

        $this->pdo->prepare('DELETE FROM contact_phones WHERE contact_id = :contact_id')
            ->execute([':contact_id' => $contactId]);

        if ($contact->phones() === []) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO contact_phones (contact_id, phone) VALUES (:contact_id, :phone)'
        );

        foreach ($contact->phones() as $phone) {
            $stmt->execute([
                ':contact_id' => $contactId,
                ':phone'      => $phone->value(),
            ]);
        }
    }

    public function findById(string $id): Contact
    {
        $sql = 'SELECT id, name, last_name, email, created_at, updated_at FROM contacts WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch();

        if ($row === false) {
            throw ContactNotFoundException::withValue($id);
        }

        $phones = $this->findPhonesByContactId($id);

        return $this->hydrateContact($row, $phones);
    }

    /** 
     * @return Phone[] 
     * */

    private function findPhonesByContactId(string $contactId): array
    {
        $stmt = $this->pdo->prepare('SELECT phone FROM contact_phones WHERE contact_id = :contact_id');
        $stmt->execute([':contact_id' => $contactId]);

        return array_map(
            fn (array $row): Phone => Phone::create($row['phone']),
            $stmt->fetchAll(),
        );
    }

    /** 
     * @param Phone[] $phones 
     * */

    private function hydrateContact(array $row, array $phones): Contact
    {
        return Contact::reconstitute(
            id: $row['id'],
            name: $row['name'],
            lastName: $row['last_name'],
            email: $row['email'],
            phones: $phones,
            createdAt: new DateTimeImmutable($row['created_at']),
            updatedAt: new DateTimeImmutable($row['updated_at']),
        );
    }


    public function delete(string $id): void
    {
        $this->findById($id);

        $this->pdo->prepare('DELETE FROM contacts WHERE id = :id')
            ->execute([':id' => $id]);
    }

    public function findByEmail(string $email): ?Contact
    {
        $sql = 'SELECT id, name, last_name, email, created_at, updated_at FROM contacts WHERE email = :email';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        $phones = $this->findPhonesByContactId($row['id']);

        return $this->hydrateContact($row, $phones);
    }

    /** 
     * @return Contact[] 
     * */

    public function findAll(): array
    {
        $rows = $this->pdo->query(
            'SELECT id, name, last_name, email, created_at, updated_at FROM contacts ORDER BY created_at DESC'
        )->fetchAll();

        if ($rows === []) {
            return [];
        }

        $contactIds = array_column($rows, 'id');
        $phonesMap  = $this->findPhonesGroupedByContactId($contactIds);

        return array_map(
            fn (array $row): Contact => $this->hydrateContact(
                $row,
                $phonesMap[$row['id']] ?? [],
            ),
            $rows,
        );
    }

    /**
     * @param  string[] $contactIds
     * @return array<string, Phone[]>
     */

    private function findPhonesGroupedByContactId(array $contactIds): array
    {
        if ($contactIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($contactIds), '?'));

        $stmt = $this->pdo->prepare(
            "SELECT contact_id, phone FROM contact_phones WHERE contact_id IN ($placeholders)"
        );

        $stmt->execute($contactIds);

        $map = [];

        foreach ($stmt->fetchAll() as $row) {
            $map[$row['contact_id']][] = Phone::create($row['phone']);
        }

        return $map;
    }

}