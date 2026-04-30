<?php
session_start();
include("connection.php");

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$stmt->close();

// Build full name
$full_name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

// Gender image
$avatar = "Maleicon.png";

$gender = strtolower(trim($user['gender'] ?? ''));

if (in_array($gender, ['female', 'f', 'woman', 'girl'])) {
    $avatar = "Femaleicon.png";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - RestIQ</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">

    <div class="sidebar">
        <h2>RestIQ</h2>
        <a href="dashboard.php" class="menu-item">Dashboard</a>
        <a href="profile.php" class="menu-item active">Profile</a>
        <a href="sleep_prediction.php" class="menu-item">Sleep Prediction</a>
        <a href="logout.php" class="menu-item">Logout</a>
    </div>

    <div class="main-area">
        <div class="profile-wrapper">

            <div class="profile-card">

                <div class="profile-header">
                    <img src="image/<?php echo $avatar; ?>" class="profile-img" alt="Profile">

                    <h2 class="profile-name">
                        <?php echo htmlspecialchars($full_name ?: 'User'); ?>
                    </h2>

                    <p class="profile-subtitle">
                        Member
                    </p>
                </div>

                <div class="profile-grid">

                    <div class="info-card">
                        <span>Full Name</span>
                        <h4><?php echo htmlspecialchars($full_name ?: 'N/A'); ?></h4>
                    </div>

                    <div class="info-card">
                        <span>Username</span>
                        <h4><?php echo htmlspecialchars($user['user_name'] ?? 'N/A'); ?></h4>
                    </div>

                    <div class="info-card">
                        <span>Email</span>
                        <h4><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></h4>
                    </div>

                    <div class="info-card">
                        <span>Status</span>
                        <h4>Active</h4>
                    </div>

                </div>

                <div class="profile-actions">
                    <button class="edit-btn">Edit Profile</button>
                </div>

            </div>

        </div>
    </div>

</div>

</body>
</html>