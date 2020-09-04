<?php

namespace StaySafe\Paginator\DataGrid\Repository;

interface PaginationQueryManagerInterface
{
    public function getPaginatedData(int $limit, int $page, array $sort, array $filterableFields, array $filter = []): array;
}
