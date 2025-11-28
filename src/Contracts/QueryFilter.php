<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Contracts;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Stringable;

/**
 * Interface for both filters and scopes.
 *
 * @phpstan-type ValidationRule array<int, string|Stringable|Rule>
 * @phpstan-type ValidationRules array<string, ValidationRule>
 *
 * @template TModel of Model
 * @template TBuilder of Builder
 */
interface QueryFilter
{
    /**
     * @return string the name of the filter
     */
    public function getName(): string;

    /**
     * @return ValidationRules the validation rules for the filter
     */
    public function getValidation(): array;

    /**
     * Locks the filter so that it cannot be modified.
     */
    public function lock(): void;
}
