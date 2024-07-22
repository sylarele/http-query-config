<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Closure;

/**
 * Applies config to multiple scopes at once.
 */
class HigherOrderScopeProxy
{
    /**
     * @param  array<int,Scope>  $scopes
     */
    public function __construct(
        protected array $scopes,
    ) {
    }

    /**
     * Adds a query argument to the scope.
     *
     * @param  string  $name   The query name of the argument
     * @param  Closure|null  $config Configures the argument. Accepts a ScopeArgument instance.
     */
    public function arg(string $name, ?Closure $config = null): static
    {
        foreach ($this->scopes as $scope) {
            $scope->arg($name, $config);
        }

        return $this;
    }
}
