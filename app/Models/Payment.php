<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Define the fillable attributes
    protected $fillable = [
        'member_id',
        'amount',
        'payment_date',
        'status', // Add status if needed (e.g., 'paid', 'pending')
    ];

    // Define the relationship with the member
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}