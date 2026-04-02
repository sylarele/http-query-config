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
final class FilterIntegerQueryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array<int, string> $arguments
     */
    #[DataProvider('getValidatedFilterProvider')]
    public function testShouldValidatedFilter(array $arguments, string $except): void
    {
        $this->createFoos();

        $response = $this
            ->getJson(route('foos.index', $arguments));

        $response
            ->assertUnprocessable()
            ->assertJsonPath('message', $except);
    }

    public static function getValidatedFilterProvider(): Generator
    {
        yield 'not array' => [
            ['size'],
            'The size field must be an array.',
        ];

        yield 'without mode' => [
            ['size[mode]'],
            'The selected size.mode is invalid.',
        ];

        yield 'without not' => [
            ['size[not]'],
            'The size.not field must be true or false.',
        ];
    }

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
            $fooName = $response->json('data.'.$key.'.name');
            self::assertSame($name, $fooName);
        }
    }

    public static function getFiltersProvider(): Generator
    {
        yield 'Equals' => [
            FilterMode::Equals,
            '1',
            ['Carol'],
        ];

        yield 'GreaterThan' => [
            FilterMode::GreaterThan,
            '3',
            ['Oscar', 'Dave'],
        ];

        yield 'GreaterThanOrEqual' => [
            FilterMode::GreaterThanOrEqual,
            '3',
            ['Eve', 'Oscar', 'Dave'],
        ];

        yield 'LessThan' => [
            FilterMode::LessThan,
            '3',
            ['Carol', 'Alice'],
        ];

        yield 'LessThanOrEqual' => [
            FilterMode::LessThanOrEqual,
            '3',
            ['Carol', 'Alice', 'Eve'],
        ];
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
}
