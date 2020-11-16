<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Repository;

class AbstractPaginatorConfig implements PaginatorConfigInterface
{
    protected const OPTIONAL_COLUMNS = [];

    private array $enableColumns = [];

    /**
     * @var array|string[]
     */
    protected array $filterableFields = [];

    /**
     * @var array
     */
    protected array $defaultSort = [
        'direction' => 'DESC',
    ];

    /**
     * @var array|array[]
     */
    protected array $columns = [];

    protected array $optionalColumns = [];

    public function enableColumn(string $columnKey): void
    {
        if (
            !\array_key_exists($columnKey, $this->optionalColumns)
            || !\in_array($columnKey, static::OPTIONAL_COLUMNS, true)
        ) {
            throw new \LogicException(\sprintf('Column %s does not exist', $columnKey));
        }

        if (\array_key_exists($columnKey, $this->enableColumns)) {
            return;
        }

        $this->enableColumns[$columnKey] = true;
    }

    /**
     * @return array|array[]
     */
    public function getColumns(): array
    {
        if (\count($this->enableColumns) > 0) {
            $columns = $this->columns;
            foreach ($this->enableColumns as $addColumn => $add) {
                if (true === $add && \array_key_exists($addColumn, $this->optionalColumns)) {
                    $columns[] = $this->optionalColumns[$addColumn];
                }
            }

            return $columns;
        }

        return $this->columns;
    }

    /**
     * @return array|string[]
     */
    public function getDefaultSort(): array
    {
        return $this->defaultSort;
    }

    /**
     * @return array|string[]
     */
    public function getFilterableFields(): array
    {
        return $this->filterableFields;
    }
}
