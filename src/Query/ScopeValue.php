<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use RuntimeException;
use WeakReference;

/**
 * A scope value on a query.
 */
readonly class ScopeValue
{
    /** @var array<int,ScopeArgumentValue> */
    protected array $arguments;

    public function __construct(
        protected WeakReference $query,
        protected Scope $scope,
    ) {
        $this->arguments = array_map(
            static fn (ScopeArgument $argument): ScopeArgumentValue => new ScopeArgumentValue(
                query: $query,
                argument: $argument,
            ),
            $scope->getArguments(),
        );
    }

    /**
     * @return string the name of the scope
     */
    public function getName(): string
    {
        return $this->scope->getName();
    }

    /**
     * @return string the Builder name of the scope
     */
    public function getScopeName(): string
    {
        return $this->scope->getScopeName();
    }

    /**
     * @return array<int,ScopeArgumentValue> the arguments of the scope
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param string $name the name of the argument
     * @return ScopeArgumentValue the argument
     *
     * @throws RuntimeException
     */
    public function getArgument(string $name): ScopeArgumentValue
    {
        foreach ($this->arguments as $argument) {
            if ($argument->getName() === $name) {
                return $argument;
            }
        }

        throw new RuntimeException(\sprintf('Argument %s not found', $name));
    }

    /**
     * Sets the value of an argument.
     *
     * @throws RuntimeException
     */
    public function setArgument(string $name, mixed $value): static
    {
        $this->getArgument($name)->set($value);

        return $this;
    }

    /**
     * @return array<string,mixed> the parameters map for the scope
     */
    public function getArgumentsMap(): array
    {
        $result = [];

        foreach ($this->arguments as $argument) {
            $value = $argument->getValue();

            if ($value !== null) {
                $result[$argument->getParameterName()] = $value;
            }
        }

        return $result;
    }
}
