<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\Database\Factories\FooFactory;

class PaginationQueryTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldValidatedPaginate(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'pagination' => 'error',
                        'limit' => 'error',
                        'page' => 'error',
                    ]
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'The selected pagination is invalid. (and 2 more errors)'
            )
            ->assertJsonPath(
                'errors',
                [
                    "pagination" => ["The selected pagination is invalid."],
                    "limit" => ["The limit field must be an integer."],
                    "page" => ["The page field must be an integer."]
                ]
            );
    }

    /**
     * @param array<string, int> $parameters
     */
    #[DataProvider('getPaginationProvider')]
    public function testShouldDefaultPaginate(
        array $parameters,
        int $countData,
        int $lastPage,
        int $perPage,
        int $to,
        int $total,
    ): void {
        FooFactory::new()->createMany(30);

        $this
            ->getJson(route('foos.index', $parameters))
            ->assertOk()
            ->assertJsonCount($countData, 'data')
            ->assertJsonCount(4, 'links')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.from', 1)
            ->assertJsonPath('meta.last_page', $lastPage)
            ->assertJsonPath('meta.per_page', $perPage)
            ->assertJsonPath('meta.to', $to)
            ->assertJsonPath('meta.total', $total);
    }

    public static function getPaginationProvider(): Generator
    {
        yield 'default' => [
            [],
            30,
            1,
            50,
            30,
            30
        ];

        yield 'with limit of 5' => [
            ['limit' => 5],
            5,
            6,
            5,
            5,
            30
        ];
    }
}
