<?php
session_start();
include("connection.php");

// Check if logged in
if (!isset($_SESSION['user_id'])) {
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

    $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, gender = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $first_name, $last_name, $email, $gender_char, $user_id);

    if ($update_stmt->execute()) {
        $success_msg = "Profile updated successfully!";
        $_SESSION['full_name'] = $first_name . ' ' . $last_name;
        $mode = 'view';
    } else {
        $error_msg = "Error updating profile.";
    }
    $update_stmt->close();
}

// Fetch user data
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
    <title>RestIQ - Profile</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <style>
        .profile-card {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .profile-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 3px solid #c084fc;
            padding: 5px;
            background: rgba(255,255,255,0.05);
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            text-align: left;
        }
        .info-row label {
            color: #c084fc;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-row span {
            color: white;
            font-weight: 500;
        }
        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            color: #c084fc;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        input, select {
            width: 100%;
            padding: 12px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: white;
            outline: none;
        }
        input:focus, select:focus { border-color: #c084fc; }

        option {
            background: #2a0a4a;
            color: white;
        }
        .alert {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success { background: rgba(0,255,150,0.1); color: #00ffcc; border: 1px solid rgba(0,255,150,0.2); }
        .alert-error { background: rgba(255,0,0,0.1); color: #ff6b6b; border: 1px solid rgba(255,0,0,0.2); }
    </style>
</head>

<body>
<div class="admin-container">
    <?php include 'sidebar.php'; ?>
    <div class="main-area" style="display: flex; align-items: center; justify-content: center;">
        
        <div class="card profile-card">
            
            <img src="image/<?php echo $avatar; ?>" class="profile-img" alt="Avatar">
            
            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
                <div class="alert alert-error"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <?php if ($mode == 'view'): ?>
                <h2 style="margin-bottom: 5px;"><?php echo htmlspecialchars($full_name ?: 'User'); ?></h2>
                <p style="color: #aaa; margin-bottom: 30px;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>

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

                <div style="margin-top: 30px;">
                    <a href="profile.php?mode=edit" class="btn" style="width: 100%;">✏️ Edit Profile</a>
                </div>

            <?php else: ?>
                <h2 style="margin-bottom: 25px;">✏️ Edit Profile</h2>
                
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

                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" name="update_profile" class="btn" style="flex: 2;">💾 Save Changes</button>
                        <a href="profile.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>

        </div>

    </div>
</div>
</body>
</html>