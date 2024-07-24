<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Unit;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\App\Models\Foo;
use Workbench\App\Queries\FooQuery;
use Workbench\Database\Factories\FooFactory;

class FilterStringQueryTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->createFoos();
    }

    #[DataProvider('getFiltersProvider')]
    public function testShouldFilterString(
        FilterMode $filterMode,
        string $value,
        string $expected
    ): void {
        $query = new FooQuery();
        $query->filter('name', $filterMode, $value);

        $foos = Foo::query()
            ->configureForQuery($query)
            ->get();

        self::assertCount(1, $foos);
        $foo = $foos[0];
        self::assertInstanceOf(Foo::class, $foo);
        self::assertSame($expected, $foo->name);
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

    public static function getFiltersProvider(): Generator
    {
        yield 'Equals' => [FilterMode::Equals, 'Alice', 'Alice'];
        yield 'Contains' => [FilterMode::Contains, 'Ali', 'Alice'];
    }
}
