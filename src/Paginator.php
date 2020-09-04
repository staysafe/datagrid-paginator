<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid;

use StaySafe\Paginator\DataGrid\Repository\PaginatorConfigInterface;
use StaySafe\Paginator\DataGrid\Repository\PaginationQueryManagerInterface;

final class Paginator
{
    public const MAX_PAGINATION_RESULTS = 5000;

    private GridPaginator $gridPaginator;

    public function __construct(GridPaginator $gridPaginator)
    {
        $this->gridPaginator = $gridPaginator;
    }

    /**
     * @param PaginatorConfigInterface        $paginatorConfig
     * @param PaginationQueryManagerInterface $queryManager
     * @param array                           $gridMetaData
     *
     * @return array<string, mixed>
     */
    public function getPaginatedData(
        PaginatorConfigInterface $paginatorConfig,
        PaginationQueryManagerInterface $queryManager,
        array $gridMetaData
    ): array {
        $meta = $this->gridPaginator->sanitizeGridMetaData(
            $gridMetaData
        );

        $sort = $this->gridPaginator->getSortArrayFroGridMetaData($gridMetaData, $paginatorConfig->getDefaultSort());

        $data = $queryManager->getPaginatedData(
            $meta['limit'] > 0 ? (int) $meta['limit'] : self::MAX_PAGINATION_RESULTS,
            (int) $meta['page'],
            $sort,
            $paginatorConfig->getFilterableFields(),
            $meta['search']
        );

        return [
            'data' => $data['items'],
            'meta' => [
                'columns' => $this->gridPaginator->calculateColumnStatusFromGridMetaData($paginatorConfig->getColumns(), $gridMetaData),
                'pagination' => [
                    'countTotalItems' => $data['countTotal'],
                    'currentPage' => $meta['page'],
                ],
            ],
        ];
    }
}
