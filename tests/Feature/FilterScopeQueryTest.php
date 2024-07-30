<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\App\Enums\FooState;
use Workbench\Database\Factories\FooFactory;

class FilterScopeQueryTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldFilterWithScope(): void
    {
        $this->createFoos();

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

    public function testShouldFilterWithScopeAndMultipleValue(): void
    {
        $this->createFoos();

        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'whereStates' => [
                            'states' => [
                                FooState::Inactive->value,
                                FooState::Pending->value,
                            ],
                        ]
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Carol');
    }

    public function testShouldValidatedScope(): void
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
                'The where state.state field is required when where state is present.'
            );

        $this
            ->getJson(
                route(
                    'foos.index',
                    ['whereState[bad_key]' => FooState::Inactive->value]
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'The where state.state field is required when where state is present.'
            );

        $this
            ->getJson(
                route(
                    'foos.index',
                    ['whereStates[states][]' => 'error']
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'message',
                'The selected whereStates.states.0 is invalid.'
            );

        $this
            ->getJson(
                route(
                    'foos.index',
                    ['whereStates[bad_key][]' => FooState::Inactive->value]
                )
            )
            ->assertJsonPath(
                'message',
                'The where states.states field is required when where states is present.'
            )
        ;
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
