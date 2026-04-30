<?php
include "connection.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $middle_name = mysqli_real_escape_string($conn, trim($_POST['middle_name'] ?? ''));
    $b_day = trim($_POST['b_day'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $user_name = mysqli_real_escape_string($conn, trim($_POST['user_name'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    $errors = array();
    
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($b_day)) $errors[] = "Birth date is required";
    if (empty($gender)) $errors[] = "Gender is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($user_name)) $errors[] = "Username is required";
    if (empty($password)) $errors[] = "Password is required";
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (!empty($password) && strlen($password) < 4) {
        $errors[] = "Password must be at least 4 characters";
    }

    if (empty($errors)) {

        if ($gender == "Male") $gender_db = "M";
        elseif ($gender == "Female") $gender_db = "F";
        else $gender_db = "O";
        
        $check_sql = "SELECT user_id FROM users WHERE user_name = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $user_name, $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $error = "Username or Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (first_name, last_name, middle_name, b_day, gender, email, user_name, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $first_name, $last_name, $middle_name, $b_day, $gender_db, $email, $user_name, $hashed_password);
            
            if ($stmt->execute()) {
                header("Location: login.php?registered=success");
                exit();
            } else {
                $error = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else {
        $error = implode(", ", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - RestIQ</title>

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

/* Container styling */
.container {
    width: 350px;
    margin: 50px auto;
    padding: 20px;
    border-radius: 10px;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    text-align: center;
    color: white;
}

/* Inputs */
input, select {
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
</style>

</head>

<body>

<div class="container">
    <img src="image/RestIQ.png" alt="Logo" style="width:100px;">

    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <h2>Registration Form</h2>

    <form action="register.php" method="POST" onsubmit="return validateForm()">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="text" name="middle_name" placeholder="Middle Name">
        <input type="date" name="b_day" required>

        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="user_name" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" id="confirm_password" placeholder="Confirm Password" required>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php" style="color:white;">Login</a></p>
</div>

<script>
function validateForm() {
    var password = document.querySelector('input[name="password"]').value;
    var confirm = document.getElementById('confirm_password').value;

    if (password != confirm) {
        alert("Passwords do not match!");
        return false;
    }

    if (password.length < 4) {
        alert("Password must be at least 4 characters!");
        return false;
    }

    return true;
}
</script>

</body>
</html>