<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
        'rfid_uid', // RFID UID of the user
        'time_in',  // Time when the user tapped in
        'time_out', // Time when the user tapped out
    ];

    // Cast fields to appropriate data types
    protected $casts = [
        'time_in' => 'datetime',  // Cast 'time_in' to a DateTime object
        'time_out' => 'datetime', // Cast 'time_out' to a DateTime object
    ];

    // Disable auto-managing of created_at and updated_at timestamps
    public $timestamps = false;

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'rfid_uid', 'rfid_uid');
    }
}