<?php

declare(strict_types=1);

namespace Workbench\App\Services;

use Sylarele\HttpQueryConfig\Contracts\QueryResult;
use Workbench\App\Models\Foo;
use Workbench\App\Queries\FooQuery;

class FooService
{
    public function list(FooQuery $query): QueryResult
    {
        return Foo::query()
            ->configureForQuery($query)
            ->paginateForQuery($query);
    }
}
