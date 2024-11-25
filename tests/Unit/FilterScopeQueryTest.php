<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\App\Enums\FooState;
use Workbench\App\Models\Foo;
use Workbench\App\Queries\FooQuery;
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
        $query = new FooQuery();
        $query
            ->scope('whereState')
            ->setArgument('state', FooState::Inactive->value);

        $foos = Foo::query()
            ->configureForQuery($query)
            ->get();

        self::assertCount(1, $foos);
        $foo = $foos[0];
        self::assertInstanceOf(Foo::class, $foo);
        self::assertSame('Carol', $foo->name);
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
