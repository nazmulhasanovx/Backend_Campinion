<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'price' => $this->price,
            'location' => $this->location,
            'status' => $this->status,
            'category' => $this->category?->name,
            'client' => $this->client,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'progress' => $this->progress,
            'beds' => $this->beds,
            'baths' => $this->baths,
            'parking' => $this->parking,
            'area' => $this->area,
            'image' => $this->featured_image,
            'summary' => $this->summary,
            'description' => $this->description,
            'features' => $this->features ?? [],
            'gallery' => $this->whenLoaded('images', fn () => $this->images->map(fn ($image) => [
                'image' => $image->image,
                'alt_text' => $image->alt_text,
            ])->values()),
            'timeline' => $this->whenLoaded('timelines', fn () => $this->timelines->map(fn ($timeline) => [
                'title' => $timeline->title,
                'date' => $timeline->date_label,
                'description' => $timeline->description,
            ])->values()),
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
        ];
    }
}
