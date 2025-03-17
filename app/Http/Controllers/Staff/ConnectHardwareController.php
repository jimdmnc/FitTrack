<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WifiCredential;
use Illuminate\Support\Facades\Validator;

class ConnectHardwareController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'ssid' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Store credentials in the database
        $wifiCredential = WifiCredential::create([
            'ssid' => $request->ssid,
            'password' => $request->password,
        ]);

        return response()->json(['message' => 'Credentials saved successfully', 'data' => $wifiCredential], 201);
    }
}