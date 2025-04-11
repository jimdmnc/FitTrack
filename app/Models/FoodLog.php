<?php
// app/Models/FoodLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodLog extends Model
{
    protected $fillable = [
        'food_id',
        'rfid_uid',
        'meal_type',
        'quantity',
        'date',
        'total_calories',
        'total_protein',
        'total_fats',
        'total_carbs'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'total_calories' => 'decimal:2',
        'total_protein' => 'decimal:2',
        'total_fats' => 'decimal:2',
        'total_carbs' => 'decimal:2',
        'date' => 'date'
    ];

    // Relationship to Food (if you have a foods table)
    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }
    

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
