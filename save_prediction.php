<?php
session_start();

// ADMIN CANNOT SAVE PREDICTIONS
if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_POST) {
    $user_id = 1; // Replace with $_SESSION['user_id']
    $sleep_time = $_POST['sleep_time'];
    $wake_time = $_POST['wake_time'];
    
    // Calculate sleep duration in minutes
    $sleep_timestamp = strtotime($sleep_time);
    $wake_timestamp = strtotime($wake_time);
    
    // Handle overnight sleep
    if ($wake_timestamp <= $sleep_timestamp) {
        $wake_timestamp += 86400; // Add 24 hours
    }
    
    $sleep_duration = ($wake_timestamp - $sleep_timestamp) / 60;
    
    // Save to database
    $sql = "INSERT INTO history (user_id, sleep_time, wake_time, sleep_duration, recorded_date) 
            VALUES (?, ?, ?, ?, CURDATE())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $user_id, $sleep_time, $wake_time, $sleep_duration);
    
    if ($stmt->execute()) {
        header("Location: predict.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>