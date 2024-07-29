<?php

declare(strict_types=1);

namespace Tests\Sylarele\HttpQueryConfig\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Sylarele\HttpQueryConfig\TestCase;
use Workbench\Database\Factories\BarFactory;
use Workbench\Database\Factories\FooFactory;

class WithQueryTest extends TestCase
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
        $this
            ->getJson(
                route(
                    'foos.index',
                    [
                        'with' => ['bars'],
                    ]
                )
            )
            ->assertOk()
            ->assertJsonCount(1, 'data.0.bars')
            ->assertJsonPath('data.0.bars.0.name', 'Antoine');
        ;
    }

    private function createFoos(): void
    {
        FooFactory::new()
            ->has(BarFactory::new(['name' => 'Antoine']))
            ->createOne(['name' => 'Carol']);
    }
}
