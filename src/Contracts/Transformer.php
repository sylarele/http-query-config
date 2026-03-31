<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Contracts;

/**
 * @phpstan-type TransformableData string|array<array-key, string>
 */
interface Transformer
{
    /**
     * @param array<array-key, array<array-key, TransformableData>|TransformableData>|TransformableData $value
     */
    public function transform(array|string $value): mixed;
}
