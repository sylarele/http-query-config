<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

readonly class LimitValue
{
    public function __construct(
        protected ?int $value,
    ) {
    }

    public function getValue(): ?int
    {
        return $this->value;
    }
}
