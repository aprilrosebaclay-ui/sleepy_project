<?php
session_start();
include("connection.php");

// CHECK ADMIN ROLE - Admin can only access their own admin profile
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";
$mode = isset($_GET['mode']) && $_GET['mode'] == 'edit' ? 'edit' : 'view';

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $gender_input = $_POST['gender'];

    // Map gender to single character to fit varchar(1)
    $gender_map = ['Male' => 'M', 'Female' => 'F', 'Other' => 'O'];
    $gender_char = $gender_map[$gender_input] ?? 'O';

    $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, gender = ? WHERE user_id = ? AND role = 'admin'";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $first_name, $last_name, $email, $gender_char, $user_id);

    if ($update_stmt->execute()) {
        $success_msg = "Admin profile updated successfully!";
        $_SESSION['full_name'] = $first_name . ' ' . $last_name;
        $mode = 'view';
    } else {
        $error_msg = "Error updating profile.";
    }
    $update_stmt->close();
}

// Fetch ADMIN user data only
$sql = "SELECT * FROM users WHERE user_id = ? AND role = 'admin' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Admin profile not found or access denied.");
}

$user = $result->fetch_assoc();
$stmt->close();

$full_name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$avatar = "Maleicon.png";
$gender_db = strtoupper(trim($user['gender'] ?? ''));

if (in_array($gender_db, ['F', 'FEMALE'])) {
    $avatar = "Femaleicon.png";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RestIQ - Admin Profile</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <link rel="stylesheet" href="CSS/pages/profile.css">
</head>

<body>
<div class="admin-container">
    <?php include 'admin_sidebar.php'; ?>
    <div class="main-area profile-main-area">
        
        <div class="card profile-card">
            
            <img src="image/<?php echo $avatar; ?>" class="profile-img" alt="Avatar">
            
            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div class="alert alert-error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <?php if ($mode == 'view'): ?>
                <h2 class="profile-title"><?php echo htmlspecialchars($full_name ?: 'Administrator'); ?></h2>
                <p class="profile-subtitle"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>

                <div class="info-row">
                    <label>Username</label>
                    <span><?php echo htmlspecialchars($user['user_name'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-row">
                    <label>First Name</label>
                    <span><?php echo htmlspecialchars($user['first_name'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-row">
                    <label>Last Name</label>
                    <span><?php echo htmlspecialchars($user['last_name'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-row">
                    <label>Gender</label>
                    <span>
                        <?php 
                        if ($gender_db == 'M') echo 'Male';
                        elseif ($gender_db == 'F') echo 'Female';
                        else echo 'Other';
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <label>Account Type</label>
                    <span>🛡️ Administrator</span>
                </div>

                <div class="profile-actions">
                    <a href="admin_profile.php?mode=edit" class="btn btn-full">✏️ Edit Profile</a>
                </div>

            <?php else: ?>
                <h2 class="profile-title profile-title-edit">✏️ Edit Profile</h2>
                
                <form method="POST">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="Male" <?php echo ($gender_db == 'M') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($gender_db == 'F') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($gender_db == 'O') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="profile-form-actions">
                        <button type="submit" name="update_profile" class="btn btn-primary-flex">💾 Save Changes</button>
                        <a href="admin_profile.php" class="btn btn-secondary btn-secondary-flex">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
