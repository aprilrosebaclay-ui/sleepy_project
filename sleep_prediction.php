<?php
include 'connection.php';

$result = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize inputs
    $workout  = floatval($_POST['workout'] ?? 0);
    $reading  = floatval($_POST['reading'] ?? 0);
    $phone    = floatval($_POST['phone'] ?? 0);
    $work     = floatval($_POST['work'] ?? 0);
    $caffeine = floatval($_POST['caffeine'] ?? 0);
    $relax    = floatval($_POST['relax'] ?? 0);

    // FULL PYTHON PATH + SCRIPT PATH
    $pythonPath = "C:\Users\ROSE\AppData\Local\Microsoft\WindowsApps\PythonSoftwareFoundation.Python.3.13_qbz5n2kfra8p0\python.exe";
    $scriptPath = "sleep_api\app.py";

    // Run Python command
    $command = "\"$pythonPath\" \"$scriptPath\" $workout $reading $phone $work $caffeine $relax 2>&1";
    $output = shell_exec($command);

    // Process output
    if ($output !== null) {
        $cleanOutput = trim($output);

        if (is_numeric($cleanOutput)) {
            $result = $cleanOutput;

            // Save valid prediction
            $stmt = $conn->prepare("
                INSERT INTO history 
                (workout, reading, phone, work_hours, caffeine, relaxation, predicted_sleep)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "ddddddd",
                $workout,
                $reading,
                $phone,
                $work,
                $caffeine,
                $relax,
                $result
            );

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
    <title>Sleep Predictor</title>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #120024, #2a0a4a, #3b1d6b);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
            margin: 0;
        }

        .container {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(15px);
            padding: 35px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-size: 13px;
            opacity: 0.8;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 12px;
            border-radius: 8px;
            border: none;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #6a2cd8, #9333ea);
            border: none;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            transform: scale(1.05);
        }

        .result {
            margin-top: 20px;
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            word-wrap: break-word;
        }

        .success {
            background: rgba(0,255,150,0.1);
            color: #00ffcc;
        }

        .error {
            background: rgba(255,0,0,0.1);
            color: #ff6b6b;
            font-size: 13px;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>🌙 Sleep Predictor</h2>

    <form method="POST">

        <label>Workout (hrs)</label>
        <input type="number" step="any" name="workout" required>

        <label>Reading (hrs)</label>
        <input type="number" step="any" name="reading" required>

        <label>Phone Usage (hrs)</label>
        <input type="number" step="any" name="phone" required>

        <label>Work Hours (hrs)</label>
        <input type="number" step="any" name="work" required>

        <label>Caffeine (mg)</label>
        <input type="number" step="any" name="caffeine" required>

        <label>Relaxation (hrs)</label>
        <input type="number" step="any" name="relax" required>

        <button type="submit">Predict Sleep</button>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <div class="result <?php echo is_numeric($result) ? 'success' : 'error'; ?>">
            <?php
            if (is_numeric($result)) {
                echo "🛌 Predicted Sleep:<br><strong style='font-size:24px;'>$result hrs</strong>";
            } else {
                echo $error;
            }
            ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>