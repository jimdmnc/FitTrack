<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConnectHardwareController;

// Define your API routes
Route::post('/wifi-credentials', [ConnectHardwareController::class, 'store']);