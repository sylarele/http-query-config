<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylarele\HttpQueryConfig\Concerns\HttpBuilder;
use Sylarele\HttpQueryConfig\Http\QueryRequest;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Tests\TestCase;
use Workbench\Database\Factories\BarFactory;
use Workbench\Database\Factories\FooFactory;

/**
 * @internal
 */
#[CoversClass(HttpBuilder::class)]
#[CoversClass(Query::class)]
#[CoversClass(QueryRequest::class)]
final class WithQueryTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldLoadRelation(): void
    {
        $this->createFoos();

        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'with' => ['bars'],
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount(1, 'data.0.bars')
            ->assertJsonPath('data.0.bars.0.name', 'Antoine');
    }

    public function testShouldValidatedSort(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'with' => ['error'],
                    ]
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'The selected with.0 is invalid.'
            )
            ->assertJsonPath(
                'errors',
                ['with.0' => ['The selected with.0 is invalid.']]
            );
    }

    private function createFoos(): void
    {
        FooFactory::new()
            ->has(BarFactory::new(['name' => 'Antoine']))
            ->createOne(['name' => 'Carol']);
    }
}
