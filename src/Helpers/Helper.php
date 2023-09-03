<?php

namespace ShipSaasReducer\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;

class Helper
{
    /**
     * Transform from ["a", "b.c", "b.e", "c.e.f"]
     * to: [
     *  "a",
     *  "b" => ["c", "e"],
     *  "c" => [
     *    e" => ["f"]
     *  ]
     * ]
     */
    public static function transformToNestedStructure(array $fields): array
    {
        return collect($fields)
            ->reduce(function (Collection $fields, mixed $value, string $key) {
                if (is_array($value)) {
                    $fields[$key] = $value;
                } elseif (Str::contains($value, '.')) {
                    [$nestedKey, $rest] = explode('.', $value, 2);
                    $fields[$nestedKey] ??= collect();
                    $fields[$nestedKey]->push($rest);
                } else {
                    $fields->push($value);
                }

                return $fields;
            }, collect([]))
            ->map(
                fn ($value) => $value instanceof Collection
                    ? self::transformToNestedStructure($value->toArray())
                    : $value
            )
            ->toArray();
    }

    public static function isEagerLoadableInstance(mixed $instance): bool
    {
        return $instance instanceof EloquentCollection
            || $instance instanceof Model;
    }
}
