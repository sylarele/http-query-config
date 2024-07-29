<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\Database\Factories\FooFactory;

class FilterStringQueryTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->createFoos();
    }

    #[DataProvider('getFiltersProvider')]
    public function testShouldFilterString(
        FilterMode $filterMode,
        string $value,
        string $expected
    ): void {
        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'name[value]' => $value,
                        'name[mode]' => $filterMode->value,
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', $expected);
    }

    public function testNoData(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    ['name[value]' => 'None']
                )
            )
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    private function createFoos(): void
    {
        FooFactory::new()
            ->createMany([
                ['name' => 'Carol'],
                ['name' => 'Alice'],
                ['name' => 'Eve'],
                ['name' => 'Oscar'],
                ['name' => 'Dave'],
            ]);
    }

    public static function getFiltersProvider(): Generator
    {
        yield 'Equals' => [FilterMode::Equals, 'Alice', 'Alice'];
        yield 'Contains' => [FilterMode::Contains, 'Ali', 'Alice'];
    }
}
