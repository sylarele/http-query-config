<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Unit;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylarele\HttpQueryConfig\Concerns\HttpBuilder;
use Sylarele\HttpQueryConfig\Enums\SortOrder;
use Sylarele\HttpQueryConfig\Http\QueryRequest;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Tests\TestCase;
use Workbench\App\Models\Foo;
use Workbench\App\Queries\FooQuery;
use Workbench\Database\Factories\FooFactory;

/**
 * @internal
 */
#[CoversClass(HttpBuilder::class)]
#[CoversClass(Query::class)]
#[CoversClass(QueryRequest::class)]
final class SortQueryTest extends TestCase
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

    public static function getSortsProvider(): Generator
    {
        yield 'Ascending' => [
            SortOrder::Ascending,
            ['Alice', 'Carol', 'Dave', 'Eve', 'Oscar'],
        ];

        yield 'Descending' => [
            SortOrder::Descending,
            ['Oscar', 'Eve', 'Dave', 'Carol', 'Alice'],
        ];
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
}
