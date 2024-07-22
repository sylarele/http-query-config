<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

/**
 * A relationship to apply to a query.
 */
class RelationshipValue
{
    public function __construct(
        protected readonly Relationship $relationship,
    ) {
    }

    /**
     * @return string the name of the relationship
     */
    public function getName(): string
    {
        return $this->relationship->getName();
    }

    /**
     * @return string the name of the Model relationship
     */
    public function getRelation(): string
    {
        return $this->relationship->getRelation();
    }

    /**
     * @return string[] the scopes to apply to the relationship
     */
    public function getScopes(): array
    {
        return $this->relationship->getScopes();
    }
}
