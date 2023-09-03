<?php

namespace ShipSaasReducer\Helpers;

use Illuminate\Container\Container;
use Illuminate\Http\Request;

class RequestHelper
{
    public static function getCurrentRequest(): Request
    {
        return Container::getInstance()->make('request');
    }

    /**
     * Accepting 2 types:
     * _f=id,name,email
     * _f[]=id&_f[]=name
     *
     * @return array
     */
    public static function getStructuredRequestedFields(
        Request $request = null,
        callable $default = null
    ): array {
        $request ??= static::getCurrentRequest();

        $fields = $request->input('_fields')
            ?: $request->input('_f');

        if (!$fields) {
            return value($default);
        }

        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }

        return Helper::transformToNestedStructure($fields) ?: value($default);
    }
}
