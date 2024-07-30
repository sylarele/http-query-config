<?php

declare(strict_types=1);

namespace Workbench\App\Queries;

use Illuminate\Validation\Rules\Enum;
use Override;
use Sylarele\HttpQueryConfig\Enums\FilterType;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Query\QueryConfig;
use Sylarele\HttpQueryConfig\Query\ScopeArgument;
use Workbench\App\Enums\FooState;
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
        // Filter
        $config->filter('name')->type(FilterType::String);
        $config->filter('size')->type(FilterType::Integer);

        // Scopes
        $config
            ->filter('whereState')
            ->scope('whereState')
            ->arg(
                'state',
                static fn (ScopeArgument $arg): ScopeArgument => $arg
                    ->withValidation(['string', new Enum(FooState::class)])
                    ->transform(
                        static fn (string $value): FooState => FooState::from($value)
                    )
            );

        // Sorts
        $config->sorts('id', 'name');

        // With
        $config->with('bars');

        $config->only(['name', 'size']);
    }
}
