<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Feature;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylarele\HttpQueryConfig\Concerns\HttpBuilder;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Sylarele\HttpQueryConfig\Http\QueryRequest;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Tests\TestCase;
use Workbench\Database\Factories\FooFactory;

/**
 * @internal
 */
#[CoversClass(HttpBuilder::class)]
#[CoversClass(Query::class)]
#[CoversClass(QueryRequest::class)]
final class FilterStringQueryTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('getFiltersProvider')]
    public function testShouldFilterString(
        FilterMode $filterMode,
        bool $not,
        string $value,
        string $expectedName,
        int $expectedCount,
    ): void {
        $this->createFoos();

        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'name[value]' => $value,
                        'name[mode]' => $filterMode->value,
                        'name[not]' => $not,
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount($expectedCount, 'data')
            ->assertJsonPath('data.0.name', $expectedName);
    }

    public function testNoData(): void
    {
        $this->createFoos();

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

    public function testShouldValidatedFilter(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'name[mode]' => 'error',
                        'name[not]' => 'error',
                    ]
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'The name.not field must be true or false. (and 1 more error)'
            )
            ->assertJsonPath(
                'errors',
                [
                    'name.not' => ['The name.not field must be true or false.'],
                    'name.mode' => ['The selected name.mode is invalid.'],
                ]
            );
    }

    public static function getFiltersProvider(): Generator
    {
        yield 'Equals' => [FilterMode::Equals, false, 'Alice', 'Alice', 1];

        yield 'Contains' => [FilterMode::Contains, false, 'Ali', 'Alice', 1];

        yield 'Not Equals' => [FilterMode::Equals, true, 'Alice', 'Carol', 4];

        yield 'Not Contains' => [FilterMode::Contains, true, 'Ali', 'Carol', 4];
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
}
