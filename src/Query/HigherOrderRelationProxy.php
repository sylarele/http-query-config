<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

/**
 * Applies relationship config on multiple relationships.
 */
class HigherOrderRelationProxy
{
    /**
     * @param array<int,Relationship> $relationships
     */
    public function __construct(
        protected array $relationships,
    ) {
    }

    /**
     * Sets the name of the relation used on the model (if different from the API relationship name).
     */
    public function relation(string $relationName): static
    {
        $this->relationships = array_map(
            static fn (Relationship $relationship): Relationship => $relationship->relation($relationName),
            $this->relationships,
        );

        return $this;
    }

    /**
     * Applies scopes to the relationship.
     */
    public function withScopes(string ...$scopes): static
    {
        $this->relationships = array_map(
            static fn (Relationship $relationship): Relationship => $relationship->withScopes(...$scopes),
            $this->relationships,
        );

        return $this;
    }
}
