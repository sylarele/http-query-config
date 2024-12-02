<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Transformers;

use BackedEnum;
use InvalidArgumentException;
use Override;
use Sylarele\HttpQueryConfig\Contracts\Transformer;
use Sylarele\HttpQueryConfig\Exceptions\InvalidTransformerArgumentTypeException;
use UnitEnum;
use ValueError;

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
        if (!method_exists($this->enumClass, 'from')) {
            throw new InvalidArgumentException(
                \sprintf("enum class '%s' does not have method from()", $this->enumClass)
            );
        }

        try {
            return $this->enumClass::from($value);
        } catch (ValueError) {
            throw new InvalidTransformerArgumentTypeException();
        }
    }
}
