<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Contact;
use App\Domain\Exception\ContactNotFoundException;
use App\Domain\Repository\ContactRepositoryInterface;


final class GetContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ){}

    public function execute(string $id): Contact {

        $contact = $this->repository->findById($id);

        if (!$contact){
            throw ContactNotFoundException::withValue($contact);
        }
    
        return $contact;
    }
}