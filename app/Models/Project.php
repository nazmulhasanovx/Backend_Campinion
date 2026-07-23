<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'project_category_id',
        'title',
        'slug',
        'price',
        'location',
        'status',
        'client',
        'start_date',
        'end_date',
        'progress',
        'beds',
        'baths',
        'parking',
        'area',
        'featured_image',
        'summary',
        'description',
        'features',
        'is_featured',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_featured' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(ProjectTimeline::class)->orderBy('sort_order');
    }
}
