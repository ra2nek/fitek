<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model\User;

class Course extends Model
{

    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'thumbnail_url',
        'type',
        'category',
        'tags',
        'level',
        'time',
        'likes',
        'views',
        'created_at',
        'updated_at'
    ];
}
