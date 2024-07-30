<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Http;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\In;
use RuntimeException;
use Sylarele\HttpQueryConfig\Contracts\QueryFilter;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Sylarele\HttpQueryConfig\Enums\FilterType;
use Sylarele\HttpQueryConfig\Enums\PaginationMode;
use Sylarele\HttpQueryConfig\Enums\SortOrder;
use Sylarele\HttpQueryConfig\Query\Filter;
use Sylarele\HttpQueryConfig\Query\Pagination;
use Sylarele\HttpQueryConfig\Query\Pagination\CursorPagination;
use Sylarele\HttpQueryConfig\Query\Pagination\NoPagination;
use Sylarele\HttpQueryConfig\Query\Pagination\OffsetPagination;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Query\QueryConfig;
use Sylarele\HttpQueryConfig\Query\Relationship;
use Sylarele\HttpQueryConfig\Query\Scope;
use Sylarele\HttpQueryConfig\Query\Sort;

/**
 * Validates a query request.
 *
 * @template TModelClass of Query
 *
 * @phpstan-import-type ValidationRules from QueryFilter
 */
abstract class QueryRequest extends FormRequest
{
    protected QueryConfig $config;

    public function __construct()
    {
        parent::__construct();

        $this->config = $this->instanciateQuery()->getConfig();
    }

    /**
     * Returns the list of rules to validate the request.
     *
     * @return ValidationRules
     */
    public function rules(): array
    {
        $filters = $this->config->getFilters();
        $relationships = $this->config->getRelationships();
        $sorts = $this->config->getSorts();
        $fieldsOnly = $this->config->getFieldsOnly();

        $rules = [];

        // Adds filters rules
        foreach ($filters as $filter) {
            $name = $filter->getName();

            foreach ($filter->getValidation() as $key => $value) {
                $rules[sprintf('%s.%s', $name, $key)] = $value;
            }
        }

        if ($relationships !== []) {
            // Adds relationships rules
            $availableRelationships = array_map(
                callback: static fn (Relationship $r): string => $r->getName(),
                array: $relationships,
            );

            $rules['with'] = ['array'];
            $rules['with.*'] = ['string', 'nullable', new In($availableRelationships)];
        }

        if ($sorts !== []) {
            // Adds sorts rules
            $rules['sortBy'] = ['nullable', new In($sorts, false)];
            $rules['sortOrder'] = ['nullable', new In(SortOrder::cases(), false)];
        }

        if ($fieldsOnly !== []) {
            $rules['only'] = ['array'];
            $rules['only.*'] = ['string', 'nullable', new In($fieldsOnly)];
        }

        // Pagination rules
        return $this->addPaginationValidation($rules);
    }

    /**
     * Returns a Query instance built from the request.
     *
     */
    public function toQuery(): Query
    {
        /** @var array<string,mixed> $inputs */
        $inputs = $this->validated();
        $filters = $this->config->getFilters();
        $pagination = $this->config->getPagination();
        $instance = $this->instanciateQuery();

        foreach ($filters as $filter) {
            // Applies filters and scopes to the query.
            if ($filter instanceof Filter) {
                $this->applyFilterToQuery($filter, $instance);
            } elseif ($filter instanceof Scope) {
                $this->applyScopeToQuery($filter, $instance);
            }
        }

        /** @var array<int,string> $withs */
        $withs = data_get($inputs, 'with', []);
        $sortBy = data_get($inputs, 'sortBy');
        $sortOrder = data_get($inputs, 'sortOrder');
        /** @var array<int,string> $fieldsOnly */
        $fieldsOnly = data_get($inputs, 'only', []);

        $this->applyPagination($pagination, $instance);

        $relationships = $this->config->getRelationships();

        // Applies relationships to the query.
        foreach ($relationships as $relationship) {
            if (\in_array($relationship->getName(), $withs)) {
                $instance->load($relationship);
            }

            if (\in_array($relationship->getRelation(), $withs)) {
                $instance->load($relationship);
            }
        }

        // Applies sorts to the query.
        if (\is_string($sortBy) || $sortBy instanceof Sort) {
            $instance->sortBy(
                sort: $sortBy,
                order: \is_string($sortOrder)
                    ? SortOrder::from($sortOrder)
                    : SortOrder::default(),
            );
        }

        foreach ($fieldsOnly as $fieldOnly) {
            $instance->fieldsOnly($fieldOnly);
        }

        return $instance;
    }

    /**
     * Returns the query class to use.
     *
     * @return class-string<TModelClass>
     */
    abstract protected function getQuery(): string;

    /**
     * Instanciates the query class.
     */
    protected function instanciateQuery(): Query
    {
        $class = $this->getQuery();

        if (!is_subclass_of($class, Query::class)) {
            throw new RuntimeException(
                sprintf('Given query class `%s` does not extend Query.', $class)
            );
        }

        return new $class();
    }

    /**
     * Parses a given filter value depending on its type.
     * @param null|array<scalar>|float|int|string $value
     */
    protected function parseFilterValue(
        Filter $filter,
        null|array|float|int|string $value
    ): mixed {
        if ($value === null) {
            return $filter->getDefault();
        }

        /** @var string $timezone */
        $timezone = config('app.timezone');

        return match ($filter->getType()) {
            FilterType::String => $value,
            FilterType::Integer => (int) $value,
            FilterType::Float => (float) $value,
            FilterType::Date,
            FilterType::DateTime => Carbon::parse(\is_string($value) ? $value : null)
                ->timezone($timezone),
            FilterType::Boolean => $value === 'true' || (bool) $value,
            FilterType::Array => (array) $value,
        };
    }

    protected function applyFilterToQuery(Filter $filter, Query $instance): void
    {
        $input = $this->validated();
        $name = $filter->getName();

        /** @var string|null $value */
        $value = data_get($input, $name . '.value');

        $not = (bool) data_get($input, $name . '.not', false);

        $mode = data_get($input, $name . '.mode');

        $mode = \is_string($mode)
            ? FilterMode::from(strtolower($mode))
            : $filter->getType()->getDefaultMode();

        $value = $this->parseFilterValue(
            filter: $filter,
            value: $value,
        );

        if ($value === null) {
            return;
        }

        $instance->filter(
            filter: $filter,
            mode: $mode,
            value: $value,
            not: $not,
        );
    }

    protected function applyScopeToQuery(Scope $scope, Query $instance): void
    {
        $input = $this->validated();

        if (!data_get($input, $scope->getName())) {
            return;
        }

        $scope = $instance->scope($scope);

        foreach ($scope->getArguments() as $argument) {
            $value = data_get($input, sprintf('%s.%s', $scope->getName(), $argument->getName()));

            if ($value !== null) {
                $argument->set($value);
            }
        }
    }

    protected function applyPagination(Pagination $pagination, Query $instance): void
    {
        $paginationMode = $this->enum('pagination', PaginationMode::class)
            ?? PaginationMode::Offset;
        $limit = $this->integer('limit', $pagination->getDefaultLimit());
        $page = $this->integer('page', 1);
        $cursor = $this->has('cursor')
            ? $this->string('cursor')->value()
            : null;

        // Applies pagination to the query.
        $instance->paginate(
            match ($paginationMode) {
                PaginationMode::Offset => new OffsetPagination(
                    page: $page,
                    limit: $limit
                ),

                PaginationMode::Cursor => new CursorPagination(
                    cursor: $cursor,
                    limit: $limit
                ),

                PaginationMode::None => new NoPagination(),
            }
        );
    }

    /**
     * @param ValidationRules $rules
     *
     * @return ValidationRules
     */
    protected function addPaginationValidation(
        array $rules
    ): array {
        $pagination = $this->config->getPagination();

        $rules['pagination'] = [
            'sometimes',
            'string',
            new In($pagination->getAllowed()),
        ];

        $rules['limit'] = [
            'sometimes',
            'integer',
            'min:1',
            'max:' . $pagination->getMaxLimit(),
        ];

        $rules['page'] = [
            'sometimes',
            'integer',
            'min:1',
            'max:999999',
        ];

        $rules['cursor'] = [
            'sometimes',
            'string',
            'max:1024',
        ];

        return $rules;
    }
}
