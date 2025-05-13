<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ Correct import

use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{

    use HasFactory; // ✅ Now it works!

    protected $fillable = [
        'rfid_uid',  // ✅ Add this line
        'membership_type',
        'start_date',
        'end_date',
        'payment_method', // ✅ Added payment_method here
        'session_status', // Add this
        'payment_screenshot',


    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'rfid_uid', 'rfid_uid');
    }
}
