<!-- resources/views/gym/qrCodeDisplay.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym QR Code</title>
</head>
<body>
    <h1>Scan the QR Code to Time Out</h1>
    <div>
        {!! $qrCode !!} <!-- This will display the generated QR Code -->
    </div>
</body>
</html>
