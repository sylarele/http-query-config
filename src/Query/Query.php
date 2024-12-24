<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Illuminate\Database\Eloquent\Model;
use Sylarele\HttpQueryConfig\Contracts\QueryPagination;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Sylarele\HttpQueryConfig\Enums\SortOrder;
use WeakReference;

/**
 * A query for a model.
 * Allows for easy filtering, sorting, and pagination.
 * Can be configured inside the configure() method.
 */
abstract class Query
{
    /** @var QueryConfig the config for this query */
    protected readonly QueryConfig $config;

    /** @var Model the model instance linked to this query */
    protected readonly Model $instance;

    /** @var array<int,FilterValue> the filters to apply to the query */
    protected array $filters = [];

    /** @var array<int,ScopeValue> the scopes to apply to the query */
    protected array $scopes = [];

    /** @var array<int,RelationshipValue> the relationships to load on the query */
    protected array $relationships = [];

    /** @var array<int,SortValue> the sorts to apply to the query */
    protected array $sorts = [];

    /** @var array<int,string> */
    protected array $fieldsOnly = [];

    /** @var QueryPagination the pagination to apply to the query */
    protected QueryPagination $pagination;

    /** @var bool whether to dump the query and die */
    protected bool $dd = false;

    final public function __construct()
    {
        $modelName = $this->model();
        $model = new $modelName();

        \assert($model instanceof Model);

        $this->instance = $model;

        $this->config = new QueryConfig(
            model: $this->instance,
        );

        $this->configure($this->config);
        $this->config->lock();

        $this->pagination = $this->config->getPagination()->makeDefault();
    }

    /**
     * @return QueryConfig the config for this query
     */
    public function getConfig(): QueryConfig
    {
        return $this->config;
    }

    /**
     * @return Model the model instance linked to this query
     */
    public function getModelInstance(): Model
    {
        return $this->instance;
    }

    /**
     * Adds a filter to the query.
     *
     * @param  Filter|string  $filter the filter name
     * @param  FilterMode  $mode the "mode" to use (equals, contains, etc.)
     * @param  mixed  $value the value to filter by
     * @param  bool  $not whether to negate the filter
     */
    public function filter(
        Filter|string $filter,
        FilterMode $mode = FilterMode::Equals,
        mixed $value = true,
        bool $not = false,
    ): static {
        $this->filters[] = new FilterValue(
            filter: $this->config->getFilterOrFail($filter),
            mode: $mode,
            value: $value,
            not: $not,
        );

        return $this;
    }

    /**
     * Adds a scope to the query. Returns a ScopeValue object that can be used to set arguments.
     */
    public function scope(Scope|string $scope): ScopeValue
    {
        $result = new ScopeValue(
            query: WeakReference::create($this),
            scope: $this->config->getScopeOrFail($scope),
        );

        $this->scopes[] = $result;

        return $result;
    }

    public function fieldsOnly(string $fields): static
    {
        $this->fieldsOnly[] = $fields;

        return $this;
    }

    /**
     * Check if query has specific scope.
     */
    public function hasScope(Scope|string $scope): bool
    {
        $scope = $this->config->getScopeOrFail($scope);

        foreach ($this->scopes as $singleScope) {
            if ($singleScope->getName() === $scope->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if query has specific filter.
     */
    public function hasFilter(Filter|string $filter): bool
    {
        $filter = $this->config->getFilterOrFail($filter);

        foreach ($this->filters as $singleFilter) {
            if ($singleFilter->getName() === $filter->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     *  Get scope.
     */
    public function getScope(Scope|string $scope): ?ScopeValue
    {
        $scope = $this->config->getScopeOrFail($scope);

        foreach ($this->scopes as $singleScope) {
            if ($singleScope->getName() === $scope->getName()) {
                return $singleScope;
            }
        }

        return null;
    }

    /**
     * Adds a sort to the query.
     */
    public function sortBy(Sort|string $sort, SortOrder $order): static
    {
        $this->sorts[] = new SortValue(
            sort: $this->config->getSortOrFail($sort),
            order: $order,
        );

        return $this;
    }

    /**
     * Instructs the query to eager load a relationship.
     */
    public function load(Relationship|string $relationship): static
    {
        $this->relationships[] = new RelationshipValue(
            relationship: $this->config->getRelationshipOrFail($relationship)
        );

        return $this;
    }

    /**
     * @return array<int,FilterValue> the filters to apply to the query
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array<int,ScopeValue> the scopes to apply to the query
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @return array<int,RelationshipValue> the relationships to load on the query
     */
    public function getRelationships(): array
    {
        return $this->relationships;
    }

    /**
     * @return array<int,SortValue> the sorts to apply to the query
     */
    public function getSorts(): array
    {
        return $this->sorts ?: $this->config->getDefaultSorts();
    }

    /**
     * @return array<int,string>
     */
    public function getFieldsOnly(): array
    {
        return $this->fieldsOnly;
    }

    /**
     * Sets the pagination to use on the query.
     */
    public function paginate(QueryPagination $pagination): static
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * @return QueryPagination the pagination to use on the query
     */
    public function getPagination(): QueryPagination
    {
        return $this->pagination;
    }

    /**
     * Instructs the query to dump the query instead of executing it.
     */
    public function dd(): static
    {
        $this->dd = true;

        return $this;
    }

    /**
     * @return bool whether to dump the query and die instead of executing it
     */
    public function shouldDD(): bool
    {
        return $this->dd;
    }

    /**
     * @return string the model class linked to this query
     */
    abstract protected function model(): string;

    /**
     * Configures the query.
     */
    abstract protected function configure(QueryConfig $config): void;
}
