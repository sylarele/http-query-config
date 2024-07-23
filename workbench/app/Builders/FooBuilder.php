<?php

declare(strict_types=1);

namespace Workbench\App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Sylarele\HttpQueryConfig\Concerns\HttpBuilder;
use Workbench\App\Models\Foo;

/**
 * @template TModelClass of Foo
 *
 * @extends Builder<TModelClass>
 */
class FooBuilder extends Builder
{
    use HttpBuilder;
}
