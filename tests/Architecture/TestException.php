<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use InvalidArgumentException;
use LogicException;
use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;

final class TestException extends TestBuilder
{
    #[TestDox('Exception architecture tests')]
    public function testException(): void
    {
        $this
            ->allClasses()
            ->fromDir('src/Exceptions')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeClasses()
                    ->toHaveSuffix('Exception')
                    ->or(
                        fn (Expr $expr): Expr => $expr
                            ->toExtend(LogicException::class)
                            ->toExtend(InvalidArgumentException::class)
                    )
                    ->toNotUseTrait()
                    ->toNotHavePublicConstant()
            );
    }
}
