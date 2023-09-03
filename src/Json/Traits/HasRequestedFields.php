<?php

namespace ShipSaasReducer\Json\Traits;

trait HasRequestedFields
{
    protected array $requestedFields;

    public function only(array $fields): self
    {
        $this->requestedFields = $fields;

        return $this;
    }
}
