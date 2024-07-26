<?php

declare(strict_types=1);

namespace Workbench\App\Http\Requests;

use Override;
use Sylarele\HttpQueryConfig\Http\QueryRequest;
use Workbench\App\Queries\FooQuery;

/**
 * @extends QueryRequest<FooQuery>
 */
class IndexFooRequest extends QueryRequest
{
    #[Override]
    protected function getQuery(): string
    {
        return FooQuery::class;
    }
}
