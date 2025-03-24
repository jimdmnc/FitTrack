<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'members_payment'; // Ensure this matches the correct table name

    protected $fillable = ['rfid_uid', 'amount', 'payment_method', 'payment_date'];

    public function user()
    {
        return $this->belongsTo(User::class, 'rfid_uid', 'rfid_uid');
    }
    
}
