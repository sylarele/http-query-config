<?php

namespace Sylarele\HttpQueryConfig\Exceptions;

class NotImplementedException extends \LogicException
{
    public function __construct(string $message = 'Not Implemented')
    {
        parent::__construct($message);
    }
}