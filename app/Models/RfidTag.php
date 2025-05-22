<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidTag extends Model
{
    use HasFactory;
    protected $table = 'rfid_tags';

    protected $fillable = ['uid'];

        // Define relationship with User
        public function user()
        {
            return $this->hasOne(User::class, 'rfid_uid', 'uid'); // Use 'uid' as the foreign key
        }
}