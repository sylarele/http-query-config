<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;
use Sylarele\HttpQueryConfig\Contracts\Transformer;

final class TestTransformer extends TestBuilder
{
    #[TestDox('Transformer architecture rules')]
    public function testTransformer(): void
    {
        $this
            ->allClasses()
            ->fromDir('src/Transformers')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeClasses()
                    ->toHaveSuffix('Transformer')
                    ->toImplement(Transformer::class)
                    ->toExtendsNothing()
                    ->toNotUseTrait()
            );
    }
}
