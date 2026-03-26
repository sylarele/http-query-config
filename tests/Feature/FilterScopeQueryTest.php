<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Feature;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylarele\HttpQueryConfig\Tests\TestCase;
use Workbench\App\Enums\FooState;
use Workbench\Database\Factories\FooFactory;

final class FilterScopeQueryTest extends TestCase
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

    public function testShouldWithScopeUsingDefault(): void
    {
        $this->createFoos();

        $response = $this
            ->getJson(
                route('foos.index', ['whereStateUsingDefault[state]'])
            );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /**
     * @param array<array-key, string> $arguments
     */
    #[DataProvider('getScopeByDefaultProvider')]
    public function testShouldFilterWithScopeByDefault(array $arguments): void
    {
        $this->createFoos();

        $response = $this
            ->getJson(route('foos.index', $arguments));

        $response
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public static function getScopeByDefaultProvider(): Generator
    {
        yield 'without value' => [
            ['whereStateDefault[state]']
        ];

        yield 'with value void' => [
            ['whereStateDefault[state]' => '']
        ];
    }

    public function testShouldFilterWithScopeAndMultipleValue(): void
    {
        $this->createFoos();

        $response = $this
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
            ->assertOk();

        $response
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Carol')
            ->assertJsonPath('data.1.name', 'John');
    }

    /**
     * @param array<array-key, string> $arguments
     */
    #[DataProvider('getValidatedScopeProvider')]
    public function testShouldValidatedScope(array $arguments, string $except): void
    {
        $response = $this
            ->getJson(route('foos.index', $arguments));

        $response
            ->assertUnprocessable()
            ->assertJsonPath('message', $except);
    }

    public static function getValidatedScopeProvider(): Generator
    {
        yield 'not array' => [
            ['whereState'],
            'The where state field must be an array.',
        ];

        yield 'without key' => [
            ['whereState[]'],
            'The where state.state field is required when where state is present.',
        ];

        yield 'without value' => [
            ['whereState[state]'],
            'The where state.state field is required when where state is present.',
        ];

        yield 'with bad key' => [
            ['whereState[bad_key]' => FooState::Inactive->value],
            'The where state.state field is required when where state is present.',
        ];

        yield 'with bad type and bad value' => [
            ['whereStates[states][]' => 'error'],
            'The selected whereStates.states.0 is invalid.',
        ];

        yield 'with bad key and bad type' => [
            ['whereStates[bad_key][]' => FooState::Inactive->value],
            'The where states.states field is required when where states is present.',
        ];
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
                ['name' => 'John', 'state' => FooState::Pending],
            ]);
    }
}
