<?php

namespace ShipSaasReducer\Tests\App\Http\Resources;

use Illuminate\Http\Request;
use ShipSaasReducer\Json\JsonReducerResource;

class ArticleResource extends JsonReducerResource
{
    public function definitions(Request $request): array
    {
        return [
            'id' => fn () => $this->id,
            'user_id' => fn () => $this->user_id,
            'title' => fn () => $this->title,
            'content' => fn () => $this->content,
            'created_at' => fn () => $this->created_at,
        ];
    }
}
