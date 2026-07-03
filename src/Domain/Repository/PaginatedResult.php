<?php

declare(strict_types=1);

namespace App\Domain\Repository;

final readonly class PaginatedResult
{
    public function __construct(
        public array $items,
        public int $total,
    ) {}
}
