<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\ContactRepositoryInterface;


final class ListContactsUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ){}

    public function execute(): array {
        return $this->repository->findAll();
    }
}