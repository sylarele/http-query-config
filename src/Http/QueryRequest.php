<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Http;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\In;
use RuntimeException;
use Sylarele\HttpQueryConfig\Contracts\QueryFilter;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Sylarele\HttpQueryConfig\Enums\FilterType;
use Sylarele\HttpQueryConfig\Enums\PaginationMode;
use Sylarele\HttpQueryConfig\Enums\SortOrder;
use Sylarele\HttpQueryConfig\Query\Filter;
use Sylarele\HttpQueryConfig\Query\Pagination\CursorPagination;
use Sylarele\HttpQueryConfig\Query\Pagination\NoPagination;
use Sylarele\HttpQueryConfig\Query\Pagination\OffsetPagination;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Query\QueryConfig;
use Sylarele\HttpQueryConfig\Query\Relationship;
use Sylarele\HttpQueryConfig\Query\Scope;

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
                $rules[\sprintf('%s.%s', $name, $key)] = $value;
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
            $rules['sortBy'] = ['sometimes', new In($sorts, false)];
            $rules['sortOrder'] = ['sometimes', new Enum(SortOrder::class)];
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
     * @return TModelClass
     */
    public function toQuery(): Query
    {
        $filters = $this->config->getFilters();
        $instance = $this->instanciateQuery();

        foreach ($filters as $filter) {
            // Applies filters and scopes to the query.
            if ($filter instanceof Filter) {
                $this->applyFilterToQuery($filter, $instance);
            } elseif ($filter instanceof Scope) {
                $this->applyScopeToQuery($filter, $instance);
            }
        }

        $this->applyPagination($instance);
        $this->applyRelationships($instance);
        $this->applySort($instance);
        $this->applyFieldsOnly($instance);

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
     *
     * @return TModelClass
     */
    protected function instanciateQuery(): Query
    {
        $class = $this->getQuery();

        if (!is_subclass_of($class, Query::class)) {
            throw new RuntimeException(
                \sprintf('Given query class `%s` does not extend Query.', $class)
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
            FilterType::Boolean => $value === 'true' || $value,
            FilterType::Array => (array) $value,
        };
    }

    protected function applyFilterToQuery(Filter $filter, Query $instance): void
    {
        $name = $filter->getName();

        /** @var null|array<scalar>|float|int|string $value */
        $value = $this->input($name . '.value');

        $mode = FilterMode::tryFrom($this->string($name . '.mode')->value())
            ?? $filter->getType()->getDefaultMode();

        $value = $this->parseFilterValue($filter, $value);

        if ($value === null) {
            return;
        }

        $instance->filter(
            filter: $filter,
            mode: $mode,
            value: $value,
            not: $this->boolean($name . '.not'),
        );
    }

    protected function applyScopeToQuery(Scope $scope, Query $instance): void
    {
        if (!$this->has($scope->getName())) {
            return;
        }

        $scope = $instance->scope($scope);

        foreach ($scope->getArguments() as $argument) {
            $value = $this->input(\sprintf('%s.%s', $scope->getName(), $argument->getName()));

            if ($value !== null) {
                $argument->set($value);
            }
        }
    }

    protected function applyPagination(Query $instance): void
    {
        $pagination = $this->config->getPagination();

        $paginationMode = $this->enum('pagination', PaginationMode::class)
            ?? PaginationMode::default();
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

    protected function applyRelationships(Query $instance): void
    {
        $relationships = $this->config->getRelationships();
        /** @var array<int,string> $withs */
        $withs = $this->input('with', []);

        // Applies relationships to the query.
        foreach ($relationships as $relationship) {
            if (\array_intersect(
                [$relationship->getName(), $relationship->getRelation()],
                $withs
            ) !== []) {
                $instance->load($relationship);
            }
        }
    }

    protected function applySort(Query $instance): void
    {
        $sortBy = $this->string('sortBy')->value();
        $sortOrder = $this->enum('sortOrder', SortOrder::class)
            ?? SortOrder::default();

        // Applies sorts to the query.
        if ($sortBy !== '') {
            $instance->sortBy(
                sort: $sortBy,
                order: $sortOrder,
            );
        }
    }

    protected function applyFieldsOnly(Query $instance): void
    {
        /** @var array<int,string> $fieldsOnly */
        $fieldsOnly = $this->input('only', []);

        foreach ($fieldsOnly as $fieldOnly) {
            $instance->fieldsOnly($fieldOnly);
        }
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
