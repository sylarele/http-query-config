<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Unit;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylarele\HttpQueryConfig\Enums\SortOrder;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\App\Models\Foo;
use Workbench\App\Queries\FooQuery;
use Workbench\Database\Factories\FooFactory;

class SortQueryTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->createFoos();
    }

    /**
     * @param array<int,string> $expected
     */
    #[DataProvider('getSortsProvider')]
    public function testShouldSortInAscendingOrder(
        SortOrder $sortOrder,
        array $expected
    ): void {
        $query = new FooQuery();
        $query->sortBy('name', $sortOrder);

        $foos = Foo::query()
            ->configureForQuery($query)
            ->get();

        self::assertCount(5, $foos);
        foreach ($expected as $key => $name) {
            $foo = $foos[$key];
            self::assertInstanceOf(Foo::class, $foo);
            self::assertSame($name, $foo->name);
        }
    }

    private function createFoos(): void
    {
        FooFactory::new()
            ->createMany([
                ['name' => 'Carol'],
                ['name' => 'Alice'],
                ['name' => 'Eve'],
                ['name' => 'Oscar'],
                ['name' => 'Dave'],
            ]);
    }

    public static function getSortsProvider(): Generator
    {
        yield 'Ascending' => [
            SortOrder::Ascending,
            ['Alice', 'Carol', 'Dave', 'Eve', 'Oscar']
        ];
        yield 'Descending' => [
            SortOrder::Descending,
            ['Oscar', 'Eve', 'Dave', 'Carol', 'Alice']
        ];
    }
}
