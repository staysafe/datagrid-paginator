<?php

declare(strict_types=1);

namespace StaySafe\Paginator\DataGrid\Test;

use PHPUnit\Framework\TestCase;
use StaySafe\Paginator\DataGrid\GridPaginator;
use StaySafe\Paginator\DataGrid\Particle\ParticleFilterFactory;

final class GridPaginatorTest extends TestCase
{
    private GridPaginator $gridPaginator;

    protected function setUp(): void
    {
        $this->gridPaginator = new GridPaginator(new ParticleFilterFactory());
    }

    /**
     * @test
     */
    public function sanitize_empty_grid_metadata_returns_valid_construct(): void
    {
        $gridMetaData = $this->gridPaginator->sanitizeGridMetaData([]);

        $this->assertArrayHasKey('page', $gridMetaData);
        $this->assertArrayHasKey('limit', $gridMetaData);
        $this->assertArrayHasKey('search', $gridMetaData);
        $this->assertArrayHasKey('sort', $gridMetaData);
    }

    /**
     * @test
     * @dataProvider searchQueryDataProvider
     *
     * @param string $searchQuery
     * @param string $sanitised
     */
    public function decode_submitted_search_query(string $searchQuery, string $sanitised): void
    {
        $meta = [
            'page' => 1,
            'limit' => 5000,
            'sort'=> [],
            'search' => [
                'all' => $searchQuery
            ],
        ];

        $gridMetaData = $this->gridPaginator->sanitizeGridMetaData($meta);

        $this->assertSame($sanitised, $gridMetaData['search']['all']);
    }

    public function searchQueryDataProvider(): array
    {
        return [
            ['%dev%', '%dev%'],
            ['dev', '%dev%'],
            ['Doctor%20Who', '%Doctor Who%'],
            ['%Doctor%20Who%', '%Doctor Who%']
        ];
    }
}
