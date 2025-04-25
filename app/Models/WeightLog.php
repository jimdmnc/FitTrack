<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeightLog extends Model
{
    protected $table = 'weight_logs';

    protected $fillable = [
        'rfid_uid',
        'weight',
        'log_date'
    ];

    // Optional: Cast weight to float
    protected $casts = [
        'weight' => 'float',
        'log_date' => 'date:Y-m-d'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'rfid_uid', 'rfid_uid'); // Use 'uid' as the foreign key
    }
}