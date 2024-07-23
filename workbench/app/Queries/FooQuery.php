<?php

declare(strict_types=1);

namespace Workbench\App\Queries;

use Override;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Query\QueryConfig;
use Workbench\App\Models\Foo;

class FooQuery extends Query
{
    /**
     * @return class-string<Foo>
     */
    #[Override]
    protected function model(): string
    {
        return Foo::class;
    }

    #[Override]
    protected function configure(QueryConfig $config): void
    {
        $config->sorts(
            'id',
            'name',
        );
    }
}
