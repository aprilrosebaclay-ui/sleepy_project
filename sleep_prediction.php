<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'connection.php';

$userName = $_SESSION['full_name'] ?? "User";
$firstName = explode(' ', $userName)[0];

$result = "";
$error = "";

$workout  = $_POST['workout'] ?? '';
$reading  = $_POST['reading'] ?? '';
$phone    = $_POST['phone'] ?? '';
$work     = $_POST['work'] ?? '';
$caffeine = $_POST['caffeine'] ?? '';
$relax    = $_POST['relax'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $workout_val  = floatval($workout);
    $reading_val  = floatval($reading);
    $phone_val    = floatval($phone);
    $work_val     = floatval($work);
    $caffeine_val = floatval($caffeine);
    $relax_val    = floatval($relax);

    $pythonPath = 'C:\Users\torre\AppData\Local\Programs\Python\Python311\python.exe';
    $scriptPath = __DIR__ . '\sleep_api\app.py';

    $command = "\"$pythonPath\" \"$scriptPath\" $workout_val $reading_val $phone_val $work_val $caffeine_val $relax_val 2>&1";
    $output = shell_exec($command);

    if ($output !== null) {
        $cleanOutput = trim($output);
        if (is_numeric($cleanOutput)) {
            $result = $cleanOutput;
            $stmt = $conn->prepare("INSERT INTO history (workout, reading, phone, work_hours, caffeine, relaxation, predicted_sleep) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ddddddd", $workout_val, $reading_val, $phone_val, $work_val, $caffeine_val, $relax_val, $result);
            $stmt->execute();
        } else {
            $error = "Python Error: " . $cleanOutput;
        }
    } else {
        $error = "No response from Python script.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>RestIQ - Predict Sleep</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <style>
        .predict-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-size: 13px;
            color: #c084fc;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        input {
            width: 100%;
            padding: 12px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: white;
            outline: none;
        }
        input:focus {
            border-color: #c084fc;
        }
        .result-box {
            margin-top: 25px;
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            background: rgba(0,255,150,0.05);
            border: 1px solid rgba(0,255,150,0.2);
        }
        .error-box {
            margin-top: 25px;
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            background: rgba(255,0,0,0.05);
            border: 1px solid rgba(255,0,0,0.2);
            color: #ff6b6b;
        }
    </style>
</head>

<body>
<div class="admin-container">
    <?php include 'sidebar.php'; ?>
    <div class="main-area" style="display: flex; align-items: center; justify-content: center;">
        <div class="card predict-container">
            <h2 style="text-align: center; margin-bottom: 25px;">🧠 Sleep Predictor</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Workout (hrs)</label>
                    <input type="number" step="any" name="workout" value="<?php echo htmlspecialchars($workout); ?>" required>
                </div>
                <div class="form-group">
                    <label>Reading (hrs)</label>
                    <input type="number" step="any" name="reading" value="<?php echo htmlspecialchars($reading); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Usage (hrs)</label>
                    <input type="number" step="any" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
                <div class="form-group">
                    <label>Work Hours (hrs)</label>
                    <input type="number" step="any" name="work" value="<?php echo htmlspecialchars($work); ?>" required>
                </div>
                <div class="form-group">
                    <label>Caffeine (mg)</label>
                    <input type="number" step="any" name="caffeine" value="<?php echo htmlspecialchars($caffeine); ?>" required>
                </div>
                <div class="form-group">
                    <label>Relaxation (hrs)</label>
                    <input type="number" step="any" name="relax" value="<?php echo htmlspecialchars($relax); ?>" required>
                </div>

                <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                    <a href="sleep_prediction.php" class="btn" style="width: 100%; background: #4b5563;">Run Another Prediction</a>
                <?php else: ?>
                    <button type="submit" class="btn" style="width: 100%;">Predict Sleep</button>
                <?php endif; ?>
            </form>

            <?php if ($result !== ""): ?>
                <div class="result-box">
                    <p style="color: #00ffcc; font-size: 14px;">🛌 Predicted Sleep:</p>
                    <strong style="font-size: 28px; color: white;"><?php echo number_format($result, 2); ?> hrs</strong>
                </div>
            <?php elseif ($error !== ""): ?>
                <div class="error-box"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>