<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Rules;

use Closure;
use DateTime;
use DateTimeInterface;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Override;

/**
 * Like date_format, but allows multiple date formats.
 */
class DateFormats implements ValidationRule
{
    final public const array ISO_FORMATS = [
        DateTimeInterface::ATOM,
        DateTimeInterface::RFC3339,
        DateTimeInterface::RFC3339_EXTENDED,
    ];

    /**
     * @param  array<int,string>  $dateFormats
     */
    public function __construct(
        protected array $dateFormats,
    ) {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    #[Override]
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        if (\is_string($value) || is_numeric($value)) {
            foreach ($this->dateFormats as $format) {
                $date = DateTime::createFromFormat('!'.$format, (string) $value);

                if ($date && $date->format($format) === (string) $value) {
                    return;
                }
            }
        }

        $fail('validation.date_formats')->translate();
    }

    public static function iso(): self
    {
        return new self(self::ISO_FORMATS);
    }
}
