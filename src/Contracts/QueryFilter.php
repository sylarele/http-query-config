<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Contracts;

use Illuminate\Contracts\Validation\Rule;
use Stringable;

/**
 * Interface for both filters and scopes.
 */
interface QueryFilter
{
    /**
     * @return string the name of the filter
     */
    public function getName(): string;

    /**
     * @return array<string, array<int, string|Stringable|Rule>> the validation rules for the filter
     */
    public function getValidation(): array;

    /**
     * Locks the filter so that it cannot be modified.
     */
    public function lock(): void;
}
