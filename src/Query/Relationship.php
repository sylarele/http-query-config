<?php

declare(strict_types=1);

namespace Sylarele\HttpQueryConfig\Query;

/**
 * Used to eager load relationships on a query.
 */
class Relationship
{
    /** @var string the name of the Model relationship */
    protected string $relation;

    /** @var array<int,string> the scopes to apply to the relationship */
    protected array $scopes = [];

    /**
     * @param  string  $name the name of the relationship
     */
    public function __construct(
        protected readonly string $name,
    ) {
        $this->relation = $name;
    }

    /**
     * Sets the name of the relation used on the model (if different from the API relationship name).
     */
    public function relation(string $relationName): static
    {
        $this->relation = $relationName;

        return $this;
    }

    /**
     * Applies scopes to the relationship.
     */
    public function withScopes(string ...$scopes): static
    {
        $this->scopes = array_merge($this->scopes, array_values($scopes));

        return $this;
    }

    /**
     * @return string the name of the relationship
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string the name of the relation used on the model
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * @return array<int,string> the scopes to apply to the relationship
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }
}
