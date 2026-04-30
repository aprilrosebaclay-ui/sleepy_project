<?php include 'header.php'; ?>
<?php include 'connection.php'; // ← YOUR FILE ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestIQ - Sleep History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
        }
        .main-card { 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
        }
        .sleep-good { background-color: #d4edda !important; }
        .sleep-poor { background-color: #f8d7da !important; }
    </style>
</head>
<body class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="card main-card">
                    <!-- Header -->
                    <div class="card-header bg-gradient text-white text-center py-4" 
                         style="background: linear-gradient(45deg, #667eea, #764ba2);">
                        <h1 class="display-5 mb-2"><i class="fas fa-history me-3"></i>Sleep History</h1>
                        <p class="lead mb-0 opacity-90">Your complete sleep tracking data</p>
                    </div>

                    <!-- Table -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="border-0">ID</th>
                                        <th class="border-0"><i class="fas fa-moon me-1"></i>Sleep</th>
                                        <th class="border-0"><i class="fas fa-sun me-1"></i>Wake</th>
                                        <th class="border-0"><i class="fas fa-clock me-1"></i>Duration</th>
                                        <th class="border-0"><i class="fas fa-star me-1"></i>Quality</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php
// FIXED - Uses YOUR connection.php & YOUR table structure
$result = $conn->query("SELECT * FROM history ORDER BY id DESC");

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hours = floor($row['sleep_duration'] / 60);
        $minutes = $row['sleep_duration'] % 60;
        $isGoodSleep = $row['sleep_duration'] >= 420; // 7+ hours
?>
                                    <tr class="<?php echo $isGoodSleep ? 'sleep-good' : 'sleep-poor'; ?>">
                                        <td><span class="badge bg-primary fs-6">#<?php echo $row['id']; ?></span></td>
                                        <td>
                                            <strong><?php echo date('h:i A', strtotime($row['sleep_time'])); ?></strong>
                                        </td>
                                        <td><?php echo date('h:i A', strtotime($row['wake_time'])); ?></td>
                                        <td>
                                            <i class="fas fa-<?php echo $isGoodSleep ? 'check-circle text-success' : 'exclamation-triangle text-warning'; ?> me-1"></i>
                                            <strong><?php echo $hours; ?>h <?php echo $minutes; ?>m</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['quality_score'] >= 7 ? 'success' : 'warning'; ?> fs-6">
                                                <?php echo $row['quality_score']; ?>/10
                                            </span>
                                        </td>
                                    </tr>
<?php
    }
} else {
?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-bed fa-4x text-muted mb-4 d-block"></i>
                                            <h3 class="text-muted mb-3">No sleep data yet</h3>
                                            <p class="text-muted mb-4">Start tracking your sleep patterns</p>
                                            <a href="predict.php" class="btn btn-primary btn-lg px-4">
                                                <i class="fas fa-plus me-2"></i>First Entry
                                            </a>
                                        </td>
                                    </tr>
<?php
}
?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-light border-0 text-center py-4">
                        <div class="btn-group" role="group">
                            <a href="dashboard.php" class="btn btn-outline-primary">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                            <a href="predict.php" class="btn btn-success">
                                <i class="fas fa-magic me-2"></i>Predict Sleep
                            </a>
                            <a href="profile.php" class="btn btn-outline-secondary">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>