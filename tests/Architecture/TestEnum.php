<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;

final class TestEnum extends TestBuilder
{
    #[TestDox('Enum architecture tests')]
    public function testEnum(): void
    {
        $this
            ->allClasses()
            ->fromDir('src/Enums')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeEnums()
                    ->toNotUseTrait()
                    ->toNotHavePublicConstant()
            );
    }
}
