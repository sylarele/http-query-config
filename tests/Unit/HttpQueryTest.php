<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\App\Models\Foo;
use Workbench\Database\Factories\FooFactory;

class HttpQueryTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        FooFactory::new()->createMany(10);
    }

    public function testIndex(): void
    {
        $foos = Foo::query()->get();

        //var_dump($foos);
        self::assertCount(10, $foos);
    }
}
