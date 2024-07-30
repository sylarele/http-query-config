<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\Database\Factories\FooFactory;

class FieldsOnlyQueryTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->createFoos();
    }

    public function testShouldFilterString(): void
    {
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

    public function testNoData(): void
    {
        $this
            ->getJson(route('foos.index', ['name[value]' => 'None']))
            ->assertOk()
            ->assertJsonCount(0, 'data');
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
