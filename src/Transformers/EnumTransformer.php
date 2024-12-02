<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Transformer;

use BackedEnum;
use InvalidArgumentException;
use Override;
use Sylarele\HttpQueryConfig\Contracts\Transformer;
use Sylarele\HttpQueryConfig\Exceptions\InvalidTransformerArgumentTypeException;
use UnitEnum;

class EnumTransformer implements Transformer
{
    /**
     * @param class-string<UnitEnum> $enumClass
     */
    public function __construct(private string $enumClass)
    {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException(
                \sprintf("enum class '%s' does not exist", $enumClass)
            );
        }
    }

    #[Override]
    public function transform(array|string $value): BackedEnum
    {
        try {
            if (!method_exists($this->enumClass, 'tryFrom')) {
                throw new InvalidArgumentException(
                    \sprintf("enum class '%s' does not have method tryFrom()", $this->enumClass)
                );
            }

            return $this->enumClass::tryFrom($value);
        } catch (InvalidArgumentException) {
            throw new InvalidTransformerArgumentTypeException();
        }
    }
}
