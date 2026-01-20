<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'intro'       => $this->intro,
            'image'       => url('/storage/'.$this->image),
            'content'     => $this->content,
            'author' => [
                'id'   => $this->author->id,
                'name' => $this->author->name
            ],
            'category' => [
                'name' => $this->category->name,
                'slug' => $this->category->slug
            ],
            'tags' => $this->tags->map(function ($tag) {
                return [
                    'name' => $tag->name,
                    'slug' => $tag->slug
                ];
            }),
            'createdAt'  => $this->created_at
        ];
    }
}