<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;
use Sylarele\HttpQueryConfig\Tests\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class TestUnitTest extends TestBuilder
{
    #[TestDox('Unit Testing architecture rules')]
    public function testUnitTesting(): void
    {
        $this
            ->allClasses()
            ->fromDir('tests/Unit')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeClasses()
                    ->toBeFinal()
                    ->toHaveSuffix('Test')
                    ->or(
                        static fn (Expr $expr): Expr => $expr
                            ->toExtend(TestCase::class)
                            ->toExtend(PhpUnitTestCase::class)
                    )
                    ->dependsOnlyOnUseTrait(RefreshDatabase::class)
            );
    }
}
