<?php

declare(strict_types=1);

namespace Workbench\App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Sylarele\HttpQueryConfig\Concerns\HttpBuilder;
use Workbench\App\Models\Bar;

/**
 * @template TModelClass of Bar
 *
 * @extends Builder<TModelClass>
 */
class BarBuilder extends Builder
{
    use HttpBuilder;
}
