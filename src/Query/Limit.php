<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

class Limit
{
    protected ?int $default = 50;

    protected ?int $max = 100;

    public function max(int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function unlimited(): static
    {
        $this->max = null;

        return $this;
    }

    public function default(int $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function defaultUnlimited(): static
    {
        $this->default = null;

        return $this;
    }

    public function getDefault(): ?int
    {
        return $this->default;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }
}
