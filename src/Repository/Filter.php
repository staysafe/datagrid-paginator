<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Repository;

use Webmozart\Assert\Assert;
use Doctrine\DBAL\ParameterType;

final class Filter
{
    private const PARAMETER_TYPES = [
        \PDO::PARAM_NULL,
        \PDO::PARAM_INT,
        \PDO::PARAM_STR,
        \PDO::PARAM_BOOL,
        ParameterType::BINARY,
    ];

    private string $filterString;

    private array $filterBindsMaps;

    public function __construct(string $filterString = '', array $filterBindsMaps = [])
    {
        $this->filterString = $filterString;
        $this->filterBindsMaps = $filterBindsMaps;
    }

    public function addBindMap(string $name, $value, int $type = ParameterType::STRING): void
    {
        Assert::keyExists(\array_flip(self::PARAMETER_TYPES), $type);

        $this->filterBindsMaps[] = [
            'name' => $name,
            'value' => $value,
            'type' => $type,
        ];
    }

    public function getBindMaps(): array
    {
        return $this->filterBindsMaps;
    }

    public function getFilterString(): string
    {
        return $this->filterString;
    }

    public function toArray(): array
    {
        return [
            'filterString' => $this->filterString,
            'bindMap' => $this->filterBindsMaps,
        ];
    }
}
