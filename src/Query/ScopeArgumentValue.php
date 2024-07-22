<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Closure;
use WeakReference;

/**
 * A scope argument value on a query.
 */
class ScopeArgumentValue
{
    /** @var mixed the value of the scope argument */
    protected mixed $value = null;

    /**
     * @param  WeakReference  $query    the Query instance this argument value belongs to (WeakReference to avoid circular references)
     * @param  ScopeArgument  $argument the argument config
     */
    public function __construct(
        protected readonly WeakReference $query,
        protected readonly ScopeArgument $argument,
    ) {
        if ($argument->getResolver() instanceof Closure) {
            $this->value = ($argument->getResolver())($query->get());
        }
    }

    /**
     * Sets the value of the scope argument.
     */
    public function set(mixed $value): static
    {
        if ($value !== null && $this->argument->getTransformer() instanceof Closure) {
            $value = ($this->argument->getTransformer())($value, $this->query->get());
        }

        $this->value = $value;

        return $this;
    }

    /**
     * @return string the name of the scope argument
     */
    public function getName(): string
    {
        return $this->argument->getName();
    }

    /**
     * @return string the scope parameter name
     */
    public function getParameterName(): string
    {
        return $this->argument->getParameterName();
    }

    /**
     * @return mixed the value of the scope argument
     */
    public function getValue(): mixed
    {
        return $this->value;
    }
}
