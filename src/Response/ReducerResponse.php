<?php

namespace ShipSaasReducer\Response;

use Illuminate\Http\Resources\Json\ResourceResponse;

class ReducerResponse extends ResourceResponse
{
    public function toResponse($request)
    {
        return tap(response()->json(
            $this->wrap(
                $this->resource->resolve($request),
                $this->resource->with($request)
            ),
            $this->calculateStatus(),
            [],
            $this->resource->jsonOptions()
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource;

            $this->resource->withResponse($request, $response);
        });
    }
}
