<?php
// Database connection
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

// Get the UID from the request
$uid = $_POST['uid'];

// Check if the user is registered
$user_sql = "SELECT * FROM users WHERE rfid_uid = '$uid'";
$user_result = $conn->query($user_sql);

if ($user_result->num_rows == 0) {
    echo json_encode(['message' => 'User not registered.']);
    exit;
}

// Get the current time
$current_time = date('Y-m-d H:i:s');

// Check if there's an existing attendance record for today
$attendance_sql = "SELECT * FROM attendances WHERE rfid_uid = '$uid' AND DATE(time_in) = CURDATE() ORDER BY time_in DESC LIMIT 1";
$attendance_result = $conn->query($attendance_sql);

if ($attendance_result->num_rows > 0) {
    $attendance = $attendance_result->fetch_assoc();
    // If there's a time-in record but no time-out, update time-out
    if (!$attendance['time_out']) {
        $update_sql = "UPDATE attendances SET time_out = '$current_time' WHERE id = " . $attendance['id'];
        if ($conn->query($update_sql)) {
            echo json_encode(['message' => 'Time-out recorded successfully.']);
        } else {
            echo json_encode(['error' => 'Error updating time-out: ' . $conn->error]);
        }
    } else {
        // If both time-in and time-out are recorded, create a new time-in record
        $insert_sql = "INSERT INTO attendances (rfid_uid, time_in) VALUES ('$uid', '$current_time')";
        if ($conn->query($insert_sql)) {
            echo json_encode(['message' => 'Time-in recorded successfully.']);
        } else {
            echo json_encode(['error' => 'Error recording time-in: ' . $conn->error]);
        }
    }
} else {
    // If no attendance record exists for today, create a new time-in record
    $insert_sql = "INSERT INTO attendances (rfid_uid, time_in) VALUES ('$uid', '$current_time')";
    if ($conn->query($insert_sql)) {
        echo json_encode(['message' => 'Time-in recorded successfully.']);
    } else {
        echo json_encode(['error' => 'Error recording time-in: ' . $conn->error]);
    }
}

$conn->close();
?>