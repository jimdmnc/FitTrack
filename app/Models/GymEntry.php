<?php

// app/Models/GymEntry.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GymEntry extends Model
{
    use HasFactory;

    // Specify the table name (optional if the table name follows Laravel's naming convention)
    protected $table = 'gym_entries';

    // Specify the primary key (optional if it's 'id')
    protected $primaryKey = 'id';

    // Define the fields that are mass assignable
    protected $fillable = [
        'rfid_uid', // Foreign key to the users table
        'entry_time', // Timestamp of the gym entry
    ];

    // Define the fields that should be cast to native types
    protected $casts = [
        'entry_time' => 'datetime', // Cast entry_time to a Carbon instance
    ];

    // Relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'rfid_uid', 'rfid_uid');
    }
}