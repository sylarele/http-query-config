<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Enums;

use BackedEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Sylarele\HttpQueryConfig\Rules\DateFormats;

enum FilterType
{
    case String;

    case Integer;

    case Float;

    case Date;

    case DateTime;

    case Boolean;

    case Array;

    /**
     * @return array<int,string|ValidationRule>
     */
    public function getValueValidation(): array
    {
        return match ($this) {
            self::String => ['string', 'max:256'],
            self::Integer => ['integer'],
            self::Float => ['numeric'],
            self::Date => [new DateFormats(['Y-m-d'])],
            self::DateTime => [new DateFormats(DateFormats::ISO_FORMATS)],
            self::Boolean => ['boolean'],
            self::Array => ['array'],
        };
    }

    /**
     * @return array<int,BackedEnum>
     */
    public function getModes(): array
    {
        return match ($this) {
            self::String => [FilterMode::Equals, FilterMode::Contains, FilterMode::In],
            self::Integer => FilterMode::numericComparisonCases(),
            self::Float => FilterMode::numericComparisonCases(),
            self::Date => FilterMode::numericComparisonCases(),
            self::DateTime => FilterMode::numericComparisonCases(),
            self::Boolean => [FilterMode::Equals],
            self::Array => [FilterMode::In],
        };
    }

    public function getDefaultMode(): FilterMode
    {
        return match ($this) {
            self::String => FilterMode::Contains,
            self::Integer => FilterMode::Equals,
            self::Float => FilterMode::Equals,
            self::Date => FilterMode::Equals,
            self::DateTime => FilterMode::Equals,
            self::Boolean => FilterMode::Equals,
            self::Array => FilterMode::In,
        };
    }
}
