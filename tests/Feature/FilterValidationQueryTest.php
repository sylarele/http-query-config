<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Sylarele\HttpQueryConfig\Http\QueryRequest;
use Sylarele\HttpQueryConfig\Query\Filter;
use Sylarele\HttpQueryConfig\Tests\TestCase;
use Workbench\App\Enums\FooState;
use Workbench\Database\Factories\FooFactory;

/**
 * @internal
 */
#[CoversClass(Filter::class)]
#[CoversClass(QueryRequest::class)]
final class FilterValidationQueryTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldAcceptAValidValue(): void
    {
        $this->createFoos();

        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'state[value]' => FooState::Pending->value,
                        'state[mode]' => FilterMode::Equals->value,
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Alice');
    }

    public function testShouldRejectAnInvalidValue(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    ['state[value]' => 'unknown']
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'errors',
                ['state.value' => ['The selected state.value is invalid.']]
            );
    }

    public function testShouldAcceptValidArrayValues(): void
    {
        $this->createFoos();

        $this
            ->getJson(
                route(
                    'foos.index',
                    ['states[value]' => [FooState::Pending->value, FooState::Inactive->value]]
                )
            )
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testShouldRejectInvalidArrayValues(): void
    {
        $this
            ->getJson(
                route(
                    'foos.index',
                    ['states[value]' => ['unknown']]
                )
            )
            ->assertUnprocessable()
            ->assertJsonPath(
                'errors',
                ['states.value.0' => ['The selected states.value.0 is invalid.']]
            );
    }

    private function createFoos(): void
    {
        FooFactory::new()
            ->createMany([
                ['name' => 'Carol', 'state' => FooState::Active],
                ['name' => 'Alice', 'state' => FooState::Pending],
                ['name' => 'Eve', 'state' => FooState::Inactive],
            ]);
    }
}
