<?php 

declare(strict_types=1);

namespace App\Application\Dto;


final readonly class ListContactsDto
{
    public function __construct(
        public int $page,
        public int $perPage,
        public string $sort,
        public string $order,
        public array $filters
    ){}

    public static function fromQueryParams(array $params): self
    {
        $page    = max(1, (int) ($params['page'] ?? 1));
        $perPage = min(100, max(1, (int) ($params['per_page'] ?? 15)));

        $allowedSortFields = ['name', 'last_name', 'email', 'created_at', 'updated_at'];
        $sort = in_array($params['sort'] ?? '', $allowedSortFields, true)
            ? $params['sort']
            : 'created_at';

        $order = strtolower($params['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedFilters = ['name', 'email'];
        $filters = array_filter(
            array_intersect_key($params, array_flip($allowedFilters)),
            fn (string $value): bool => $value !== '',
        );

        return new self(
            page: $page,
            perPage: $perPage,
            sort: $sort,
            order: $order,
            filters: $filters,
        );
    }
}