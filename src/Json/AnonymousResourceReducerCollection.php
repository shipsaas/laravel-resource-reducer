<?php

namespace ShipSaasReducer\Json;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use ShipSaasReducer\Helpers\Helper;
use ShipSaasReducer\Json\Traits\HasRelation;
use ShipSaasReducer\Json\Traits\HasRequestedFields;
use ShipSaasReducer\Response\ReducerResponse;

class AnonymousResourceReducerCollection extends AnonymousResourceCollection
{
    use HasRequestedFields, HasRelation;

    /**
     * @var $collection Collection<JsonReducerResource>
     */
    public $collection;

    public function __construct($resource, $collects, array $requestedFields = [])
    {
        $this->requestedFields = $requestedFields;

        parent::__construct($resource, $collects);
    }

    public function toArray(Request $request): array
    {
        // eager-loading for collection
        $reducer = $this->collection->first();
        if (Helper::isEagerLoadableInstance($reducer?->resource) && !$this->hasRelation()) {
            $this->doEagerLoad($reducer);
        }

        return $this->collection
            ->map(
                fn (JsonReducerResource $item) => $item
                    // from Collection, we always make sure that we will do eager-load
                    // from the outer layer, so from item perspective, we don't have to do the eager-loading again
                    ->disableEagerLoad()
                    ->additional($this->additional)
                    ->only($this->requestedFields)
                    ->when($this->hasRelation(), fn () => $item->setRelationName($this->relationName), $item)
                    ->toArray($request)
            )->all();
    }

    private function doEagerLoad(JsonReducerResource $reducerResource): void
    {
        $relations = (clone $reducerResource)
            ->only($this->requestedFields)
            ->resolveRelationships();

        $collection = new EloquentCollection(
            $this->collection->map(
                fn (JsonReducerResource $reducer) => $reducer->resource
            )
        );

        $collection->load($relations);
    }

    public function toResponse($request)
    {
        return (new ReducerResponse($this))->toResponse($request);
    }
}
