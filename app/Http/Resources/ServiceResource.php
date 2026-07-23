<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->description,
            'icon' => $this->icon,
            'image' => $this->image,
            'benefits' => $this->benefits ?? [],
            'process' => $this->process ?? [],
            'related_project_slugs' => $this->related_project_slugs ?? [],
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
        ];
    }
}
