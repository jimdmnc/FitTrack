<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodLog extends Model
{
    use HasFactory;
    protected $table = 'food_logs'; // Add this line

    protected $fillable = [
        'food_id',
        'rfid_uid',
        'quantity',
        'date',
        'total_calories',
        'total_protein',
        'total_fats',
        'total_carbs',
    ];
}
