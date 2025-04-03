<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-5 rounded-lg shadow-md text-center w-full max-w-md">
    <h2 class="text-xl font-bold mb-4">Scan QR Code</h2>

    <!-- QR Scanner Container -->
    <div id="qr-reader" class="border-2 border-gray-300 p-2 rounded-md"></div>
    
    <p class="text-gray-600 mt-2">Point your camera at the QR code</p>

    <input type="hidden" id="qr-result">

    <a href="/" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-md">Go Back</a>
</div>

<script>
    function onScanSuccess(rfidUid) {
        fetch('/attendance/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Include CSRF token for security
            },
            body: JSON.stringify({
                rfid_uid: rfidUid,  // Sending the scanned RFID UID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);  // Show success message
                window.location.href = '/'; // Redirect to home or other page after scanning
            } else {
                alert(data.message);  // Show error message
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing the QR code.');
        });
    }



    function onScanError(errorMessage) {
        console.warn(`QR Scan Error: ${errorMessage}`);
    }

    // Initialize the scanner
    const html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", {
        fps: 10,  // Frames per second
        qrbox: { width: 250, height: 250 }  // Scanner box size
    });

    html5QrcodeScanner.render(onScanSuccess, onScanError);
</script>

</body>
</html>
