<?php

declare(strict_types=1);

namespace Workbench\App\Queries;

use Illuminate\Validation\Rules\Enum;
use InvalidArgumentException;
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
            ->scope()
            ->arg(
                'state',
                static fn (ScopeArgument $arg): ScopeArgument => $arg
                    ->withValidation([
                        'required_with:whereState',
                        'string',
                        new Enum(FooState::class)
                    ])
                    ->transform(
                        static fn (array|string $value): FooState => \is_array($value)
                            ? throw new InvalidArgumentException()
                            : FooState::from($value)
                    )
            );
        $config
            ->filter('whereStates')
            ->scope()
            ->arg(
                'states',
                static fn (ScopeArgument $arg): ScopeArgument => $arg
                    ->withValidation(['required_with:whereStates', 'array', 'min:1'])
                    ->addedValidation('*', ['required', 'string', new Enum(FooState::class)])
                    ->transform(
                        static fn (array|string $data): array => \is_string($data)
                            ? throw new InvalidArgumentException()
                            : array_map(
                                static fn (string $value): FooState => FooState::from($value),
                                $data
                            )
                    )
            );

        // Sorts
        $config->sorts('id', 'name');

        // With
        $config->with('bars');

        $config->only(['name', 'size']);
    }
}
