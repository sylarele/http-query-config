<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Enums;

enum SortOrder: string
{
    case Ascending = 'asc';

    case Descending = 'desc';

    public static function default(): self
    {
        return self::Ascending;
    }
}
