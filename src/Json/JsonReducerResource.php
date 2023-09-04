<?php

namespace ShipSaasReducer\Json;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;
use LogicException;
use ShipSaasReducer\Helpers\Helper;
use ShipSaasReducer\Helpers\RequestHelper;
use ShipSaasReducer\Json\Relationships\RelationResolver;
use ShipSaasReducer\Json\Traits\HasRelation;
use ShipSaasReducer\Json\Traits\HasRequestedFields;
use ShipSaasReducer\Response\ReducerResponse;

abstract class JsonReducerResource extends JsonResource
{
    use HasRelation, HasRequestedFields;

    protected array $computedDefinitions;

    /**
     * The resource definition
     *
     * Same as Laravel Resource, but you need to wrap the field inside a callback, e.g:
     * [
     *  'uuid' => fn () => $this->uuid,
     *  'email' => fn () => $this->email,
     * ]
     *
     * Additionally, you can use the $request to compute logic too, inside the callback of course
     *
     * @return array<string, callable>
     */
    public abstract function definitions(Request $request): array;

    /**
     * @internal this method is not meant to use from userland
     *
     * You either use these:
     * - (new MyResource($my))->response()
     * - MyResource::collection($my)->response()
     */
    public function toArray(Request $request): array
    {
        if (is_null($this->resource)) {
            return [];
        }

        $this->computeDefinitions();

        $this->requestedFields = $this->requestedFields
            ?? RequestHelper::getStructuredRequestedFields($request, function () {
                return array_keys($this->computedDefinitions);
            });

        // here we will do eager loading
        // for single instance mode
        if (Helper::isEagerLoadableInstance($this->resource) && $this->shouldEagerLoad()) {
            $relationships = $this->resolveRelationships();
            $this->resource->load($relationships);
        }

        if (!is_array($this->resource) && !($this->resource instanceof Collection)) {
            return $this->transformSingleResource($this->resource);
        }

        return collect($this->resource)
            ->map($this->transformSingleResource(...))
            ->toArray();
    }

    private function computeDefinitions(): void
    {
        $this->computedDefinitions = $this->definitions(RequestHelper::getCurrentRequest()) + $this->additional;
        $this->validateDefinitions();
    }

    /**
     * @throws LogicException
     */
    private function validateDefinitions(): void
    {
        $unwrappedFields = array_filter(
            $this->computedDefinitions,
            fn ($value, $key) => !is_callable($value) && !($value instanceof JsonReducerResource),
            ARRAY_FILTER_USE_BOTH
        );

        if (count($unwrappedFields) > 0) {
            $unwrappedKeys = implode(', ', array_keys($unwrappedFields));

            throw new LogicException(
                "All definition fields must be wrapped inside a Closure. Error keys: {$unwrappedKeys}"
            );
        }
    }

    public function resolveRelationships(): array
    {
        $this->computeDefinitions();

        return RelationResolver::resolve($this->computedDefinitions, $this->requestedFields)
            ->toArray();
    }

    private function transformSingleResource(mixed $resource): array
    {
        $this->resource = $resource;

        $flatRequestedFields = collect($this->requestedFields)
            ->map(fn ($value, $key) => is_array($value) ? $key : $value);

        return collect($this->computedDefinitions)
            ->only($flatRequestedFields)
            ->map($this->transformValue(...))
            ->toArray();
    }

    private function transformValue(mixed $retriever, string $requestedKey): mixed
    {
        if ($retriever instanceof JsonReducerResource) {
            if (!$this->resource instanceof Model) {
                return null;
            }

            $relationName = $retriever->getRelationName();
            if (!$this->resource->relationLoaded($relationName)) {
                return null;
            }

            $relationObject = $this->resource->{$relationName};
            $retrieverHandler = $relationObject instanceof Collection
                ? $retriever::collection($relationObject)
                : $retriever::make($relationObject);

            // API consumers would send different keys
            // not the relationName
            $wantedFields = $this->requestedFields[$requestedKey];

            return $retrieverHandler
                ->setRelationName($retriever->relationName)
                ->only($wantedFields)
                ->disableEagerLoad()
                ->toArray(RequestHelper::getCurrentRequest());
        }

        $value = value($retriever);

        // we'll need to ensure the deferred value is a legit value
        if ($value instanceof MissingValue) {
            return null;
        }

        return $value;
    }

    public function toResponse($request)
    {
        return (new ReducerResponse($this))->toResponse($request);
    }

    protected static function newCollection($resource, array $requestedFields = null): AnonymousResourceReducerCollection
    {
        $requestedFields ??= RequestHelper::getStructuredRequestedFields();

        return new AnonymousResourceReducerCollection($resource, static::class, $requestedFields);
    }

    public static function makeRelation(string $name): static
    {
        $relationalResource = new static(null);
        $relationalResource->setRelationName($name);

        return $relationalResource;
    }
}
