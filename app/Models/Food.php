<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'foods'; // Add this line
    protected $fillable = ['foodName', 'calories', 'protein', 'fats', 'carbs', 'grams'];

}
