<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\App\Enums\FooState;
use Workbench\Database\Factories\FooFactory;

class FilterScopeQueryTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->createFoos();
    }

    public function testShouldFilterWithScope(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    ['whereState[state]' => FooState::Inactive->value]
                )
            )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Carol');
    }

    public function testShouldFilterWithScopeWithoutValue(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    ['whereState[state]']
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'The where state.state field must be a string. (and 1 more error)'
            );
    }

    private function createFoos(): void
    {
        FooFactory::new()
            ->createMany([
                ['name' => 'Carol', 'state' => FooState::Inactive],
                ['name' => 'Alice', 'state' => FooState::Active],
                ['name' => 'Eve', 'state' => FooState::Active],
                ['name' => 'Oscar', 'state' => FooState::Active],
                ['name' => 'Dave', 'state' => FooState::Active],
            ]);
    }
}
