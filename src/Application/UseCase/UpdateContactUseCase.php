<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\UpdateContactDto;
use App\Domain\Entity\Contact;
use App\Domain\Repository\ContactRepositoryInterface;
use App\Domain\Exception\DuplicateEmailException;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Phone;

final readonly class UpdateContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $repository,
    ) {}

    public function execute(UpdateContactDto $dto): Contact
    {
        $contact = $this->repository->findById($dto->id);

        $newEmail = Email::create($dto->email);

        $this->guardAgainstDuplicateEmail($newEmail, $dto->id);

        $contact->updateName($dto->name);
        $contact->updateLastName($dto->lastName);
        $contact->updateEmail($newEmail->value());
        $contact->replacePhones(
            array_map(
                fn (string $number): Phone => Phone::create($number),
                $dto->phones,
            ),
        );

        $this->repository->save($contact);

        return $contact;
    }

    private function guardAgainstDuplicateEmail(Email $email, string $currentContactId): void
    {
        $existing = $this->repository->findByEmail($email->value());

        if ($existing !== null && $existing->id()->value() !== $currentContactId) {
            throw DuplicateEmailException::withValue($email->value());
        }
    }
}