<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Exceptions;

use Exception;
use InvalidArgumentException;

class InvalidTransformerArgumentTypeException extends InvalidArgumentException
{
    public function __construct(string $message = 'Invalid transformer argument type')
    {
        parent::__construct($message);
    }
}
