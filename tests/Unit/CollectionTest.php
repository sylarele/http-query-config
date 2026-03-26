<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Sylarele\HttpQueryConfig\Collections\CursorPaginator;
use Sylarele\HttpQueryConfig\Collections\EloquentCollection;
use Sylarele\HttpQueryConfig\Collections\LengthAwarePaginator;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;
use Sylarele\HttpQueryConfig\Query\Pagination\CursorPagination;
use Sylarele\HttpQueryConfig\Query\Pagination\NoPagination;
use Sylarele\HttpQueryConfig\Query\Pagination\OffsetPagination;
use Sylarele\HttpQueryConfig\Tests\TestCase;
use Workbench\App\Models\Foo;
use Workbench\App\Queries\FooQuery;
use Workbench\Database\Factories\FooFactory;

final class CollectionTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->createFoos();
    }

    public function testShouldOffsetPagination(): void
    {
        $query = new FooQuery();
        $query->paginate(new OffsetPagination(
            page: 0,
            limit: 5
        ));

        /** @var QueryResult<Foo> $foos */
        $foos = Foo::query()->paginateForQuery($query);

        $foos->loadMissing('bars');

        self::assertInstanceOf(LengthAwarePaginator::class, $foos);
        self::assertCount(5, $foos);
    }

    public function testShouldCursorPagination(): void
    {
        $query = new FooQuery();
        $query->paginate(new CursorPagination(
            cursor: '',
            limit: 0
        ));

        /** @var QueryResult<Foo> $foos */
        $foos = Foo::query()->paginateForQuery($query);

        $foos->loadMissing('bars');

        self::assertInstanceOf(CursorPaginator::class, $foos);
        self::assertCount(5, $foos);
    }

    public function testShouldNoPagination(): void
    {
        $query = new FooQuery();
        $query->paginate(new NoPagination());

        /** @var QueryResult<Foo> $foos */
        $foos = Foo::query()->paginateForQuery($query);

        $foos->loadMissing('bars');

        self::assertInstanceOf(EloquentCollection::class, $foos);
        self::assertCount(5, $foos);
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
