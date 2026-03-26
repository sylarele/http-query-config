<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Tests\Architecture;

use Illuminate\Contracts\Validation\ValidationRule;
use StructuraPhp\Structura\Attributes\TestDox;
use StructuraPhp\Structura\Expr;
use StructuraPhp\Structura\Testing\TestBuilder;

final class TestRule extends TestBuilder
{
    #[TestDox('Validation rules architecture rules')]
    public function testValidationRule(): void
    {
        $this
            ->allClasses()
            ->fromDir('src/Rules')
            ->should(
                static fn (Expr $expr): Expr => $expr
                    ->toUseStrictTypes()
                    ->toBeClasses()
                    ->toBeFinal()
                    ->toImplement(ValidationRule::class)
                    ->toExtendsNothing()
                    ->toNotUseTrait()
            );
    }
}
