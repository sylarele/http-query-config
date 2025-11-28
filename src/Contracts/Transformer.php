<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Contracts;

/**
 * @phpstan-type TransformableData string|array<array-key, string>
 */
interface Transformer
{
    /**
     * @param TransformableData|array<array-key, TransformableData|array<array-key, TransformableData>> $value
     */
    public function transform(array|string $value): mixed;
}
