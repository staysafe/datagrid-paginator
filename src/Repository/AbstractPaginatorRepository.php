<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use StaySafe\Paginator\DataGrid\Helper\NativeSqlHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractPaginatorRepository extends ServiceEntityRepository
{
    protected NativeSqlHelper $nativeSqlHelper;

    public function __construct(NativeSqlHelper $nativeSqlHelper, ManagerRegistry $registry, $entityClass)
    {
        $this->nativeSqlHelper = $nativeSqlHelper;
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param string      $countSQL
     * @param Filter      $filters
     * @param string|null $groupBy
     * @param string|null $having
     * @param bool        $useCountWrapper
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getPaginationCount(
        string $countSQL,
        Filter $filters,
        ?string $groupBy = null,
        ?string $having = null,
        bool $useCountWrapper = false
    ): int {
        // filters
        $countSQL .= $filters->getFilterString();

        if (null !== $groupBy) {
            $countSQL .= $groupBy;
        }

        if (null !== $having) {
            $countSQL .= $having;
        }

        if ($useCountWrapper) {
            $countSQL = \sprintf('
SELECT
	COUNT(*) AS total
FROM (
    %s
) AS total
', $countSQL);
        }

        $statement = $this->getConnection()->prepare($countSQL);

        foreach ($filters->getBindMaps() as $bind) {
            $statement->bindValue($bind['name'], $bind['value'], $bind['type']);
        }

        $statement->execute();
        $countResult = $statement->fetch();

        return ($statement->rowCount() > 0) ? (int) $countResult['total'] : 0;
    }

    /**
     * @param string      $itemsSQL
     * @param Filter      $filters
     * @param array       $sort
     * @param int         $page
     * @param int         $limit
     * @param string|null $groupBy
     * @param string|null $having
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getPaginationItemsResult(
        string $itemsSQL,
        Filter $filters,
        array $sort,
        int $page,
        int $limit,
        ?string $groupBy = null,
        ?string $having = null
    ): array {
        // filters
        $itemsSQL .= $filters->getFilterString();

        if (null !== $groupBy) {
            $itemsSQL .= $groupBy;
        }

        if (null !== $having) {
            $itemsSQL .= $having;
        }

        // Sort
        $itemsSQL .= $this->nativeSqlHelper->getSortString($sort);

        // Limit
        $itemsSQL .= ' LIMIT :limit OFFSET :offset';

        $statement = $this->getConnection()->prepare($itemsSQL);
        $statement->bindValue('limit', $limit, ParameterType::INTEGER);
        $statement->bindValue('offset', $page > 1 ? ($page - 1) * $limit : 0, ParameterType::INTEGER);

        foreach ($filters->getBindMaps() as $bind) {
            $statement->bindValue($bind['name'], $bind['value'], $bind['type']);
        }

        $statement->execute();

        return $statement->fetchAll();
    }

    private function getConnection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }
}
