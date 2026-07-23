<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->category?->name,
            'author' => $this->author,
            'published_at' => $this->published_at?->toDateString(),
            'read_time' => $this->read_time,
            'image' => $this->featured_image,
            'excerpt' => $this->excerpt,
            'content' => $this->content ?? [],
            'tags' => $this->tags ?? [],
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
        ];
    }
}
