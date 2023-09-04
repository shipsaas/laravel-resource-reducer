<?php

namespace ShipSaasReducer\Tests\App\Http\Resources;

use Illuminate\Http\Request;
use ShipSaasReducer\Json\JsonReducerResource;

class UserResource extends JsonReducerResource
{
    public function definitions(Request $request): array
    {
        return [
            'id' => fn () => $this->id,
            'name' => fn () => $this->name,
            'email' => fn () => $this->email,
            'created_at' => fn () => $this->created_at,

            'articles' => ArticleResource::makeRelation('articles'),
            'role' => RoleResource::makeRelation('role'),
        ];
    }
}
