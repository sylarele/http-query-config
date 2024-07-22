<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

class LimitValue
{
    public function __construct(
        protected readonly ?int $value,
    ) {
    }

    public function getValue(): ?int
    {
        return $this->value;
    }
}
