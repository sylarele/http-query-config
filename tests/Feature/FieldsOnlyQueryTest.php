<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\Database\Factories\FooFactory;

class FieldsOnlyQueryTest extends TestCase
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
                ["only.0" => ["The selected only.0 is invalid."]]
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
