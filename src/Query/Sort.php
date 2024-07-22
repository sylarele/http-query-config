<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Override;
use Stringable;
use Sylarele\HttpQueryConfig\Enums\SortOrder;

/**
 * A sorting option for a query.
 */
class Sort implements Stringable
{
    /** @var string the database field to use */
    protected string $field;

    /**
     * @param  QueryConfig  $config the config to apply the default sort to
     * @param  string  $name   the sort name on the query
     */
    public function __construct(
        protected readonly QueryConfig $config,
        protected readonly string $name,
    ) {
        $this->field = $name;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Sets the database field to sort by.
     */
    public function field(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string the name of the sort on the query
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string the database field to sort by
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Sets this sort as the default sort for the query, using the given order.
     */
    public function asDefault(SortOrder $order): static
    {
        $this->config->defaultSort(
            sort: $this,
            order: $order,
        );

        return $this;
    }
}
