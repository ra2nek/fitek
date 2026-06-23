<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'meal_time',
        'calories',
        'suitable_diets',
        'ingredients',
        'preparation',
        'likes',
        'views',
        'time',
        'difficulty',
        'created_at',
        'updated_at'
    ];
}
