<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Sylarele\HttpQueryConfig\Enums\FilterType;

/**
 * A filter value on a query.
 */
class FilterValue
{
    public function __construct(
        protected readonly Filter $filter,
        protected readonly FilterMode $mode,
        protected readonly mixed $value,
        protected readonly bool $not = false,
    ) {
    }

    /**
     * @return string the name of the filter
     */
    public function getName(): string
    {
        return $this->filter->getName();
    }

    /**
     * @return string the database field name of the filter
     */
    public function getField(): string
    {
        return $this->filter->getField();
    }

    /**
     * @return FilterType the type of the filter
     */
    public function getType(): FilterType
    {
        return $this->filter->getType();
    }

    /**
     * @return FilterMode the operator used to filter
     */
    public function getMode(): FilterMode
    {
        return $this->mode;
    }

    /**
     * @return mixed the value to filter by
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return bool whether the filter is negated
     */
    public function isNot(): bool
    {
        return $this->not;
    }

    /**
     * @return bool whether the filter is a dummy filter
     */
    public function isDummy(): bool
    {
        return $this->filter->isDummy();
    }

    /**
     * @return string the SQL operator used to filter, depending on whether to negate it or not
     */
    public function getEloquentBoolean(): string
    {
        return $this->not ? 'and not' : 'and';
    }
}
