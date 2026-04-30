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
        $sql = "SELECT user_id, first_name, last_name, user_name, password 
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

                if ($remember) {
                    setcookie('remember_username', $user_name, time() + (86400 * 30), "/");
                } else {
                    setcookie('remember_username', '', time() - 3600, "/");
                }

                header("Location: dashboard.php");
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

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: url("image/MOON.png") no-repeat center center fixed;
    background-size: cover;
}

/* Dark overlay */
body::before {
    content: "";
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    top: 0;
    left: 0;
    z-index: -1;
}

/* Container */
.container {
    width: 350px;
    margin: 80px auto;
    padding: 20px;
    border-radius: 10px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    text-align: center;
    color: white;
}

/* Inputs */
input {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border-radius: 5px;
    border: none;
}

/* Button */
button {
    width: 100%;
    padding: 10px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* Messages */
.message.error {
    background: red;
    padding: 10px;
    margin-bottom: 10px;
}
.message.success {
    background: green;
    padding: 10px;
    margin-bottom: 10px;
}

/* Password toggle */
.password-wrapper {
    position: relative;
}
.toggle-password {
    position: absolute;
    right: 10px;
    top: 10px;
    cursor: pointer;
}

/* Options */
.options {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    margin: 10px 0;
}
</style>

</head>

<body>

<div class="container">
    <img src="image/RestIQ.png" alt="Logo" style="width:100px;">

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
            <a href="forgot_password.php" style="color:white;">Forgot?</a>
        </div>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php" style="color:white;"><strong>Register</strong></a></p>
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