<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Helper;

use Doctrine\DBAL\ParameterType;
use StaySafe\Paginator\DataGrid\Repository\Filter;

/**
 * Class NativeSqlHelper.
 */
class NativeSqlHelper
{
    /**
     * Generate an order by string from a sort array.
     *
     * @param array $sort
     *
     * @return string
     */
    public function getSortString(array $sort): string
    {
        if (empty($sort) || !array_key_exists('fields', $sort) || !array_key_exists('direction', $sort)) {
            return '';
        }

        $sortString = ' ORDER BY ';
        $first = true;
        foreach ($sort['fields'] as $field) {
            if (!$first) {
                $sortString .= ', ';
            } else {
                $first = false;
            }

            $sortString .= $field.' '.$sort['direction'];
        }

        return $sortString;
    }

    /**
     * Get a filter string and a bind map to use when building native queries with prepared statements.
     *
     * @param array $filterableFields
     * @param array $filters
     * @param bool  $includeWhere
     *
     * @return Filter
     */
    public function getFilters(array $filterableFields, array $filters, bool $includeWhere = false): Filter
    {
        $filterString = '';
        $filterBindsMaps = [];

        if (!is_array($filters) || !count($filters)) {
            return new Filter();
        }

        if ($includeWhere) {
            $filterString .= ' WHERE ';
        }

        foreach ($filters as $filterType => $filter) {
            switch ($filterType) {
                case 'all':
                    $first = true;
                    foreach ($filterableFields as $field) {
                        if (!in_array($field, $filterableFields, true)) {
                            continue;
                        }

                        if ($first) {
                            if (!$includeWhere) {
                                $filterString .= ' AND';
                            }
                            $filterString .= ' ('.$field.' LIKE :allSearch';
                            $first = false;
                        }

                        $filterString .= ' OR '.$field.' LIKE :allSearch';
                    }
                    $filterString .= ')';

                    $filterBindsMaps[] = [
                        'name' => 'allSearch',
                        'value' => $filter,
                        'type' => ParameterType::STRING,
                    ];
                    break;
                default:
                    continue 2;
            }
        }

        return new Filter($filterString, $filterBindsMaps);
    }
}
