<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Dto\ListContactsDto;
use App\Domain\Repository\ContactRepositoryInterface;
use App\Domain\Repository\PaginatedResult;


final readonly class ListContactsUseCase
{
    public function __construct(
        private ContactRepositoryInterface $repository
    ){}

    public function execute(ListContactsDto $dto): PaginatedResult {
        return $this->repository->findAll(
            page: $dto->page,
            perPage: $dto->perPage,
            sort: $dto->sort,
            order: $dto->order,
            filters: $dto->filters,
        );
    }
}