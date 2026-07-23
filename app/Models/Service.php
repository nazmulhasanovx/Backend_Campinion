<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'icon',
        'image',
        'benefits',
        'process',
        'related_project_slugs',
        'is_active',
        'sort_order',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'process' => 'array',
            'related_project_slugs' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
