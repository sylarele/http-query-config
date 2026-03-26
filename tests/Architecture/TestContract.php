<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;

final class TestContract extends TestBuilder
{
    #[TestDox('Contract architecture tests')]
    public function testContract(): void
    {
        $this
            ->allClasses()
            ->fromDir('src/Contracts')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeInterfaces()
                    ->toNotUseTrait()
                    ->toNotHavePublicConstant()
            );
    }
}
