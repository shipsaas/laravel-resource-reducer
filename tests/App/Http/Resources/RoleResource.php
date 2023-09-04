<?php

namespace ShipSaasReducer\Tests\App\Http\Resources;

use Illuminate\Http\Request;
use ShipSaasReducer\Json\JsonReducerResource;

class RoleResource extends JsonReducerResource
{
    public function definitions(Request $request): array
    {
        return [
            'id' => fn () => $this->id,
            'name' => fn () => $this->name,
            'created_at' => fn () => $this->created_at,

            'permissions' => PermissionResource::makeRelation('permissions'),
        ];
    }
}
