<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Transformers;

use Illuminate\Support\Carbon;
use Override;
use Sylarele\HttpQueryConfig\Contracts\Transformer;
use Sylarele\HttpQueryConfig\Exceptions\InvalidTransformerArgumentTypeException;

class CarbonTransformer implements Transformer
{
    #[Override]
    public function transform(array|string $value): Carbon
    {
        return \is_string($value)
            ? Carbon::parse($value)
            : throw new InvalidTransformerArgumentTypeException();
    }
}
