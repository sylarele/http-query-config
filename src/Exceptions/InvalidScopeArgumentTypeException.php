<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Exceptions;

use Illuminate\Database\Eloquent\Model;
use LogicException;
use Sylarele\HttpQueryConfig\Query\Scope;
use Sylarele\HttpQueryConfig\Query\ScopeArgument;

class InvalidScopeArgumentTypeException extends LogicException
{
    public function __construct(
        public readonly Model $model,
        public readonly Scope $scope,
        public readonly ScopeArgument $argument,
    ) {
        $class = class_basename($model);

        parent::__construct(
            sprintf('Type of parameter `%s` of scope filter `%s` ', $argument->getName(), $scope->getName())
            .sprintf('on model `%s` is either too intricate or missing. You may need to use a ', $class)
            .'transformer or custom validation.',
        );
    }
}
