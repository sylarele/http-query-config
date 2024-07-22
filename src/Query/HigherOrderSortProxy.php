<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

/**
 * Applies config to multiple sorts at once.
 */
class HigherOrderSortProxy
{
    /**
     * @param array<int,Sort> $sorts
     */
    public function __construct(
        protected array $sorts,
    ) {
    }

    /**
     * Sets the database field to sort by.
     */
    public function field(string $field): static
    {
        $this->sorts = array_map(
            static fn (Sort $sort): Sort => $sort->field($field),
            $this->sorts,
        );

        return $this;
    }
}
