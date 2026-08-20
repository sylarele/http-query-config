<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Override;
use Sylarele\HttpQueryConfig\Contracts\QueryFilter;
use Sylarele\HttpQueryConfig\Enums\FilterType;

/**
 * Configures a simple query filter, for a single field.
 *
 * @phpstan-import-type ValidationRule from QueryFilter
 * @phpstan-import-type ValidationRules from QueryFilter
 *
 * @template TModel of Model
 * @template TBuilder of Builder
 *
 * @implements QueryFilter<TModel, TBuilder>
 */
class Filter implements QueryFilter
{
    /** @var string the database field to filter on */
    protected string $field;

    /** @var FilterType the type of filter (depends on the type of the field) */
    protected FilterType $type = FilterType::String;

    /** @var mixed the default value for the filter */
    protected mixed $default = null;

    /** @var bool whether the filter is a dummy filter (does not affect the query) */
    protected bool $dummy = false;

    /** @var ValidationRules the custom validation rules for the filter */
    protected array $validation = [];

    /**
     * @param TModel $model the model linked to the query
     * @param string $name the name of the filter on the query
     * @param Closure $mutate internal, used to transform the filter into a scope if scope() is called
     */
    public function __construct(
        protected readonly Model $model,
        protected readonly string $name,
        protected readonly Closure $mutate,
    ) {
        $this->field = $name;
    }

    /**
     * Sets the name of the database field to filter on.
     */
    public function field(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Sets the type of filter.
     *
     * @see FilterType
     */
    public function type(FilterType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Sets the default value for the filter.
     */
    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    /**
     * Sets custom validation rules for the value of the filter.
     * Replaces the rules inferred from the filter type.
     *
     * @param ValidationRule $rules
     */
    public function withValidation(array $rules): static
    {
        $this->validation['value'] = $rules;

        return $this;
    }

    /**
     * Adds custom validation rules for a sub-key of the value of the filter.
     * Mostly useful for array filters, using `*` as the sub-key.
     *
     * @param ValidationRule $rules
     */
    public function addedValidation(string $subKey, array $rules): static
    {
        $this->validation['value.'.$subKey] = $rules;

        return $this;
    }

    /**
     * Transforms the filter into a scope filter.
     *
     * @param string|null $scopeName the name of the scope on the model, if different from the filter name
     *
     * @see Scope
     */
    public function scope(?string $scopeName = null): Scope
    {
        $mutate = $this->mutate;

        $scope = new Scope(
            model: $this->model,
            name: $this->name,
            scopeName: $scopeName ?? $this->name,
        );

        $mutate($this, $scope);

        return $scope;
    }

    /**
     * @param Closure $callback (Closure(TBuilder<TModel>)): (Closure(mixed...): TBuilder<TModel>)))
     */
    public function scopeClosure(Closure $callback): Scope
    {
        $mutate = $this->mutate;

        $scope = new Scope(
            model: $this->model,
            name: $this->name,
            scopeName: $callback,
        );

        $mutate($this, $scope);

        return $scope;
    }

    /**
     * Marks the filter as a dummy filter (does not affect the query).
     */
    public function dummy(): static
    {
        $this->dummy = true;

        return $this;
    }

    /**
     * @return string the name of the filter
     */
    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string the name of the database field to filter on
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return FilterType the type of filter
     */
    public function getType(): FilterType
    {
        return $this->type;
    }

    /**
     * @return mixed the default value for the filter
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * @return bool whether the filter is a dummy filter
     */
    public function isDummy(): bool
    {
        return $this->dummy;
    }

    /**
     * @return ValidationRules the validation rules for the filter
     */
    #[Override]
    public function getValidation(): array
    {
        return [
            ...[
                'value' => ['nullable', ...$this->getType()->getValueValidation()],
                'not' => ['boolean'],
                'mode' => [Rule::in($this->getType()->getModes(), false)],
            ],
            ...$this->validation,
        ];
    }

    #[Override]
    public function lock(): void
    {
        // nothing
    }
}
