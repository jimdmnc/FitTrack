<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfid_uid', 'age', 'gender', 'weight', 'height', 'activity_level'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'rfid_uid', 'rfid_uid');
    }
}
