<?php
// Database configuration
$host = 'localhost';
$dbname = 'fittrack_db';
$username = 'root';
$password = '';

// Set headers to allow cross-origin requests (if needed)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Check if request is POST and contains UID
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the UID from the request
    $uid = isset($_POST['uid']) ? $_POST['uid'] : null;
    
    // Validate the UID (basic validation - adjust as needed)
    if (!empty($uid) && strlen($uid) <= 30) {
        try {
            // Connect to the database
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // **Delete the existing UID if it exists**
            $deleteStmt = $pdo->prepare("DELETE FROM rfid_tags WHERE uid = :uid");
            $deleteStmt->bindParam(':uid', $uid);
            $deleteStmt->execute();
            
            // **Insert the new UID**
            $stmt = $pdo->prepare("INSERT INTO rfid_tags (uid, created_at) VALUES (:uid, NOW())");
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            
            // Respond with success
            echo json_encode(['status' => 'success', 'message' => 'UID saved successfully (Old UID deleted)']);
            
        } catch (PDOException $e) {
            // Handle database errors
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Invalid UID data
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid UID data']);
    }
} else {
    // Method not allowed
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
?>
