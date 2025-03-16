<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Define the membership types
    const MEMBERSHIP_TYPES = [
        7 => 'Week',
        30 => 'Monthly',
        375 => 'Annual',
    ];

    protected $fillable = [
        'first_name', // Add this
        'last_name',  // Add this
        'email',
        'password',
        'gender',
        'phone_number',
        'membership_type',
        'start_date',
        'rfid_uid',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'rfid_uid', 'rfid_uid');
    }
        // Define relationship with RfidTag
        public function rfidTag()
        {
            return $this->belongsTo(RfidTag::class, 'rfid_uid', 'uid');
        }
        
    // Method to get the membership type as a word
    public function getMembershipType()
    {
        return self::MEMBERSHIP_TYPES[$this->membership_type] ?? 'N/A';
    }
}
