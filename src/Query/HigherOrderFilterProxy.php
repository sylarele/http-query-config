<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Sylarele\HttpQueryConfig\Enums\FilterType;

/**
 * Applies config to multiple filters at once.
 */
class HigherOrderFilterProxy
{
    /**
     * The filters to apply config to.
     *
     * @param  array<int, Filter>  $filters
     */
    public function __construct(
        protected array $filters,
    ) {
    }

    /**
     * Sets the name of the database field to filter on.
     */
    public function field(string $field): static
    {
        $this->filters = array_map(
            static fn (Filter $filter): Filter => $filter->field($field),
            $this->filters,
        );

        return $this;
    }

    /**
     * Sets the type of filter.
     */
    public function type(FilterType $type): static
    {
        $this->filters = array_map(
            static fn (Filter $filter): Filter => $filter->type($type),
            $this->filters,
        );

        return $this;
    }

    /**
     * Sets the default value for the filter.
     */
    public function default(mixed $value): static
    {
        $this->filters = array_map(
            static fn (Filter $filter): Filter => $filter->default($value),
            $this->filters,
        );

        return $this;
    }

    /**
     * Transforms the filter into a scope filter (higher order proxy).
     */
    public function scope(): HigherOrderScopeProxy
    {
        return new HigherOrderScopeProxy(
            array_map(
                static fn (Filter $filter): Scope => $filter->scope(),
                $this->filters,
            ),
        );
    }
}
