<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'project_type',
        'location',
        'estimated_budget',
        'message',
        'status',
    ];
}
