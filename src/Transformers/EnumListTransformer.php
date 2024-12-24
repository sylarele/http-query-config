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

class EnumListTransformer implements Transformer
{
    /**
     * @param class-string<BackedEnum> $enumClass
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
     * @return array<int,BackedEnum>
     */
    #[Override]
    public function transform(array|string $value): array
    {
        if (\is_string($value)) {
            throw new InvalidTransformerArgumentTypeException();
        }

        if (!method_exists($this->enumClass, 'from')) {
            throw new InvalidTransformerArgumentTypeException(
                \sprintf("enum class '%s' does not have method from()", $this->enumClass)
            );
        }

        try {
            /** @var array<int, BackedEnum> $list */
            $list = array_map($this->enumClass::from(...), $value);

            return array_values($list);
        } catch (ValueError) {
            throw new InvalidTransformerArgumentTypeException();
        }
    }
}
