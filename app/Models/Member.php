<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    // Define the fillable attributes
    protected $fillable = [
        'name',
        'email',
        'membership_type',
        'join_date',
        'rfid_uid', // Add RFID UID if needed
    ];

    // Define the relationship with payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}