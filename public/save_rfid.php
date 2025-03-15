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
$uid = $_POST['uid']; // Match the column name in rfid_tags

// Check if the UID already exists in the rfid_tags table
$check_sql = "SELECT * FROM rfid_tags WHERE uid = '$uid'";
$result = $conn->query($check_sql);

if ($result->num_rows == 0) {
    // Insert the new UID into the rfid_tags table
    $insert_sql = "INSERT INTO rfid_tags (uid) VALUES ('$uid')";
    if ($conn->query($insert_sql) === TRUE) {
        echo "RFID UID saved successfully.";
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
} else {
    echo "RFID UID already exists.";
}

$conn->close();
?>