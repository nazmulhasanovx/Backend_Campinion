<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    protected $fillable = ['city', 'address', 'phone', 'email', 'map_embed_url', 'sort_order'];
}
