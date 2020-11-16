<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Repository;

abstract class AbstractPaginationQueryManager implements PaginationQueryManagerInterface
{
    private array $columns = [];

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function addColumn(string $name, $value): self
    {
        $this->columns[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasColumn(string $name): bool
    {
        return array_key_exists($name, $this->columns);
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function getColumn(string $name)
    {
        if ($this->hasColumn($name)) {
            return $this->columns[$name];
        }

        return null;
    }
}
