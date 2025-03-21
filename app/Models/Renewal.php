<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'rfid_uid', 'rfid_uid');
    }}
