<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Transformer;

use InvalidArgumentException;
use Override;
use Sylarele\HttpQueryConfig\Contracts\Transformer;
use Sylarele\HttpQueryConfig\Exceptions\InvalidTransformerArgumentTypeException;
use UnitEnum;

class EnumListTransformer implements Transformer
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

    /**
     * @return array<int,\BackedEnum>
     */
    #[Override]
    public function transform(array|string $value): array
    {
        if (\is_string($value)) {
            throw new InvalidTransformerArgumentTypeException();
        }

        if (!method_exists($this->enumClass, 'tryFrom')) {
            throw new InvalidTransformerArgumentTypeException(
                \sprintf("enum class '%s' does not have method tryFrom()", $this->enumClass)
            );
        }

        try {
            return array_values(
                array_map($this->enumClass::tryFrom(...), $value)
            );
        } catch (InvalidArgumentException) {
            throw new InvalidTransformerArgumentTypeException();
        }
    }
}
