<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Override;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Sylarele\HttpQueryConfig\Contracts\QueryFilter;
use Sylarele\HttpQueryConfig\Exceptions\InvalidScopeArgumentTypeException;
use Sylarele\HttpQueryConfig\Exceptions\ScopeParameterNotFoundException;

/**
 * Similar to filters, but use a Builder scope instead of a database field.
 *
 * @phpstan-import-type ValidationRules from QueryFilter
 */
class Scope implements QueryFilter
{
    /** @var array<int,ScopeArgument> */
    protected array $arguments = [];

    /**
     * @param Model $model the model linked to the query
     * @param string $name the name of the filter on the query
     * @param string $scopeName the name of the Builder scope to call
     */
    public function __construct(
        protected readonly Model $model,
        protected readonly string $name,
        protected readonly string $scopeName,
    ) {
    }

    /**
     * Adds a query argument to the scope.
     *
     * @param string $name The query name of the argument
     * @param Closure(ScopeArgument):ScopeArgument|null $config Configures the argument. Accepts a
     * ScopeArgument instance.
     */
    public function arg(string $name, ?Closure $config = null): static
    {
        $argument = new ScopeArgument($name);

        $this->arguments[] = $argument;

        if ($config instanceof Closure) {
            $config($argument);
        }

        return $this;
    }

    /**
     * @return string the name of the scope on the query
     */
    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string the name of the scope on the model
     */
    public function getScopeName(): string
    {
        return $this->scopeName;
    }

    /**
     * @return array<int,ScopeArgument> the arguments for the scope
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Locks the scope.
     * Arguments transformers may be changed to account for implicit model binding.
     */
    #[Override]
    public function lock(): void
    {
        if ($this->arguments === []) {
            return;
        }

        $reflection = new ReflectionMethod($this->model->newQuery(), $this->scopeName);

        foreach ($this->arguments as $argument) {
            $parameter = $this->getArgumentParameter($reflection, $argument);

            $argument->resolveTransformer($parameter);
        }
    }

    /**
     * @return ValidationRules the validation rules for the scope
     */
    #[Override]
    public function getValidation(): array
    {
        $result = [];

        $reflection = new ReflectionMethod($this->model->newQuery(), $this->scopeName);

        foreach ($this->arguments as $argument) {
            $result[] = $argument->getValidation()
                ?? $this->guessArgumentValidation(
                    reflection: $reflection,
                    argument: $argument,
                );
        }

        return array_merge(...$result);
    }

    /**
     * Guesses a scope argument validation rules, using the reflection API.
     *
     * @return ValidationRules
     */
    protected function guessArgumentValidation(
        ReflectionMethod $reflection,
        ScopeArgument $argument,
    ): array {
        $parameter = $this->getArgumentParameter($reflection, $argument);
        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType || !$type->isBuiltin()) {
            throw new InvalidScopeArgumentTypeException(
                model: $this->model,
                scope: $this,
                argument: $argument,
            );
        }

        return [
            $argument->getName() => [
                $parameter->isOptional() || $type->allowsNull()
                    ? 'nullable'
                    : 'required_with:' . $this->getName(),
                $this->builtinTypeToValidation($argument, $type->getName()),
            ]
        ];
    }

    protected function getArgumentParameter(
        ReflectionMethod $reflection,
        ScopeArgument $argument,
    ): ReflectionParameter {
        $transformer = $argument->getTransformer();

        if ($transformer instanceof Closure) {
            $transformerReflection = new ReflectionFunction($transformer);

            $reflectionParameter = Arr::first($transformerReflection->getParameters());

            if (!$reflectionParameter instanceof ReflectionParameter) {
                throw new ScopeParameterNotFoundException(
                    model: $this->model,
                    scope: $this,
                    argument: $argument,
                );
            }

            return $reflectionParameter;
        }

        $result = Arr::first(
            array: $reflection->getParameters(),
            callback: static fn (
                ReflectionParameter $parameter
            ): bool => $parameter->getName() === $argument->getParameterName(),
        );

        if (!$result instanceof ReflectionParameter) {
            throw new ScopeParameterNotFoundException(
                model: $this->model,
                scope: $this,
                argument: $argument,
            );
        }

        return $result;
    }

    protected function builtinTypeToValidation(ScopeArgument $argument, string $builtin): string
    {
        return match ($builtin) {
            'int' => 'integer',
            'float' => 'numeric',
            'string' => 'string',
            'bool' => 'boolean',
            default => throw new InvalidScopeArgumentTypeException(
                model: $this->model,
                scope: $this,
                argument: $argument,
            ),
        };
    }
}
