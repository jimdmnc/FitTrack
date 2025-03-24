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
        'rfid_uid', // RFID UID of the user
        'time_in',  // Time when the user tapped in
        'time_out', // Time when the user tapped out
    ];

    // Cast fields to appropriate data types
    protected $casts = [
        'time_in' => 'datetime',  // Cast 'time_in' to a DateTime object
        'time_out' => 'datetime', // Cast 'time_out' to a DateTime object
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
          // Ensure both time_in and time_out are not null
          if ($this->time_in && $this->time_out) {
              $timeIn = Carbon::parse($this->time_in);
              $timeOut = Carbon::parse($this->time_out);
              $diff = $timeOut->diff($timeIn);
  
              // Format duration as "X hours Y minutes"
              return sprintf('%d hrs %d min', $diff->h, $diff->i);
          }
  
          return 'N/A'; // Return 'N/A' if either time_in or time_out is missing
      }
}