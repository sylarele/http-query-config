<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Concerns;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use InvalidArgumentException;
use Nette\NotImplementedException;
use Sylarele\HttpQueryConfig\Contracts\QueryResult;
use Sylarele\HttpQueryConfig\Enums\FilterMode;
use Sylarele\HttpQueryConfig\Enums\FilterType;
use Sylarele\HttpQueryConfig\Query\FilterValue;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Query\Relationship;
use Sylarele\HttpQueryConfig\Query\RelationshipValue;
use Sylarele\HttpQueryConfig\Query\ScopeValue;

trait HttpBuilder
{
    /**
     * Apply the query filters, relationships and sorts.
     */
    public function configureForQuery(?Query $query): static
    {
        if (!$query instanceof Query) {
            return $this;
        }

        $result = $this;

        foreach ($query->getFilters() as $filter) {
            if ($filter->isDummy()) {
                continue;
            }

            $result = match ($filter->getType()) {
                FilterType::String => $this->applyStringFilter($filter),
                FilterType::Integer, FilterType::Float => $this->applyNumberFilter($filter),
                FilterType::Date, FilterType::DateTime => $this->applyDateTimeFilter($filter),
                FilterType::Boolean => $this->applyBooleanFilter($filter),
                FilterType::Array => $this->applyArrayFilter($filter),
            };
        }

        foreach ($query->getScopes() as $scope) {
            $this->applyScope($query, $scope);
        }

        foreach ($query->getRelationships() as $relation) {
            $result = $this->applyRelationship($query, $relation);
        }

        foreach ($query->getSorts() as $sort) {
            $result = $result->orderBy($sort->getField(), $sort->getOrder()->value);
        }

        if ($query->shouldDD()) {
            dd(
                $result->getConnection()->getDatabaseName(),
                $result->toSql(),
                $result->getBindings(),
            );
        }

        return $result;
    }

    /**
     * @deprecated use configureForQuery instead
     */
    public function forQuery(?Query $query): static
    {
        return $this->configureForQuery($query);
    }

    /**
     * Returns the paginated results, depending on the query pagination.
     */
    public function paginateForQuery(Query $query): QueryResult
    {
        return $query->getPagination()->handleQuery($this);
    }


    /**
     * Applies a scope filter to the query, injecting its dependencies.
     */
    protected function applyScope(Query $query, ScopeValue $scope): static
    {
        $scope->getScopeName();

        return $this;
    }

    /**
     * Applies a relationship to the query, using the `with()` method.
     * Also applies any scopes to the relationship.
     */
    protected function applyRelationship(Query $query, RelationshipValue $relation): static
    {
        $builder = $this->with(
            $relation->getRelation(),
            static function (Relation $builder) use ($relation): Relation {
                if ($relation->getScopes() === []) {
                    return $builder;
                }

                foreach ($relation->getScopes() as $scope) {
                    if (!$builder instanceof MorphTo) {
                        app()->call([$builder->getQuery(), $scope]);

                        continue;
                    }

                    $types = $builder->getQuery()->getModel()->getCasts()[$builder->getMorphType()] ?? null;

                    if (!is_subclass_of($types, BackedEnum::class)) {
                        throw new InvalidArgumentException(sprintf(
                            'The model %s does not have an enum cast for the morph type %s',
                            class_basename($builder->getQuery()->getModel()),
                            $builder->getMorphType(),
                        ));
                    }
                }

                return $builder;
            }
        );

        $dotPosition = strrpos($relation->getName(), '.');

        if ($dotPosition !== false) {
            $subrelation = $query->getConfig()->getRelationship(
                relationship: substr($relation->getName(), 0, $dotPosition),
            );

            if ($subrelation instanceof Relationship) {
                $builder = $this->applyRelationship($query, new RelationshipValue(
                    relationship: $subrelation,
                ));
            }
        }

        return $builder;
    }

    protected function applyStringFilter(FilterValue $filter): static
    {
        return match ($filter->getMode()) {
            FilterMode::Equals => $this->where(
                column: $filter->getField(),
                operator: '=',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::Contains => $this->where(
                column: $filter->getField(),
                operator: 'like',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),
            default => throw new NotImplementedException('Invalid filter mode'),
        };
    }

    protected function applyNumberFilter(FilterValue $filter): static
    {
        return match ($filter->getMode()) {
            FilterMode::Equals => $this->where(
                column: $filter->getField(),
                operator: '=',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::GreaterThan => $this->where(
                column: $filter->getField(),
                operator: '>',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::GreaterThanOrEqual => $this->where(
                column: $filter->getField(),
                operator: '>=',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::LessThan => $this->where(
                column: $filter->getField(),
                operator: '<',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::LessThanOrEqual => $this->where(
                column: $filter->getField(),
                operator: '<=',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),
            default => throw new NotImplementedException('Invalid filter mode'),
        };
    }

    protected function applyDateTimeFilter(FilterValue $filter): static
    {
        return match ($filter->getMode()) {
            FilterMode::Equals => $this->where(
                column: $filter->getField(),
                operator: '=',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::GreaterThan => $this->where(
                column: $filter->getField(),
                operator: '>',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::GreaterThanOrEqual => $this->where(
                column: $filter->getField(),
                operator: '>=',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::LessThan => $this->where(
                column: $filter->getField(),
                operator: '<',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),

            FilterMode::LessThanOrEqual => $this->where(
                column: $filter->getField(),
                operator: '<=',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),
            default => throw new NotImplementedException('Invalid filter mode'),
        };
    }

    protected function applyBooleanFilter(FilterValue $filter): static
    {
        return match ($filter->getMode()) {
            FilterMode::Equals => $this->where(
                column: $filter->getField(),
                operator: '=',
                value: $filter->getValue(),
                boolean: $filter->getEloquentBoolean(),
            ),
            default => throw new NotImplementedException('Invalid filter mode'),
        };
    }

    protected function applyArrayFilter(FilterValue $filter): static
    {
        return match ($filter->getMode()) {
            FilterMode::In => $this->whereIn($filter->getField(), $filter->getValue()),
            default => throw new NotImplementedException('Invalid filter mode'),
        };
    }
}