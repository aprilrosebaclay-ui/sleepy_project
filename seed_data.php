<?php
/**
 * RestIQ Database Seeder - Generate Realistic Demo Data
 * Run this file once to populate the database with sample data
 * Access: http://localhost/sleepy_project/seed_data.php
 */

include 'connection.php';

// Stop execution if this has already been run
$check_seed = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$user_count = mysqli_fetch_assoc($check_seed)['total'];

if ($user_count > 0) {
    die("<h2 style='color: #ff6b6b; text-align: center; margin-top: 50px;'>⚠️ Database already contains user data. Seeding aborted to prevent duplication.</h2>");
}

// Disable foreign key checks temporarily
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

// Clear existing data (optional)
// mysqli_query($conn, "TRUNCATE TABLE predictions");
// mysqli_query($conn, "DELETE FROM users WHERE role = 'user'");

$errors = [];
$success = [];

// 1. CREATE ADMIN USER (if not exists)
$admin_check = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
$admin_count = mysqli_fetch_assoc($admin_check)['total'];

if ($admin_count == 0) {
    $admin_pass = password_hash('admin123', PASSWORD_BCRYPT);
    $admin_sql = "INSERT INTO users (first_name, last_name, email, user_name, password, gender, role, timestamp) 
                  VALUES ('Admin', 'User', 'admin@restiq.com', 'admin', ?, 'M', 'admin', NOW())";
    $stmt = $conn->prepare($admin_sql);
    $stmt->bind_param("s", $admin_pass);
    if ($stmt->execute()) {
        $success[] = "✅ Admin user created";
    } else {
        $errors[] = "❌ Failed to create admin user";
    }
}

// 2. CREATE SAMPLE USERS (100 users with realistic names)
$first_names = ['Alex', 'Jordan', 'Sam', 'Casey', 'Morgan', 'Taylor', 'Riley', 'Avery', 'Quinn', 'Parker',
                'Jamie', 'Skylar', 'River', 'Phoenix', 'Sage', 'Dakota', 'Cameron', 'Blake', 'Dominic', 'Evan',
                'Liam', 'Noah', 'Oliver', 'Elijah', 'James', 'Benjamin', 'Lucas', 'Henry', 'Mason', 'Michael',
                'Emma', 'Olivia', 'Ava', 'Isabella', 'Mia', 'Charlotte', 'Amelia', 'Harper', 'Evelyn', 'Abigail',
                'Sophia', 'Elizabeth', 'Emily', 'Avery', 'Ella', 'Scarlett', 'Victoria', 'Grace', 'Chloe', 'Penelope'];

$last_names = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Martinez', 'Davis', 'Rodriguez', 'Miller',
               'Wilson', 'Moore', 'Taylor', 'Anderson', 'Thomas', 'Jackson', 'White', 'Harris', 'Martin', 'Thompson',
               'Lee', 'Perez', 'Edwards', 'Collins', 'Reyes', 'Stewart', 'Morris', 'Morales', 'Murphy', 'Rogers'];

$genders = ['M', 'F', 'O'];

for ($i = 0; $i < 100; $i++) {
    $fname = $first_names[array_rand($first_names)];
    $lname = $last_names[array_rand($last_names)];
    $email = strtolower($fname . '.' . $lname . rand(1, 999) . '@email.com');
    $username = strtolower($fname . substr($lname, 0, 3) . rand(100, 999));
    $password = password_hash('password123', PASSWORD_BCRYPT);
    $gender = $genders[array_rand($genders)];
    
    // Random timestamp within last 30 days
    $timestamp = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days'));
    
    $sql = "INSERT INTO users (first_name, last_name, email, user_name, password, gender, role, timestamp) 
            VALUES (?, ?, ?, ?, ?, ?, 'user', ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $fname, $lname, $email, $username, $password, $gender, $timestamp);
    $stmt->execute();
    $stmt->close();
}
$success[] = "✅ Created 100 sample users";

// 3. CREATE SAMPLE PREDICTIONS (500+ predictions with realistic data)
$sleep_categories = ['Normal', 'Light', 'Deep', 'Insomnia', 'REM', 'Restless'];
$sleep_hours_range = [4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8, 8.5, 9, 9.5, 10];

// Get all user IDs
$users = mysqli_query($conn, "SELECT user_id FROM users WHERE role = 'user' LIMIT 100");
$user_ids = [];
while ($user = mysqli_fetch_assoc($users)) {
    $user_ids[] = $user['user_id'];
}

// Create 5-8 predictions per user (500-800 total)
$prediction_count = 0;
foreach ($user_ids as $user_id) {
    $num_predictions = rand(5, 8);
    
    for ($j = 0; $j < $num_predictions; $j++) {
        $category = $sleep_categories[array_rand($sleep_categories)];
        $sleep_hours = $sleep_hours_range[array_rand($sleep_hours_range)];
        
        // Prediction timestamp: last 30 days
        $pred_date = date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days ' . rand(0, 23) . ' hours'));
        
        $pred_sql = "INSERT INTO predictions (user_id, predicted_category, sleep_hours, prediction_date) 
                     VALUES (?, ?, ?, ?)";
        $pred_stmt = $conn->prepare($pred_sql);
        $pred_stmt->bind_param("isds", $user_id, $category, $sleep_hours, $pred_date);
        $pred_stmt->execute();
        $pred_stmt->close();
        $prediction_count++;
    }
}
$success[] = "✅ Created $prediction_count realistic predictions";

// Re-enable foreign key checks
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

// Calculate statistics for display
$final_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];
$final_predictions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM predictions"))['count'];
$avg_sleep = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(sleep_hours) as avg FROM predictions"))['avg'], 1);
$top_category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT predicted_category, COUNT(*) as count FROM predictions GROUP BY predicted_category ORDER BY count DESC LIMIT 1"))['predicted_category'];

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestIQ - Database Seeder</title>
    <link rel="stylesheet" href="CSS/pages/seed-data.css">
</head>
<body>

<div class="container">
    <h1>🌙 RestIQ Seeder</h1>
    <p class="subtitle">Database Population Tool</p>
    
    <div class="completion-badge">✓ SEEDING COMPLETE</div>
    
    <div class="messages">
        <?php
        foreach ($success as $msg) {
            echo "<div class='message success'>$msg</div>";
        }
        foreach ($errors as $msg) {
            echo "<div class='message error'>$msg</div>";
        }
        ?>
    </div>
    
    <div class="stats">
        <div class="stat-row">
            <span class="stat-label">👥 Total Users</span>
            <span class="stat-value"><?php echo $final_users; ?></span>
        </div>
        <div class="stat-row">
            <span class="stat-label">🧠 Total Predictions</span>
            <span class="stat-value"><?php echo $final_predictions; ?></span>
        </div>
        <div class="stat-row">
            <span class="stat-label">😴 Avg Sleep Hours</span>
            <span class="stat-value"><?php echo $avg_sleep; ?>h</span>
        </div>
        <div class="stat-row">
            <span class="stat-label">🌓 Top Category</span>
            <span class="stat-value"><?php echo $top_category; ?></span>
        </div>
    </div>
    
    <div class="action-buttons">
        <a href="login.php" class="btn btn-primary">🔐 Login to Dashboard</a>
        <a href="index.php" class="btn btn-secondary">🏠 Home</a>
    </div>
</div>

</body>
</html>
