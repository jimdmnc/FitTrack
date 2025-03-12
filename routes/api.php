<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\MembershipRegistrationController;



// Route to handle storing the RFID UID from the ESP32

Route::post('/register-rfid', [MembershipRegistrationController::class, 'store']);

// Route to fetch the latest RFID UID from the cache
