<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Override;
use Stringable;
use Sylarele\HttpQueryConfig\Contracts\QueryFilter;
use Sylarele\HttpQueryConfig\Enums\FilterType;
use Sylarele\HttpQueryConfig\Rules\In;

/**
 * Configures a simple query filter, for a single field.
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

    /**
     * @param  Model  $model  the model linked to the query
     * @param  string  $name   the name of the filter on the query
     * @param  Closure  $mutate internal, used to transform the filter into a scope if scope() is called
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
     * Transforms the filter into a scope filter.
     *
     * @param  string|null  $scopeName the name of the scope on the model, if different from the filter name
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
     * @return array<string, array<int,string|ValidationRule|Stringable>> the validation rules for the filter
     */
    #[Override]
    public function getValidation(): array
    {
        return [
            'value' => ['nullable', ...$this->getType()->getValueValidation()],
            'mode' => ['nullable', Rule::in($this->getType()->getModes(), false)],
            'not' => ['nullable', 'boolean'],
        ];
    }

    #[Override]
    public function lock(): void
    {
        // nothing
    }
}
