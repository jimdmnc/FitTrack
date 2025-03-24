<?php
// Database connection details
$host = 'localhost';
$dbname = 'fittrack_db';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the RFID UID from the ESP32 request
$uid = $_POST['uid']; 

// Check if the UID already exists in the rfid_tags table
$check_sql = "SELECT * FROM rfid_tags WHERE uid = '$uid'";
$result = $conn->query($check_sql);

if ($result->num_rows == 0) {
    // Insert the new UID into the rfid_tags table with registered = 0 (temporary)
    $insert_sql = "INSERT INTO rfid_tags (uid, registered, created_at) VALUES ('$uid', 0, NOW())";
    if ($conn->query($insert_sql) === TRUE) {
        echo "RFID UID saved successfully. If not registered, it will be removed in 2 minutes.";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    $row = $result->fetch_assoc();
    
    if ($row['registered'] == 1) {
        echo "RFID UID is already registered.";
    } else {
        echo "UID is pending registration.";
    }
}

// Delete unregistered UIDs older than 2 minutes
$delete_sql = "DELETE FROM rfid_tags WHERE registered = 0 AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) >= 2";
$conn->query($delete_sql);

$conn->close();
?>
