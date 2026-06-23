<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietPlan extends Model
{
    use HasFactory;

    protected $fillables = [
        'id',
        'date',
        'diet',
        'breakfast_id',
        'bruchn_id',
        'lunch_id',
        'snack_id',
        'dinner_id'
    ];
}
