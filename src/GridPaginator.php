<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid;

use Particle\Filter\Filter;
use StaySafe\Paginator\DataGrid\Particle\ParticleFilterFactory;

class GridPaginator
{
    public const MAX_PAGINATION_RESULTS = 5000;

    /**
     * Particle Filter.
     *
     * @var Filter
     */
    protected Filter $filter;

    /**
     * GridPaginator constructor.
     *
     * @param ParticleFilterFactory $particleFactory
     */
    public function __construct(ParticleFilterFactory $particleFactory)
    {
        $this->filter = $particleFactory::create();
    }

    /**
     * Provide column information based on the columns available and the frontend state.
     *
     * @param array $columns
     * @param array $gridMetaData
     *
     * @return array
     */
    public function calculateColumnStatusFromGridMetaData(array $columns, array $gridMetaData): array
    {
        if (array_key_exists('columns', $gridMetaData) && count($gridMetaData['columns'])) {
            foreach ($gridMetaData['columns'] as $columnRequest) {
                $columnIdx = array_search($columnRequest['value'], array_column($columns, 'value'), true);

                if (!is_int($columnIdx)) {
                    continue;
                }

                $columns[$columnIdx]['show'] = $columnRequest['show'];
            }
        }

        return $columns;
    }

    /**
     * Get the sort array or return the default sort.
     *
     * @param array $gridMetaData
     * @param array $defaultSortFields
     *
     * @return array
     */
    public function getSortArrayFroGridMetaData(array $gridMetaData, array $defaultSortFields): array
    {
        $sortDirection = 'asc';
        $sort = $defaultSortFields;

        if (array_key_exists('sort', $gridMetaData) && count($gridMetaData['sort'])) {
            $sortDirection = $gridMetaData['sort'][0]['direction'];
            $sort = $gridMetaData['sort'][0]['sortFields'];
        }

        return [
            'fields' => $sort,
            'direction' => $sortDirection,
        ];
    }

    /**
     * Sanitize the grids meta data and return a working construct.
     *
     * @param array $gridMetaData
     *
     * @return array
     */
    public function sanitizeGridMetaData(array $gridMetaData): array
    {
        $this->filter->all()->trim();
        $this->filter->value('page')->int()->defaults(1);
        $this->filter->value('limit')->int()->defaults(self::MAX_PAGINATION_RESULTS);
        $this->filter->value('search')->defaults([]);
        $this->filter->value('sort')->defaults([]);

        $gridMetaData = $this->filter->filter($gridMetaData);

        $this->decodeSearchQuery($gridMetaData);

        return $gridMetaData;
    }

    private function decodeSearchQuery(array &$gridMetaData): void
    {
        if (isset($gridMetaData['search']['all']) && \is_string($gridMetaData['search']['all'])) {
            $search = \trim($gridMetaData['search']['all']);
            if ('' === $search) {
                return;
            }
            if ('%' === $search[0]) {
                $search = substr($search, 1);
            }

            if ('%' === \substr($search, -1)) {
                $search = substr($search, 0, -1);
            }

            $gridMetaData['search']['all'] = '%'.\urldecode($search).'%';
        }
    }
}
