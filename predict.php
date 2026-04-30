<?php
include 'header.php';
include 'connection.php';

session_start();

// Use real logged-in user if available
$user_id = $_SESSION['user_id'] ?? 1;

// Get user's sleep pattern
$pattern_sql = "
    SELECT 
        AVG(sleep_duration) AS avg_duration,
        AVG(HOUR(sleep_time)) AS avg_sleep_hour,
        AVG(quality_score) AS avg_quality
    FROM history
    WHERE user_id = ?
";

$pattern_stmt = $conn->prepare($pattern_sql);
$pattern_stmt->bind_param("i", $user_id);
$pattern_stmt->execute();
$pattern = $pattern_stmt->get_result()->fetch_assoc();

// Prediction function
function generatePrediction($pattern) {
    $avg_duration   = !empty($pattern['avg_duration']) ? $pattern['avg_duration'] : 480; // 8h
    $avg_sleep_hour = isset($pattern['avg_sleep_hour']) ? round($pattern['avg_sleep_hour']) : 22; // 10PM
    $avg_quality    = !empty($pattern['avg_quality']) ? $pattern['avg_quality'] : 7;

    // Clamp values
    $avg_sleep_hour = max(0, min(23, $avg_sleep_hour));
    $avg_quality = max(1, min(10, $avg_quality));

    $optimal_sleep = sprintf('%02d:00:00', $avg_sleep_hour);

    $duration_hours = floor($avg_duration / 60);
    $duration_minutes = $avg_duration % 60;

    $optimal_wake = date(
        'H:i:s',
        strtotime($optimal_sleep . " + $duration_hours hours + $duration_minutes minutes")
    );

    $confidence = min(95, max(60, 60 + ($avg_quality * 3)));

    return [
        'sleep_time' => date('h:i A', strtotime($optimal_sleep)),
        'wake_time' => date('h:i A', strtotime($optimal_wake)),
        'duration' => round($avg_duration),
        'confidence' => round($confidence),
        'quality_prediction' => min(10, round($avg_quality + 1))
    ];
}

$prediction = generatePrediction($pattern);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Predict Sleep - RestIQ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .predict-card {
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .prediction-display {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card predict-card">

                <div class="card-header text-center py-4 prediction-display">
                    <h1 class="mb-0">
                        <i class="fas fa-brain me-3"></i>Sleep Recommendation
                    </h1>
                    <p class="mb-0">Based on your recorded sleep history</p>
                </div>

                <div class="card-body p-5 text-center">

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="p-4 bg-dark text-white rounded-4">
                                <i class="fas fa-moon fa-2x mb-3 text-info"></i>
                                <h3>Recommended Sleep Time</h3>
                                <h2 class="fw-bold"><?php echo $prediction['sleep_time']; ?></h2>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-4 bg-warning text-dark rounded-4">
                                <i class="fas fa-sun fa-2x mb-3"></i>
                                <h3>Recommended Wake Time</h3>
                                <h2 class="fw-bold"><?php echo $prediction['wake_time']; ?></h2>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <h5><i class="fas fa-clock me-2 text-primary"></i>Duration</h5>
                            <h4 class="text-success">
                                <?php echo floor($prediction['duration']/60); ?>h
                                <?php echo $prediction['duration']%60; ?>m
                            </h4>
                        </div>

                        <div class="col-md-4">
                            <h5><i class="fas fa-star me-2 text-warning"></i>Quality</h5>
                            <span class="badge fs-4 bg-success">
                                <?php echo $prediction['quality_prediction']; ?>/10
                            </span>
                        </div>

                        <div class="col-md-4">
                            <h5><i class="fas fa-chart-line me-2 text-info"></i>Confidence</h5>
                            <div class="progress" style="height:25px;">
                                <div class="progress-bar bg-success"
                                     style="width: <?php echo $prediction['confidence']; ?>%;">
                                    <?php echo $prediction['confidence']; ?>%
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-5">

                    <h4 class="mb-4">Record Tonight's Sleep</h4>

                    <form action="save_prediction.php" method="POST" class="row g-3 justify-content-center">
                        <div class="col-md-4">
                            <label class="form-label">Sleep Time</label>
                            <input type="time" class="form-control form-control-lg" name="sleep_time" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Wake Time</label>
                            <input type="time" class="form-control form-control-lg" name="wake_time" required>
                        </div>

                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Save Record
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center py-4">
                    <a href="history.php" class="btn btn-outline-primary btn-lg me-3">
                        <i class="fas fa-list me-2"></i>View History
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</body>
</html>