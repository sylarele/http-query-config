<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use Illuminate\Foundation\Testing\RefreshDatabase;
use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;
use Sylarele\HttpQueryConfig\Tests\TestCase;

final class TestFeatureTest extends TestBuilder
{
    #[TestDox('Feature Testing architecture rules')]
    public function testFeatureTesting(): void
    {
        $this
            ->allClasses()
            ->fromDir('tests/Feature')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeClasses()
                    ->toBeFinal()
                    ->toHaveSuffix('Test')
                    ->toExtend(TestCase::class)
                    ->toUseTrait(RefreshDatabase::class)
            );
    }
}
