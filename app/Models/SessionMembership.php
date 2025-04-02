<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'session_start',
        'session_end',
        'qr_code_path'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
