<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Repository;

interface PaginatorConfigInterface
{
    /**
     * @return array<int, string>
     */
    public function getFilterableFields(): array;

    /**
     * @return array<int, array<string, string|bool|array>>
     */
    public function getColumns(): array;

    /**
     * @return array<int, string>
     */
    public function getDefaultSort(): array;
}
