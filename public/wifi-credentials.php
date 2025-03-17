<?php
// public/wifi-credentials.php

require __DIR__ . '/../vendor/autoload.php'; // Load Laravel autoloader
$app = require_once __DIR__ . '/../bootstrap/app.php'; // Bootstrap Laravel

// Run the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Handle the request
header("Content-Type: application/json");

try {
    // Validate JSON input
    $input = json_decode($request->getContent(), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input.');
    }

    // Validate input using Laravel's validation
    $validator = Illuminate\Support\Facades\Validator::make($input, [
        'ssid' => 'required|string|max:255',
        'password' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        throw new Exception($validator->errors()->first());
    }

    // Sanitize input
    $ssid = htmlspecialchars($input['ssid']);
    $password = htmlspecialchars($input['password']);

    // Hash the password before storing it
    $hashedPassword = Illuminate\Support\Facades\Hash::make($password);

    // Insert into database using Eloquent model
    App\Models\WifiCredential::create([
        'ssid' => $ssid,
        'password' => $hashedPassword,
    ]);

    echo json_encode(['message' => 'WiFi credentials saved successfully!']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

// Terminate the application
$kernel->terminate($request, $response);