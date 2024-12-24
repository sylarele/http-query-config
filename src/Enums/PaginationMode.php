<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Enums;

/**
 * Pagination modes.
 */
enum PaginationMode: string
{
    /** Offset pagination (standard pagination) */
    case Offset = 'offset';

    /** Cursor pagination (better for infinite scrolling) */
    case Cursor = 'cursor';

    /** No pagination (all results returned) */
    case None = 'none';

    public static function default(): self
    {
        return self::Offset;
    }
}
