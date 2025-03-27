<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Renewal;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    // Define the membership types
    const MEMBERSHIP_TYPES = [
        7 => 'Week',
        30 => 'Month',
        365 => 'Annual',
        1 => 'Session',
    ];

    protected $fillable = [
        'first_name', // Add this
        'last_name',  // Add this
        'email',
        'gender',
        'phone_number',
        'membership_type',
        'start_date',
        'end_date', // Ensure this is included
        'rfid_uid',
        'role',
        'birthdate', // ✅ Added birthdate here
        'password', // ✅ This must be here!
        'member_status',


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
    

    public function userDetails()
    {
        return $this->hasOne(UserDetail::class, 'rfid_uid', 'rfid_uid');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'rfid_uid', 'rfid_uid');
    }


    public function getTotalVisitsAttribute()
    {
        return $this->attendances()
            ->whereYear('time_in', now()->year)
            ->whereMonth('time_in', now()->month)
            ->count();
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
        // Relationship to gym entries
        public function gymEntries()
        {
            return $this->hasMany(GymEntry::class, 'rfid_uid', 'rfid_uid');
        }
    public function renewals()
    {
        return $this->hasMany(Renewal::class, 'rfid_uid', 'rfid_uid');
    }

    public function checkIns()
    {
        return $this->hasMany(Attendance::class, 'rfid_uid', 'rfid_uid');
    }
    public function payments() {
        return $this->hasMany(MembersPayment::class, 'rfid_uid', 'rfid_uid');
    }


// In User model
public function attendance()
{
    return $this->hasOne(Attendance::class, 'rfid_uid', 'rfid_uid'); // 'rfid_uid' in both tables
}

    
    public function getMembershipTypeNameAttribute()
{
    return self::MEMBERSHIP_TYPES[$this->membership_type] ?? 'N/A';
}

    
    // In User.php model

// Accessor for membership status
public function getMembershipStatusTextAttribute()
{
    return self::MEMBERSHIP_TYPES[$this->membership_status] ?? 'Unknown';
}



}
