<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Transformers;

use Override;
use Sylarele\HttpQueryConfig\Contracts\Transformer;
use Sylarele\HttpQueryConfig\Exceptions\InvalidTransformerArgumentTypeException;

class FloatTransformer implements Transformer
{
    #[Override]
    public function transform(array|string $value): float
    {
        return \is_string($value) && is_numeric($value)
            ? (float) $value
            : throw new InvalidTransformerArgumentTypeException();
    }
}
