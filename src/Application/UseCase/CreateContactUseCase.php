<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\CreateContactDto;
use App\Domain\Entity\Contact;
use App\Domain\Exception\DuplicateEmailException;
use App\Domain\Repository\ContactRepositoryInterface;


final class CreateContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ){}

    public function execute(CreateContactDto $dto): Contact {
        $contact = $this->repository->findByEmail($dto->email);

        if ($contact !== null){
            throw DuplicateEmailException::withValue($dto->email);
        }

        $newContact = Contact::create($dto->name, $dto->lastName, $dto->email, $dto->phones);
        $this->repository->save($newContact);
        return $newContact;

    }
}