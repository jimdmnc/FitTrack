<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalkthroughData extends Model
{
    use HasFactory;

    protected $table = 'user_details'; // ✅ Set to your actual table name

    protected $fillable = [
        'rfid_uid', 'gender', 'activity_level', 'age', 'height', 'weight', 'target_muscle', 'goal',
    ];
}
