<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\App\Models\Foo;
use Workbench\App\Queries\FooQuery;
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
        $query = new FooQuery();
        $query->fieldsOnly('name');

        $foos = Foo::query()
            ->configureForQuery($query)
            ->get();

        self::assertCount(5, $foos);
        $foo = $foos[0];
        self::assertInstanceOf(Foo::class, $foo);
        self::assertEquals('Carol', $foo->name);
        self::assertArrayNotHasKey('size', $foo);
    }

    private function createFoos(): void
    {
        FooFactory::new()
            ->createMany([
                ['name' => 'Carol', 'size' => 1],
                ['name' => 'Alice', 'size' => 1],
                ['name' => 'Eve', 'size' => 1],
                ['name' => 'Oscar', 'size' => 1],
                ['name' => 'Dave', 'size' => 1],
            ]);
    }
}
