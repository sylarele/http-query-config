<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylarele\HttpQueryConfig\Concerns\HttpBuilder;
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
final class FieldsOnlyQueryTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldLoadFieldsOnly(): void
    {
        $this->createFoos();

        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'only' => ['name', 'size'],
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment([
                'name' => 'Carol',
            ]);
    }

    public function testShouldValidatedFieldOnly(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'only' => ['error'],
                    ]
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'The selected only.0 is invalid.'
            )
            ->assertJsonPath(
                'errors',
                ['only.0' => ['The selected only.0 is invalid.']]
            );
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
