<?php

namespace StaySafe\Paginator\DataGrid\Repository;

interface PaginationQueryManagerInterface
{
    public function getPaginatedData(int $limit, int $page, array $sort, array $filterableFields, array $filter = []): array;

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function addColumn(string $name, $value): self;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasColumn(string $name): bool;

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function getColumn(string $name);
}
