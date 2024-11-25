<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylarele\HttpQueryConfig\Enums\SortOrder;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\Database\Factories\FooFactory;

class SortQueryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array<int,string> $expected
     */
    #[DataProvider('getSortsProvider')]
    public function testShouldSortInAscendingOrder(
        SortOrder $sortOrder,
        array $expected
    ): void {
        $this->createFoos();

        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'sortBy' => 'name',
                        'sortOrder' => $sortOrder->value,
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.0.name', $expected[0])
            ->assertJsonPath('data.1.name', $expected[1])
            ->assertJsonPath('data.2.name', $expected[2])
            ->assertJsonPath('data.3.name', $expected[3])
            ->assertJsonPath('data.4.name', $expected[4]);
    }

    public function testShouldValidatedSort(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'sortBy' => 'error',
                        'sortOrder' => 'error',
                    ]
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'The selected sort by is invalid. (and 1 more error)'
            )
            ->assertJsonPath(
                'errors',
                [
                    "sortBy" => ["The selected sort by is invalid."],
                    "sortOrder" => ["The selected sort order is invalid."],
                ]
            );
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

    public static function getSortsProvider(): Generator
    {
        yield 'Ascending' => [
            SortOrder::Ascending,
            ['Alice', 'Carol', 'Dave', 'Eve', 'Oscar']
        ];
        yield 'Descending' => [
            SortOrder::Descending,
            ['Oscar', 'Eve', 'Dave', 'Carol', 'Alice']
        ];
    }
}
