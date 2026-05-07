<?php
session_start();
include 'connection.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    header("Location: myadmin.php");
    exit();
}

// Fetch user details
$user_query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Fetch user history
$history_query = "SELECT * FROM predictions WHERE user_id = ? ORDER BY prediction_date DESC";
$h_stmt = $conn->prepare($history_query);
$h_stmt->bind_param("i", $user_id);
$h_stmt->execute();
$history_result = $h_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestIQ | User Details</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <link rel="stylesheet" href="CSS/pages/view-user.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-container">
    <?php include 'sidebar.php'; ?>

    <div class="main-area">
        <a href="myadmin.php" class="back-btn">← Back to User Management</a>

        <div class="profile-header">
            <div class="avatar-large"><?php echo strtoupper(substr($user['first_name'], 0, 1)); ?></div>
            <div>
                <h1 class="profile-name"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h1>
                <p class="profile-meta">User ID: #<?php echo $user['user_id']; ?> • Joined <?php echo date('M d, Y', strtotime($user['timestamp'])); ?></p>
            </div>
        </div>

        <div class="info-grid">
            <div class="card">
                <h2 class="section-title">👤 Profile Details</h2>
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?php echo $user['first_name'] . ' ' . ($user['middle_name'] ? $user['middle_name'] . ' ' : '') . $user['last_name']; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?php echo $user['email']; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Username</div>
                    <div class="info-value">@<?php echo $user['user_name']; ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="info-value"><?php echo $user['gender'] == 'M' ? 'Male' : ($user['gender'] == 'F' ? 'Female' : 'Other'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Birthday</div>
                    <div class="info-value"><?php echo date('F j, Y', strtotime($user['b_day'])); ?></div>
                </div>
            </div>

            <div class="card">
                <h2 class="section-title">📊 Sleep History</h2>
                <div style="overflow-x: auto;">
                    <?php if ($history_result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sleep Target</th>
                                <th>Prediction</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $history_result->fetch_assoc()): ?>
                            <tr>
                                <td class="history-date"><?php echo date('M d, Y', strtotime($row['prediction_date'])); ?></td>
                                <td class="sleep-hours"><?php echo $row['sleep_hours']; ?> hrs</td>
                                <td><span class="sleep-badge"><?php echo $row['predicted_category']; ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: #555;">
                        <p>No sleep prediction history found for this user.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
