<?php


declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Contact;

interface ContactRepositoryInterface
{

    public function save(Contact $contact): void;
    public function findById(string $id): Contact;
    public function findByEmail(string $email): Contact;
    public function findAll(): array;
    public function delete(string $id): void;

}