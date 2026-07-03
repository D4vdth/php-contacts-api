<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Repository\ContactRepositoryInterface;


final class DeleteContactUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ){}

    public function execute(string $id): void {
        $this->repository->delete($id);
    }
}