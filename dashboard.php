<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] === 'admin') {
    header("Location: myadmin.php");
    exit();
}

$userName = $_SESSION['full_name'] ?? 'User';
$firstName = explode(' ', $userName)[0];

$totalPredictions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM history"))['t'] ?? 0;
$avgSleep = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(predicted_sleep) as a FROM history"))['a'] ?? 0, 1);
$maxSleep = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(predicted_sleep) as m FROM history"))['m'] ?? 0, 1);
$avgCaffeine = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(caffeine) as c FROM history"))['c'] ?? 0, 0);
$avgPhone = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(phone) as p FROM history"))['p'] ?? 1, 1);

$recentPredictions = mysqli_query($conn, "SELECT * FROM history ORDER BY id DESC LIMIT 10");

$chartDataQuery = mysqli_query($conn, "SELECT predicted_sleep, id FROM (SELECT predicted_sleep, id FROM history ORDER BY id DESC LIMIT 7) as sub ORDER BY id ASC");
$chartLabels = [];
$chartValues = [];
while ($row = mysqli_fetch_assoc($chartDataQuery)) {
    $chartLabels[] = '#' . $row['id'];
    $chartValues[] = $row['predicted_sleep'];
}

$latestEntry = mysqli_fetch_assoc(mysqli_query($conn, "SELECT predicted_sleep FROM history ORDER BY id DESC LIMIT 1"));
$latestSleep = $latestEntry['predicted_sleep'] ?? 0;
$energyLevel = 0;
$energyStatus = 'No data yet';

if ($latestSleep >= 8) {
    $energyLevel = rand(92, 98);
    $energyStatus = 'Fully Charged';
} elseif ($latestSleep >= 7) {
    $energyLevel = rand(80, 90);
    $energyStatus = 'Ready for the day';
} elseif ($latestSleep >= 6) {
    $energyLevel = rand(60, 75);
    $energyStatus = 'Feeling average';
} elseif ($latestSleep > 0) {
    $energyLevel = rand(30, 50);
    $energyStatus = 'Low energy';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestIQ | Dashboard</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <link rel="stylesheet" href="CSS/pages/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="admin-container">
    <?php include 'sidebar.php'; ?>

    <div class="main-area">
        <header class="dashboard-header">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($firstName); ?>. Here is your sleep data.</p>
            </div>
            <div class="page-meta">
                <div class="page-meta-main"><?php echo date('l, F d'); ?></div>
                <div class="page-meta-sub">System Online • RestIQ v1.0</div>
            </div>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <h4>🛌 Average Sleep</h4>
                <div class="value"><?php echo $avgSleep; ?> <span class="stat-unit">hrs</span></div>
                <div class="trend <?php echo ($avgSleep >= 7) ? 'trend-good' : 'trend-warning'; ?>"><?php echo ($avgSleep >= 7) ? 'Good Amount' : 'Needs More'; ?></div>
            </div>
            <div class="stat-card">
                <h4>🏆 Best Sleep</h4>
                <div class="value"><?php echo $maxSleep; ?> <span class="stat-unit">hrs</span></div>
                <div class="trend trend-muted">Personal Record</div>
            </div>
            <div class="stat-card">
                <h4>☕ Coffee Drink</h4>
                <div class="value"><?php echo $avgCaffeine; ?> <span class="stat-unit">mg</span></div>
                <div class="trend trend-muted">Daily Average</div>
            </div>
            <div class="stat-card">
                <h4>📱 Phone Use</h4>
                <div class="value"><?php echo $avgPhone; ?> <span class="stat-unit">hrs</span></div>
                <div class="trend <?php echo ($avgPhone > 3) ? 'trend-warning' : 'trend-good'; ?>"><?php echo ($avgPhone > 3) ? 'A bit high' : 'Great balance'; ?></div>
            </div>
        </div>

        <div class="dashboard-main">
            <div class="chart-container card">
                <h3 class="table-title">📈 Sleep Trends</h3>
                <canvas id="sleepChart"></canvas>
            </div>

            <div class="side-content">
                <div class="card">
                    <div class="icon-bg">🧠</div>
                    <div class="action-copy-row">
                        <span class="action-icon">🧠</span>
                        <h3 class="action-title">Check Tonight</h3>
                    </div>
                    <p class="action-copy">Input your activities today to see how much sleep you need.</p>
                    <a href="sleep_prediction.php" class="action-btn">START PREDICTION</a>
                </div>

                <div class="card info-card info-card-accent">
                    <h4 class="trend-good">💡 Daily Tip</h4>
                    <p>Using your phone less before bed could help you sleep longer.</p>
                </div>

                <div class="card info-card info-card--energy">
                    <h4 class="trend-muted">⚡ Energy Level</h4>
                    <div class="energy-row">
                        <div class="energy-value"><?php echo $energyLevel; ?>%</div>
                        <div class="energy-status"><?php echo $energyStatus; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="impact-grid">
            <div class="card impact-card">
                <div class="impact-col">
                    <div class="impact-line">
                        <span>🏃 Physical Activity</span>
                        <span class="trend-good">High Impact</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill progress-fill--accent" style="width: 85%;"></div>
                    </div>
                </div>
                <div class="impact-col">
                    <div class="impact-line">
                        <span>🤳 Phone Exposure</span>
                        <span class="trend-warning">Negative</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill progress-fill--danger" style="width: 65%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="recent-table-container card">
            <div class="table-header-row">
                <h3 class="table-title">📊 Recent Predictions</h3>
                <a href="history.php" class="table-link">View Full History →</a>
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
                            <?php while ($row = mysqli_fetch_assoc($recentPredictions)): ?>
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
                                <td colspan="8" class="empty-state">No data available yet.</td>
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
const canvasHeight = ctx.canvas.clientHeight || 350;
const gradient = ctx.createLinearGradient(0, 0, 0, canvasHeight);
gradient.addColorStop(0, 'rgba(168, 85, 247, 0.4)');
gradient.addColorStop(1, 'rgba(168, 85, 247, 0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'Sleep Prediction',
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
        plugins: { legend: { display: false } },
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
