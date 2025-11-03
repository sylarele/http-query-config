<?php

declare(strict_types=1);

namespace Workbench\App\Queries;

use Closure;
use Illuminate\Validation\Rules\Enum;
use Override;
use Sylarele\HttpQueryConfig\Enums\FilterType;
use Sylarele\HttpQueryConfig\Query\Query;
use Sylarele\HttpQueryConfig\Query\QueryConfig;
use Sylarele\HttpQueryConfig\Query\ScopeArgument;
use Sylarele\HttpQueryConfig\Transformers\EnumListTransformer;
use Sylarele\HttpQueryConfig\Transformers\EnumTransformer;
use Workbench\App\Builders\FooBuilder;
use Workbench\App\Enums\FooState;
use Workbench\App\Models\Foo;

/**
 * @extends Query<Foo,FooBuilder>
 */
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
            ->scopeClosure(static fn (FooBuilder $instance): Closure => $instance->whereState(...))
            ->arg(
                'state',
                static fn (ScopeArgument $arg): ScopeArgument => $arg
                    ->withValidation([
                        'required_with:whereState',
                        'string',
                        new Enum(FooState::class)
                    ])
                    ->transform(new EnumTransformer(FooState::class))
            );
        $config
            ->filter('whereStates')
            ->scope('whereStates')
            ->arg(
                'states',
                static fn (ScopeArgument $arg): ScopeArgument => $arg
                    ->withValidation(['required_with:whereStates', 'array', 'min:1'])
                    ->addedValidation('*', ['required', 'string', new Enum(FooState::class)])
                    ->transform(new EnumListTransformer(FooState::class))
            );
        $config
            ->filter('whereStateDefault')
            ->scope()
            ->arg(
                'state',
                static fn (ScopeArgument $argument): ScopeArgument => $argument
                    ->withValidation([
                        'nullable',
                        'string',
                        new Enum(FooState::class)
                    ])
            );
        $config
            ->filter('whereStateUsingDefault')
            ->scope('whereStateDefault')
            ->arg(
                'state',
                static fn (ScopeArgument $argument): ScopeArgument => $argument
                    ->using(static fn (): FooState => FooState::Inactive)
                    ->withValidation([
                        'nullable',
                        'string',
                        new Enum(FooState::class)
                    ])
            );

        // Sorts
        $config->sorts('id', 'name');

        // With
        $config->with('bars');

        $config->only(['name', 'size']);
    }
}
