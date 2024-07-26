<?php

declare(strict_types=1);

namespace Workbench\App\Enums;

enum FooState: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Inactive = 'inactive';
}
