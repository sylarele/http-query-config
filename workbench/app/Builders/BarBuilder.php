<?php

declare(strict_types=1);

namespace Workbench\App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Sylarele\HttpQueryConfig\Concerns\HttpBuilder;
use Workbench\App\Models\Bar;

/**
 * @extends Builder<Bar>
 */
class BarBuilder extends Builder
{
    /** @use HttpBuilder<Bar> */
    use HttpBuilder;
}
