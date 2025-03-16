<?php
// Set the default timezone
date_default_timezone_set('Asia/Manila'); // Replace with your timezone

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

// Start a transaction
$conn->begin_transaction();

try {
    // Check if the user is registered
    $stmt = $conn->prepare("SELECT * FROM users WHERE rfid_uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows == 0) {
        echo json_encode(['message' => 'User not registered.']);
        exit;
    }

    // Get the current time
    $current_time = date('Y-m-d H:i:s');

    // Fetch the latest attendance record for today
    $stmt = $conn->prepare("SELECT * FROM attendances WHERE rfid_uid = ? AND DATE(time_in) = CURDATE() ORDER BY time_in DESC LIMIT 1");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $attendance_result = $stmt->get_result();

    if ($attendance_result->num_rows > 0) {
        $attendance = $attendance_result->fetch_assoc();

        // Debugging: Log the fetched record
        error_log("Fetched Record: " . print_r($attendance, true));

        // Check if time_out is NULL or empty
        if ($attendance['time_out'] === null || $attendance['time_out'] === '') {
            // Debugging: Log that time-out is being updated
            error_log("Updating time-out for record ID: " . $attendance['id']);

            // Make sure we're not recording time_out too soon after time_in (preventing accidental double-taps)
            $time_in = strtotime($attendance['time_in']);
            $now = strtotime($current_time);
            $diff_seconds = $now - $time_in;

            // Minimum time between check-in and check-out (in seconds), adjust as needed
            $min_time_difference = 30; // 30 seconds

            if ($diff_seconds < $min_time_difference) {
                echo json_encode(['message' => 'Please wait at least ' . $min_time_difference . ' seconds before checking out.']);
            } else {
                // Update time-out
                $stmt = $conn->prepare("UPDATE attendances SET time_out = ? WHERE id = ?");
                $stmt->bind_param("si", $current_time, $attendance['id']);
                if ($stmt->execute()) {
                    echo json_encode(['message' => 'Time-out recorded successfully.']);
                } else {
                    throw new Exception('Error updating time-out: ' . $stmt->error);
                }
            }
        } else {
            // Debugging: Log that a new time-in is being created
            error_log("Creating new time-in record.");

            // If both time-in and time-out are recorded, create a new time-in record
            $stmt = $conn->prepare("INSERT INTO attendances (rfid_uid, time_in) VALUES (?, ?)");
            $stmt->bind_param("ss", $uid, $current_time);
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Time-in recorded successfully.']);
            } else {
                throw new Exception('Error recording time-in: ' . $stmt->error);
            }
        }
    } else {
        // Debugging: Log that no record exists for today
        error_log("No attendance record found for today. Creating new time-in record.");

        // If no attendance record exists for today, create a new time-in record
        $stmt = $conn->prepare("INSERT INTO attendances (rfid_uid, time_in) VALUES (?, ?)");
        $stmt->bind_param("ss", $uid, $current_time);
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Time-in recorded successfully.']);
        } else {
            throw new Exception('Error recording time-in: ' . $stmt->error);
        }
    }

    // Commit the transaction
    $conn->commit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

// Close the connection
$conn->close();
?>