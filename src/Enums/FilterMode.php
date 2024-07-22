<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Enums;

enum FilterMode: string
{
    case Equals = 'equals';

    case Contains = 'contains';

    case GreaterThan = 'gt';

    case GreaterThanOrEqual = 'gte';

    case LessThan = 'lt';

    case LessThanOrEqual = 'lte';

    case In = 'in';

    /**
     * @return array<int,self>
     */
    public static function numericComparisonCases(): array
    {
        return [
            self::Equals,
            self::GreaterThan,
            self::GreaterThanOrEqual,
            self::LessThan,
            self::LessThanOrEqual,
            self::In,
        ];
    }
}
