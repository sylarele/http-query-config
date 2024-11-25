<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\Database\Factories\FooFactory;

class FilterIntegerQueryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array<int, string> $expected
     */
    #[DataProvider('getFiltersProvider')]
    public function testShouldFilterString(
        FilterMode $filterMode,
        string $value,
        array $expected
    ): void {
        $this->createFoos();

        $response = $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'size[value]' => $value,
                        'size[mode]' => $filterMode->value,
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount(\count($expected), 'data');

        foreach ($expected as $key => $name) {
            $fooName = $response->json('data.' . $key . '.name');
            self::assertSame($name, $fooName);
        }
    }

    private function createFoos(): void
    {
        FooFactory::new()
            ->createMany([
                ['name' => 'Carol', 'size' => 1],
                ['name' => 'Alice', 'size' => 2],
                ['name' => 'Eve', 'size' => 3],
                ['name' => 'Oscar', 'size' => 4],
                ['name' => 'Dave', 'size' => 5],
            ]);
    }

    public static function getFiltersProvider(): Generator
    {
        yield 'Equals' => [
            FilterMode::Equals,
            '1',
            ['Carol']
        ];
        yield 'GreaterThan' => [
            FilterMode::GreaterThan,
            '3',
            ['Oscar', 'Dave']
        ];
        yield 'GreaterThanOrEqual' => [
            FilterMode::GreaterThanOrEqual,
            '3',
            ['Eve', 'Oscar', 'Dave']
        ];
        yield 'LessThan' => [
            FilterMode::LessThan,
            '3',
            ['Carol', 'Alice']
        ];
        yield 'LessThanOrEqual' => [
            FilterMode::LessThanOrEqual,
            '3',
            ['Carol', 'Alice', 'Eve']
        ];
    }
}
