<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Test\Repository;

use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\ParameterType;
use StaySafe\Paginator\DataGrid\Repository\Filter;

final class FilterTest extends TestCase
{
    /**
     * @test
     * @dataProvider validParameterTypesDataProvider
     *
     * @param string $name
     * @param mixed  $value
     * @param mixed  $type
     */
    public function can_add_bind_map_with_valid_type(string $name, $value, $type): void
    {
        $filter = new Filter('', []);

        $filter->addBindMap($name, $value, $type);

        $this->assertSame($name, $filter->getBindMaps()[0]['name']);
        $this->assertSame($value, $filter->getBindMaps()[0]['value']);
        $this->assertSame($type, $filter->getBindMaps()[0]['type']);
    }

    public function validParameterTypesDataProvider(): array
    {
        return [
            [':count', 1, ParameterType::INTEGER],
            [':name', 'example', ParameterType::STRING],
            [':exists', null, ParameterType::NULL],
            [':valid', false, ParameterType::BOOLEAN],
        ];
    }

    /**
     * @test
     */
    public function invalid_type_throws_exception(): void
    {
        $filter = new Filter('', []);

        $this->expectException(\InvalidArgumentException::class);

        $filter->addBindMap(':example', 'something', 42);
    }

    /**
     * @test
     */
    public function can_add_multiple_mind_maps(): void
    {
        $filter = new Filter('', []);
        $bindings = [
            [':count', 1, ParameterType::INTEGER],
            [':name', 'example', ParameterType::STRING],
            [':exists', null, ParameterType::NULL],
            [':valid', false, ParameterType::BOOLEAN],
        ];

        foreach ($bindings as $binding) {
            $filter->addBindMap($binding[0], $binding[1], $binding[2]);
        }

        $this->assertCount(4, $filter->getBindMaps());
    }
}
