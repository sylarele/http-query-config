<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Exceptions;

use Illuminate\Database\Eloquent\Model;
use LogicException;
use Sylarele\HttpQueryConfig\Query\Scope;
use Sylarele\HttpQueryConfig\Query\ScopeArgument;

class ScopeParameterNotFoundException extends LogicException
{
    public function __construct(
        public readonly Model $model,
        public readonly Scope $scope,
        public readonly ScopeArgument $argument,
    ) {
        $class = class_basename($model);

        parent::__construct(
            sprintf(
                'Unknown parameter `%s` for scope filter `%s` on model `%s`.',
                $argument->getName(),
                $scope->getName(),
                $class
            )
        );
    }
}
