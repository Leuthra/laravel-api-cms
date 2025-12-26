<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'content'      => $this->content,
            'type'         => $this->type,
            'status'       => $this->status,
            'payload'      => $this->payload,
            'seo'          => $this->seo,
            'featured_img' => $this->getFirstMediaUrl('featured_images'),
            'author'       => $this->author->name ?? 'Anonymous',
            'taxonomies'   => $this->taxonomies->map(fn($t) => ['id' => $t->id, 'name' => $t->name]),
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
