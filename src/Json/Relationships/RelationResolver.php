<?php

namespace ShipSaasReducer\Json\Relationships;

use Illuminate\Support\Collection;
use ShipSaasReducer\Helpers\RequestHelper;
use ShipSaasReducer\Json\JsonReducerResource;

class RelationResolver
{
    /**
     * From "definitions", load all relationships here
     *
     * @param array<array-key, callable|JsonReducerResource> $definitions
     * @param array $requestedFields the requested fields from Consumers
     *
     * @return Collection
     */
    public static function resolve(array $definitions, array $requestedFields): Collection
    {
        return collect($definitions)
            ->filter(fn ($value, $key) => $value instanceof JsonReducerResource)
            ->mapWithKeys(function (JsonReducerResource $value, string $key) use ($requestedFields) {
                if (!array_key_exists($key, $requestedFields)) {
                    return [];
                }

                $childDefinitions = $value->definitions(RequestHelper::getCurrentRequest());
                $relationName = $value->getRelationName();

                return [
                    $relationName => static::resolve(
                        $childDefinitions,
                        $requestedFields[$key]
                    )->keys(),
                ];
            });
    }
}
