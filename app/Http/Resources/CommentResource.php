<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id' => $this->id,
        'body' => $this->body,
        'user' => [
            'name' => $this->user->name,
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name),
        ],
        'created_at' => $this->created_at->diffForHumans(),
        'parent_id' => $this->parent_id,
        'replies' => CommentResource::collection($this->whenLoaded('replies')),
    ];
    }
}
