<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Exceptions;

use LogicException;

class QueryConfigLockedException extends LogicException
{
    public function __construct()
    {
        parent::__construct('Cannot update configuration after query has been built.');
    }
}
