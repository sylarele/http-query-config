<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Sylarele\HttpQueryConfig\Contracts\QueryPagination;
use Sylarele\HttpQueryConfig\Enums\PaginationMode;
use Sylarele\HttpQueryConfig\Query\Pagination\CursorPagination;
use Sylarele\HttpQueryConfig\Query\Pagination\NoPagination;
use Sylarele\HttpQueryConfig\Query\Pagination\OffsetPagination;

/**
 * Pagination configuration for a query.
 */
class Pagination
{
    /** @var array<int,PaginationMode> the allowed pagination modes on the query */
    protected array $allowed = [
        PaginationMode::Offset,
        PaginationMode::Cursor
    ];

    /** @var PaginationMode the default pagination mode on the query, if none specified */
    protected PaginationMode $default = PaginationMode::Offset;

    /** @var int the default limit on the query, aka items per page */
    protected int $defaultLimit = 50;

    /** @var int the maximum limit on the query, aka items per page */
    protected int $maxLimit = 100;

    /**
     * Sets the default limit on the query (if none specified).
     */
    public function defaultLimit(int $default): static
    {
        $this->defaultLimit = $default;

        return $this;
    }

    /**
     * Sets the maximum limit on the query.
     */
    public function maxLimit(int $max): static
    {
        $this->maxLimit = $max;

        return $this;
    }

    /**
     * Allows pagination=none on the query.
     */
    public function allowNone(): static
    {
        if (!\in_array(PaginationMode::None, $this->allowed, true)) {
            $this->allowed[] = PaginationMode::None;
        }

        return $this;
    }

    /**
     * Never paginates the query.
     */
    public function none(): static
    {
        $this->allowed = [PaginationMode::None];
        $this->default = PaginationMode::None;

        return $this;
    }

    /**
     * Sets the default pagination mode on the query.
     */
    public function default(PaginationMode $default): static
    {
        if ($default === PaginationMode::None) {
            $this->allowNone();
        }

        $this->default = $default;

        return $this;
    }

    /**
     * Sets `none` as the default pagination mode on the query.
     */
    public function defaultNone(): static
    {
        return $this->default(PaginationMode::None);
    }

    /**
     * @return PaginationMode the default pagination mode on the query
     */
    public function getDefaultMode(): PaginationMode
    {
        return $this->default;
    }

    /**
     * @return array<int,PaginationMode> the allowed pagination modes on the query
     */
    public function getAllowed(): array
    {
        return $this->allowed;
    }

    /**
     * @return int the default limit on the query
     */
    public function getDefaultLimit(): int
    {
        return $this->defaultLimit;
    }

    /**
     * @return int the maximum limit on the query
     */
    public function getMaxLimit(): int
    {
        return $this->maxLimit;
    }

    /**
     * @return QueryPagination a default pagination instance, based on the default mode
     */
    public function makeDefault(): QueryPagination
    {
        return match ($this->default) {
            PaginationMode::None => new NoPagination(),

            PaginationMode::Offset => new OffsetPagination(
                page: 1,
                limit: $this->defaultLimit,
            ),

            PaginationMode::Cursor => new CursorPagination(
                cursor: null,
                limit: $this->defaultLimit,
            ),
        };
    }
}
