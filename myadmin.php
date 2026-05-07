<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_full_name = $_SESSION['full_name'] ?? 'Administrator';
$firstName = explode(' ', $admin_full_name)[0];

$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users WHERE role = 'user'"))['t'] ?? 0;
$totalPredictions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM history"))['t'] ?? 0;
$avgSleep = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(predicted_sleep) as a FROM history"))['a'] ?? 0, 1);
$maxSleep = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(predicted_sleep) as m FROM history"))['m'] ?? 0, 1);
$avgCaffeine = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(caffeine) as c FROM history"))['c'] ?? 0, 0);
$avgPhone = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(phone) as p FROM history"))['p'] ?? 0, 1);

$recentPredictions = mysqli_query($conn, "SELECT * FROM history ORDER BY id DESC LIMIT 10");
$chartDataQuery = mysqli_query($conn, "SELECT predicted_sleep, id FROM (SELECT predicted_sleep, id FROM history ORDER BY id DESC LIMIT 10) as sub ORDER BY id ASC");
$chartLabels = [];
$chartValues = [];
while ($row = mysqli_fetch_assoc($chartDataQuery)) {
    $chartLabels[] = '#' . $row['id'];
    $chartValues[] = $row['predicted_sleep'];
}

$platformEnergyEntry = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(predicted_sleep) as avg_s FROM (SELECT predicted_sleep FROM history ORDER BY id DESC LIMIT 50) as recent_50"));
$platformAvgSleep = $platformEnergyEntry['avg_s'] ?? 0;
$energyLevel = 0;
$energyStatus = 'No data yet';

if ($platformAvgSleep >= 8) {
    $energyLevel = rand(92, 98);
    $energyStatus = 'Community Well-Rested';
} elseif ($platformAvgSleep >= 7) {
    $energyLevel = rand(80, 90);
    $energyStatus = 'Ready for the day';
} elseif ($platformAvgSleep >= 6) {
    $energyLevel = rand(60, 75);
    $energyStatus = 'Community Average';
} elseif ($platformAvgSleep > 0) {
    $energyLevel = rand(30, 50);
    $energyStatus = 'Community Low Energy';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestIQ | Admin Dashboard</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <link rel="stylesheet" href="CSS/pages/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="admin-container">
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-area">
        <header class="admin-header">
            <div>
                <h1 class="page-title">
                    Admin Console
                </h1>
                <p class="page-subtitle">Welcome, <?php echo htmlspecialchars($firstName); ?>. Viewing overall platform performance.</p>
            </div>
            <div class="page-meta">
                <div class="page-meta-main"><?php echo date('l, F d'); ?></div>
                <div class="page-meta-sub">System Online • RestIQ Admin v1.0</div>
            </div>
        </header>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4>👥 Total Users</h4>
                <div class="value"><?php echo $totalUsers; ?></div>
                <div class="trend trend-good">Active Base</div>
            </div>
            <div class="stat-card">
                <h4>🛌 Avg Platform Sleep</h4>
                <div class="value"><?php echo $avgSleep; ?> <span class="stat-unit">hrs</span></div>
                <div class="trend <?php echo ($avgSleep >= 7) ? 'trend-good' : 'trend-warning'; ?>">
                    <?php echo ($avgSleep >= 7) ? 'Healthy Community' : 'Low Community Avg'; ?>
                </div>
            </div>
            <div class="stat-card">
                <h4>☕ Platform Caffeine</h4>
                <div class="value"><?php echo $avgCaffeine; ?> <span class="stat-unit">mg</span></div>
                <div class="trend trend-muted">Community Average</div>
            </div>
            <div class="stat-card">
                <h4>📱 Platform Phone</h4>
                <div class="value"><?php echo $avgPhone; ?> <span class="stat-unit">hrs</span></div>
                <div class="trend <?php echo ($avgPhone > 3) ? 'trend-warning' : 'trend-good'; ?>">
                    <?php echo ($avgPhone > 3) ? 'High usage' : 'Healthy usage'; ?>
                </div>
            </div>
        </div>

        <!-- MIDDLE SECTION: CHART & QUICK INFO -->
        <div class="dashboard-main">
            <div class="chart-container card">
                <h3 class="table-title">📈 Platform Sleep Trends</h3>
                <canvas id="sleepChart"></canvas>
            </div>

            <div class="side-content">
                <div class="card">
                    <div class="icon-bg">🛡️</div>
                    <div class="action-copy-row">
                        <span class="action-icon">👥</span>
                        <h3 class="action-title">User Registry</h3>
                    </div>
                    <p class="action-copy">
                        Manage users, view their activity logs, and system access levels.
                    </p>
                    <a href="admin_users.php" class="action-btn">
                        OPEN REGISTRY
                    </a>
                </div>

                <div class="card info-card info-card-accent">
                    <h4 class="trend-good">📊 Total Data points</h4>
                    <p>
                        Our system currently has <strong><?php echo $totalPredictions; ?></strong> sleep predictions gathered.
                    </p>
                </div>

                <div class="card info-card info-card--energy">
                    <h4 class="trend-muted">⚡ Community Wellness</h4>
                    <div class="energy-row">
                        <div class="energy-value"><?php echo $energyLevel; ?>%</div>
                        <div class="energy-status"><?php echo $energyStatus; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FULL WIDTH BOTTOM: RECENT PREDICTIONS -->
        <div class="recent-table-container card">
            <div class="table-header-row">
                <h3 class="table-title">📊 Global Recent Predictions</h3>
                <span class="trend-muted">Real-time feed</span>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Workout</th>
                            <th>Reading</th>
                            <th>Phone</th>
                            <th>Work</th>
                            <th>Caffeine</th>
                            <th>Prediction</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($recentPredictions) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($recentPredictions)): ?>
                                <tr>
                                    <td class="table-id">#<?php echo $row['id']; ?></td>
                                    <td><?php echo $row['workout']; ?>h</td>
                                    <td><?php echo $row['reading']; ?>h</td>
                                    <td><?php echo $row['phone']; ?>h</td>
                                    <td><?php echo $row['work_hours']; ?>h</td>
                                    <td><?php echo $row['caffeine']; ?>mg</td>
                                    <td class="table-value"><?php echo number_format($row['predicted_sleep'], 1); ?>h</td>
                                    <td>
                                        <?php if ($row['predicted_sleep'] >= 7): ?>
                                            <span class="badge badge-good">Healthy</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Low Sleep</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="empty-state">No platform data available yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
const ctx = document.getElementById('sleepChart').getContext('2d');
// Use actual canvas height for gradient so it scales responsively and avoids stretching artifacts
const canvasHeight = ctx.canvas.clientHeight || 350;
const gradient = ctx.createLinearGradient(0, 0, 0, canvasHeight);
gradient.addColorStop(0, 'rgba(168, 85, 247, 0.4)');
gradient.addColorStop(1, 'rgba(168, 85, 247, 0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'Avg Sleep Prediction',
            data: <?php echo json_encode($chartValues); ?>,
            borderColor: '#a855f7',
            borderWidth: 3,
            backgroundColor: gradient,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#a855f7',
            pointBorderColor: 'rgba(255,255,255,0.5)',
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(255,255,255,0.05)' },
                ticks: { color: '#888' }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#888' }
            }
        }
    }
});
</script>

</body>
</html>
