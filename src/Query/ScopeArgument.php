<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Closure;
use Illuminate\Database\Eloquent\Model;
use ReflectionNamedType;
use ReflectionParameter;
use Stringable;

/**
 * A query scope argument config.
 */
class ScopeArgument
{
    /** @var string the name of the scope parameter. Defaults to the name of the query argument */
    protected string $parameterName;

    /** @var Closure|null allows the resolving of default values for the scope parameter */
    protected ?Closure $resolver = null;

    /** @var Closure|null transforms the value passed to the query argument into the value passed to the scope parameter */
    protected ?Closure $transformer = null;

    /** @var array<int|string, array<int,string|Stringable>>|null the custom validation rules for the query argument */
    protected ?array $validation = null;

    /**
     * @param  string  $name the name of the scope argument
     */
    public function __construct(
        protected readonly string $name,
    ) {
        $this->parameterName = $name;
    }

    /**
     * Sets the scope parameter to binds this argument to.
     */
    public function for(string $parameterName): static
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    /**
     * Sets the resolver for the default value of the scope parameter.
     */
    public function using(Closure $resolver): static
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * Sets the transformer for this argument.
     */
    public function transform(Closure $transformer): static
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Sets custom validation rules for this argument.
     *
     * @param array<int|string, array<int, string|Stringable>> $rules
     */
    public function withValidation(array $rules): static
    {
        $this->validation = $rules;

        return $this;
    }

    /**
     * @return string the query argument name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string the name of the scope parameter
     */
    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    /**
     * @return Closure|null the resolver for the default value of the scope parameter
     */
    public function getResolver(): ?Closure
    {
        return $this->resolver;
    }

    /**
     * @return Closure|null the transformer for the value of the query argument
     */
    public function getTransformer(): ?Closure
    {
        return $this->transformer;
    }

    /**
     * @return array<int|string, array<int, string|Stringable>>|null the custom validation rules for the query argument
     */
    public function getValidation(): ?array
    {
        return $this->validation;
    }

    /**
     * Updates the transformer if needed.
     * Can add a layer if for instance the argument is a model.
     */
    public function resolveTransformer(ReflectionParameter $reflection): static
    {
        $type = $reflection->getType();

        if (! $type instanceof ReflectionNamedType) {
            return $this;
        }

        if ($type->isBuiltin()) {
            return $this;
        }

        $baseTransformer = $this->transformer;

        if (is_subclass_of($type->getName(), Model::class)) {
            // When the parameter is a model, we can resolve it from the database
            // This wraps the transformer into a new one that implicitly resolves the model instance
            $class = $type->getName();

            $this->transformer = static function (string $value, Query $query) use ($class, $baseTransformer, $type) {
                // If the parameter is nullable, we pass null if the model was not found
                $instance = $type->allowsNull()
                    ? $class::query()->find($value)
                    : $class::query()->findOrFail($value);

                if ($baseTransformer instanceof Closure) {
                    return $baseTransformer($instance, $query);
                }

                return $instance;
            };
        }

        return $this;
    }
}