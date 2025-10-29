<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Exceptions;

use LogicException;

class NotImplementedException extends LogicException
{
    public function __construct(string $message = 'Not Implemented')
    {
        parent::__construct($message);
    }
}
