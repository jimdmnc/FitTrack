<?php
date_default_timezone_set('Asia/Manila');

$host = 'localhost';
$dbname = 'fittrack_db';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uid = $_POST['uid'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE rfid_uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows == 0) {
        echo json_encode(['message' => 'User not registered.']);
        exit;
    }

    $user = $user_result->fetch_assoc();
    $full_name = $user['first_name'] . ' ' . $user['last_name']; // Concatenating first and last name

    if ($user['member_status'] === 'expired') {
        echo json_encode(['message' => 'Membership expired! Attendance not recorded.']);
        exit;
    }

    $current_time = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("SELECT * FROM attendances WHERE rfid_uid = ? AND DATE(time_in) = CURDATE() ORDER BY time_in DESC LIMIT 1");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $attendance_result = $stmt->get_result();

    if ($attendance_result->num_rows > 0) {
        $attendance = $attendance_result->fetch_assoc();

        if ($attendance['time_out'] === null || $attendance['time_out'] === '') {
            $time_in = strtotime($attendance['time_in']);
            $now = strtotime($current_time);
            $diff_seconds = $now - $time_in;
            $min_time_difference = 30; 

            if ($diff_seconds < $min_time_difference) {
                echo json_encode(['message' => 'Please wait at least ' . $min_time_difference . ' seconds before checking out.']);
            } else {
                $stmt = $conn->prepare("UPDATE attendances SET time_out = ? WHERE id = ?");
                $stmt->bind_param("si", $current_time, $attendance['id']);
                if ($stmt->execute()) {
                    echo json_encode([
                        'message' => 'Time-out recorded successfully.',
                        'name' => $full_name
                    ]);
                } else {
                    throw new Exception('Error updating time-out: ' . $stmt->error);
                }
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO attendances (rfid_uid, time_in) VALUES (?, ?)");
            $stmt->bind_param("ss", $uid, $current_time);
            if ($stmt->execute()) {
                echo json_encode([
                    'message' => 'Time-in recorded successfully.',
                    'name' => $full_name
                ]);
            } else {
                throw new Exception('Error recording time-in: ' . $stmt->error);
            }
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO attendances (rfid_uid, time_in) VALUES (?, ?)");
        $stmt->bind_param("ss", $uid, $current_time);
        if ($stmt->execute()) {
            echo json_encode([
                'message' => 'Time-in recorded successfully.',
                'name' => $full_name
            ]);
        } else {
            throw new Exception('Error recording time-in: ' . $stmt->error);
        }
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
