<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = ['title', 'file_path', 'alt_text', 'category', 'mime_type', 'size'];
}
