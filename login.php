<?php
session_start();
include "connection.php";

$error = "";
$success = "";
$remembered_username = "";

// Success message
if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
    $success = "✅ Registration successful! Please login.";
}

// Remember me
if (isset($_COOKIE['remember_username'])) {
    $remembered_username = htmlspecialchars($_COOKIE['remember_username']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = trim($_POST['user_name'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    if (empty($user_name) || empty($password)) {
        $error = "Please enter username and password.";
    } else {
        $sql = "SELECT user_id, first_name, last_name, user_name, password, role 
                FROM users WHERE user_name = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user_name, $user_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['full_name'] = $user['first_name'] . " " . $user['last_name'];
                $_SESSION['role'] = $user['role'];

                if ($remember) {
                    setcookie('remember_username', $user_name, time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_username', '', time() - 3600, "/");
                }

                if ($_SESSION['role'] === 'admin') {
                    header("Location: myadmin.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "User not found!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - RestIQ</title>
<link rel="stylesheet" href="CSS/pages/auth.css">

</head>

<body>

<div class="container">
    <img src="image/RestIQ.png" alt="Logo" class="logo">

    <?php if ($success): ?>
        <div class="message success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?php echo $error; ?></div>
    <?php endif; ?>

    <h2>Welcome Back!</h2>

    <form action="login.php" method="POST" id="loginFormElement">
        <input type="text" name="user_name" placeholder="Username or Email" 
               value="<?php echo $remembered_username; ?>" required>

        <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <div class="options">
            <label>
                <input type="checkbox" name="remember" <?php echo $remembered_username ? 'checked' : ''; ?>>
                Remember me
            </label>
            <a href="forgot_password.php" class="auth-link">Forgot?</a>
        </div>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php" class="auth-link"><strong>Register</strong></a></p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}

// Loading effect
document.getElementById('loginFormElement').addEventListener('submit', function() {
    const btn = document.querySelector('button');
    btn.textContent = "Logging in...";
    btn.disabled = true;
});
</script>

</body>
</html>