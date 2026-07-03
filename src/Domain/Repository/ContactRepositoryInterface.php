<?php


declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Contact;

interface ContactRepositoryInterface
{

    public function save(Contact $contact): void;
    public function findById(string $id): Contact;
    public function findByEmail(string $email): ?Contact;

    /**
     * @param array<string, string> $filters
     */
    public function findAll(
        int $page,
        int $perPage,
        string $sort,
        string $order,
        array $filters,
    ): PaginatedResult;

    public function delete(string $id): void;

}