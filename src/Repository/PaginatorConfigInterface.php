<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Repository;

/**
 * Specify the list of columns across all tables used.
 * For multi-column search, add an entry with 'concat_ws'
 * e.g. "concat_ws(' ', u.first_name, u.last_name)"
 */
interface PaginatorConfigInterface
{
    /**
     * Returns an array with all the filtered fields which will
     * be added in the WHERE clause with the search data.
     * e.g. ['u.first_name'] will result to "u.first_name LIKE :allSearch"
     * where ':allSearch' will be replaced with the submitted data within '%'
     *
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
