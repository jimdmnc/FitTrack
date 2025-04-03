<?php
namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRCodeController extends Controller
{
    // Generate Static QR Code for Gym Time-out
    public function generateGymQRCode()
    {
        // Retrieve the RFID UID (You can get this from the logged-in user or manually set a test UID)
        // Example: For logged-in user
        $rfid_uid = auth()->user()->rfid_uid; // Assuming the user is logged in and has an RFID UID
    
        // If no user is logged in or if you want to test with a static value
        // $rfid_uid = 'sample_rfid_uid'; // Replace this with the actual RFID UID for testing
        
        // URL to the scan attendance route (POST method)
        $scanUrl = url("/attendance/scan"); // POST route for time-out
    
        // Generate the QR code with the URL
        $qrCode = QrCode::size(250)->generate($scanUrl);
    
        // Pass the QR code and RFID UID to the view
        return view('gym.qrCodeDisplay', compact('qrCode', 'rfid_uid'));
    }
    
    // Save QR Code as an Image (Optional)
    public function saveGymQRCode()
    {
        $timeOutUrl = url('/attendance/timeout'); // Static time-out URL
        $qrCodePath = 'public/gym_qr_code.png'; // Path to save the QR Code
    
        // Generate and save as PNG
        Storage::put($qrCodePath, QrCode::format('png')->size(300)->generate($timeOutUrl));
    
        // Get the path for display
        $qrCodeUrl = asset('storage/gym_qr_code.png');
    
        return view('gym.qrCodeDisplayImage', compact('qrCodeUrl'));
    }
}
