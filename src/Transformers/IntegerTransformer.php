<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Transformers;

use Illuminate\Support\Carbon;
use Override;
use Sylarele\HttpQueryConfig\Contracts\Transformer;
use Sylarele\HttpQueryConfig\Exceptions\InvalidTransformerArgumentTypeException;

class IntegerTransformer implements Transformer
{
    #[Override]
    public function transform(array|string $value): int
    {
        return \is_string($value) && \is_numeric($value)
            ? (int) $value
            : throw new InvalidTransformerArgumentTypeException();
    }
}
