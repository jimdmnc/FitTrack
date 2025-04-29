<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Attendance extends Model
{
    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
    'rfid_uid',
    'time_in',
    'time_out',
    'status',
    'attendance_date',
    'check_in_method',
    'session_id'];


    // Cast fields to appropriate data types
    protected $casts = [
        'attendance_date' => 'date',
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Disable auto-managing of created_at and updated_at timestamps
    public $timestamps = false;

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'rfid_uid', 'rfid_uid');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    
      // Optional: Accessor to format duration as "X hours Y minutes"
      public function getFormattedDurationAttribute()
    {
        if (!$this->time_in) {
            return 'N/A';
        }

        // For displaying in the view, handle past dates without checkout
        if (!$this->time_out && $this->time_in->startOfDay()->lt(Carbon::today())) {
            // For past days without checkout, assume checkout at 9 PM
            $checkoutTime = Carbon::parse($this->time_in)->setTime(21, 0, 0);
            $diffInSeconds = $this->time_in->diffInSeconds($checkoutTime);
        } elseif (!$this->time_out) {
            // For ongoing sessions, calculate duration up to now
            $diffInSeconds = $this->time_in->diffInSeconds(Carbon::now());
        } else {
            // Normal case with checkout time
            $diffInSeconds = $this->time_in->diffInSeconds($this->time_out);
        }

        // Format duration
        $hours = floor($diffInSeconds / 3600);
        $minutes = floor(($diffInSeconds % 3600) / 60);

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        } else {
            return $minutes . ' minutes';
        }
    }
}