<?php

namespace ShipSaasReducer\Tests\App\Http\Resources;

use Illuminate\Http\Request;
use ShipSaasReducer\Json\JsonReducerResource;

class PermissionResource extends JsonReducerResource
{
    public function definitions(Request $request): array
    {
        return [
            'id' => fn () => $this->id,
            'role_id' => fn () => $this->role_id,
            'name' => fn () => $this->name,
            'created_at' => fn () => $this->created_at,
        ];
    }
}
