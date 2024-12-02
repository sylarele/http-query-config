<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Contracts;

interface Transformer
{
    /**
     * @param string|array<int|string,string>|string $value
     */
    public function transform(array|string $value): mixed;
}
