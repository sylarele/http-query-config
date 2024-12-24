<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use RuntimeException;
use Sylarele\HttpQueryConfig\Contracts\QueryFilter;
use Sylarele\HttpQueryConfig\Enums\SortOrder;
use Sylarele\HttpQueryConfig\Exceptions\QueryConfigLockedException;

class QueryConfig
{
    protected bool $locked = false;

    /** @var array<int,QueryFilter> the configured filters and scopes */
    protected array $filters = [];

    /** @var array<int,Sort> the configured sorts */
    protected array $sorts = [];

    /** @var array<int, string> */
    protected array $fieldsOnly = [];

    /** @var Pagination the configured pagination */
    protected Pagination $pagination;

    /** @var array<int,SortValue> the default sorts */
    protected array $defaultSorts = [];

    /** @var array<int,Relationship> the configured relationships */
    protected array $relationships = [];

    public function __construct(
        protected readonly Model $model,
    ) {
        $this->pagination = new Pagination();
    }

    /**
     * Adds a filter to the config.
     * Can be transformed into a scope by chaining the scope() method.
     *
     * @param  string  $name the query name of the filter
     */
    public function filter(string $name): Filter
    {
        if ($this->locked) {
            throw new QueryConfigLockedException();
        }

        return $this->filters[] = new Filter(
            model: $this->model,
            name: $name,
            mutate: $this->replace(...),
        );
    }

    /**
     * Adds multiple filters to the config at once.
     */
    public function filters(string ...$names): HigherOrderFilterProxy
    {
        return new HigherOrderFilterProxy(
            array_map($this->filter(...), array_values($names))
        );
    }

    /**
     * Allows configuring the pagination.
     */
    public function pagination(): Pagination
    {
        if ($this->locked) {
            throw new QueryConfigLockedException();
        }

        return $this->pagination;
    }

    /**
     * Adds a relationship to the config.
     *
     * @param  string  $relation the query name of the relationship
     */
    public function with(string $relation): Relationship
    {
        if ($this->locked) {
            throw new QueryConfigLockedException();
        }

        return $this->relationships[] = new Relationship($relation);
    }

    /**
     * Adds multiple relationships to the config at once.
     */
    public function withMany(string ...$relations): HigherOrderRelationProxy
    {
        /** @var array<int,Relationship> $relationship */
        $relationship = array_map($this->with(...), $relations);

        return new HigherOrderRelationProxy($relationship);
    }

    /**
     * Adds a sort option to the config.
     *
     * @param  string  $name the query name of the sort
     */
    public function sort(string $name): Sort
    {
        if ($this->locked) {
            throw new QueryConfigLockedException();
        }

        return $this->sorts[] = new Sort($this, $name);
    }

    /**
     * Adds multiple sort options to the config at once.
     */
    public function sorts(string ...$names): HigherOrderSortProxy
    {
        /** @var array<int, Sort> $sorts */
        $sorts = array_map($this->sort(...), $names);

        return new HigherOrderSortProxy($sorts);
    }

    /**
     * @param array<int,string> $names
     */
    public function only(array $names): void
    {
        $this->fieldsOnly = $names;
    }

    /**
     * Adds a default sort to the config.
     *
     * @param  Sort|string  $sort  the sort name to add
     * @param  SortOrder  $order the order to sort by
     */
    public function defaultSort(Sort|string $sort, SortOrder $order): static
    {
        if ($this->locked) {
            throw new QueryConfigLockedException();
        }

        $this->defaultSorts[] = new SortValue(
            sort: $this->getSortOrFail($sort),
            order: $order,
        );

        return $this;
    }

    /**
     * @return array<int,QueryFilter> the configured filters and scopes
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array<int,Relationship> the configured relationships
     */
    public function getRelationships(): array
    {
        return $this->relationships;
    }

    /**
     * @return array<int,Sort> the default sorts
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * @return array<int,string>
     */
    public function getFieldsOnly(): array
    {
        return $this->fieldsOnly;
    }

    /**
     * @return Pagination the pagination config
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @return array<int,SortValue> the default sorting
     */
    public function getDefaultSorts(): array
    {
        return $this->defaultSorts;
    }

    /**
     * Finds a filter by name.
     */
    public function getFilterOrFail(Filter|string $filter): Filter
    {
        if ($filter instanceof Filter) {
            $filter = $filter->getName();
        }

        $found = Arr::first(
            array: $this->filters,
            callback: static fn (QueryFilter $f): bool => $f->getName() === $filter
                && $f instanceof Filter,
        );

        if (!$found instanceof Filter) {
            throw new RuntimeException(
                \sprintf('No filter was registered with name `%s`.', $filter)
            );
        }

        return $found;
    }

    /**
     * Finds a scope by name.
     */
    public function getScopeOrFail(Scope|string $scope): Scope
    {
        if ($scope instanceof Scope) {
            $scope = $scope->getName();
        }

        $foundScope = Arr::first(
            array: $this->filters,
            callback: static fn (QueryFilter $f): bool => $f->getName() === $scope
                && $f instanceof Scope,
        );

        if (!$foundScope instanceof Scope) {
            throw new RuntimeException(
                \sprintf('No filter was registered with name `%s`.', $scope)
            );
        }

        return $foundScope;
    }

    /**
     * Finds a sort by name.
     */
    public function getSortOrFail(Sort|string $sort): Sort
    {
        if ($sort instanceof Sort) {
            if (!\in_array($sort, $this->sorts, true)) {
                throw new RuntimeException(
                    \sprintf('Given sort named `%s` is not registered on this query type.', $sort),
                );
            }

            return $sort;
        }

        $foundSort = Arr::first(
            array: $this->sorts,
            callback: static fn (Sort $s): bool => $s->getName() === $sort,
        );

        if (!$foundSort instanceof Sort) {
            throw new RuntimeException(
                \sprintf('No sort was registered with name `%s`.', $sort),
            );
        }

        return $foundSort;
    }

    /**
     * Finds a relationship by name.
     */
    public function getRelationship(Relationship|string $relationship): ?Relationship
    {
        if ($relationship instanceof Relationship) {
            if (!\in_array($relationship, $this->relationships)) {
                return null;
            }

            return $relationship;
        }

        $foundRelationship = Arr::first(
            array: $this->relationships,
            callback: static fn (Relationship $r): bool => $r->getName() === $relationship,
        );

        if (!$foundRelationship instanceof Relationship) {
            throw new RuntimeException('Invalid type for `$foundRelationship`');
        }

        return $foundRelationship;
    }

    /**
     * Finds a relationship by name.
     */
    public function getRelationshipOrFail(Relationship|string $relationship): Relationship
    {
        if ($relationship instanceof Relationship) {
            if (!\in_array($relationship, $this->relationships)) {
                throw new RuntimeException(
                    \sprintf(
                        'Given relationship named `%s` is not registered on this query type.',
                        $relationship->getName()
                    ),
                );
            }

            return $relationship;
        }

        $foundRelationship = Arr::first(
            array: $this->relationships,
            callback: static fn (Relationship $r): bool => $r->getName() === $relationship,
        );

        if (!$foundRelationship instanceof Relationship) {
            throw new RuntimeException('Invalid type for `$foundRelationship`');
        }

        return $foundRelationship;
    }

    /**
     * Locks the config to prevent further changes.
     */
    public function lock(): void
    {
        if ($this->locked) {
            return;
        }

        $this->locked = true;

        foreach ($this->filters as $filter) {
            $filter->lock();
        }
    }

    /**
     * Replaces a filter with another one.
     * Used by the ->filter()->sort() method.
     */
    protected function replace(QueryFilter $old, QueryFilter $new): void
    {
        $this->filters = array_map(
            static fn (QueryFilter $filter): QueryFilter => $filter === $old
                ? $new
                : $filter,
            $this->filters,
        );
    }
}
