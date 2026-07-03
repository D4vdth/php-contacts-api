<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\ListContactsDto;
use App\Domain\Repository\ContactRepositoryInterface;
use App\Domain\Repository\PaginatedResult;


final class ListContactsUseCase
{
    public function __construct(
        private readonly ContactRepositoryInterface $repository
    ){}

    public function execute(ListContactsDto $dto): PaginatedResult {
        return $this->repository->findAll($dto);
    }
}