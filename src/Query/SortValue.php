<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

use Sylarele\HttpQueryConfig\Enums\SortOrder;

/**
 * A sorting value on a query.
 */
class SortValue
{
    public function __construct(
        protected readonly Sort $sort,
        protected readonly SortOrder $order,
    ) {
    }

    /**
     * @return string the name of the sort
     */
    public function getName(): string
    {
        return $this->sort->getName();
    }

    /**
     * @return string the database field name of the sort
     */
    public function getField(): string
    {
        return $this->sort->getField();
    }

    /**
     * @return SortOrder the sort order (ASC or DESC)
     */
    public function getOrder(): SortOrder
    {
        return $this->order;
    }
}
