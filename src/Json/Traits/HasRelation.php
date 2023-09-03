<?php

namespace ShipSaasReducer\Json\Traits;

trait HasRelation
{
    protected string $relationName;

    protected bool $shouldEagerLoad = true;

    public function setRelationName(string $name): self
    {
        $this->relationName = $name;

        return $this;
    }

    public function hasRelation(): bool
    {
        return isset($this->relationName);
    }

    public function getRelationName(): string
    {
        return $this->relationName;
    }

    public function shouldEagerLoad(): bool
    {
        return $this->shouldEagerLoad;
    }

    public function disableEagerLoad(): self
    {
        $this->shouldEagerLoad = false;

        return $this;
    }
}
