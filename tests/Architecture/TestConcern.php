<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;

final class TestConcern extends TestBuilder
{
    #[TestDox('Trait architecture tests')]
    public function testTrait(): void
    {
        $this
            ->allClasses()
            ->fromDir('src/Concerns')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeTraits()
                    ->toNotUseTrait()
                    ->toNotHavePublicConstant()
            );
    }
}
