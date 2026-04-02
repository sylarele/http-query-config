<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;

final class TestCollection extends TestBuilder
{
    #[TestDox('Collection architecture tests')]
    public function testCollection(): void
    {
        $this
            ->allClasses()
            ->fromDir('src/Collections')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeClasses()
                    ->toImplement(QueryResult::class)
                    ->toNotUseTrait()
                    ->toNotHavePublicConstant()
            );
    }
}
