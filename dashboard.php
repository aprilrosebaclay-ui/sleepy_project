<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['full_name'] ?? "User";
$firstName = explode(' ', $userName)[0];
$userId = $_SESSION['user_id'];

// BASIC STATS
$totalPredictions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM history"))['t'] ?? 0;
$avgSleep = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(predicted_sleep) as a FROM history"))['a'] ?? 0, 1);
$maxSleep = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(predicted_sleep) as m FROM history"))['m'] ?? 0, 1);
$avgCaffeine = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(caffeine) as c FROM history"))['c'] ?? 0, 0);
$avgPhone = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(phone) as p FROM history"))['p'] ?? 1, 1);

// RECENT PREDICTIONS (10)
$recentPredictions = mysqli_query($conn, "SELECT * FROM history ORDER BY id DESC LIMIT 10");

// DATA FOR CHART (Last 7 entries)
$chartDataQuery = mysqli_query($conn, "SELECT predicted_sleep, id FROM (SELECT predicted_sleep, id FROM history ORDER BY id DESC LIMIT 7) as sub ORDER BY id ASC");
$chartLabels = [];
$chartValues = [];
while($row = mysqli_fetch_assoc($chartDataQuery)) {
    $chartLabels[] = "#" . $row['id'];
    $chartValues[] = $row['predicted_sleep'];
}

// LATEST ENERGY CALCULATION
$latestEntry = mysqli_fetch_assoc(mysqli_query($conn, "SELECT predicted_sleep FROM history ORDER BY id DESC LIMIT 1"));
$latestSleep = $latestEntry['predicted_sleep'] ?? 0;
$energyLevel = 0;
$energyStatus = "No data yet";

if ($latestSleep >= 8) {
    $energyLevel = rand(92, 98);
    $energyStatus = "Fully Charged";
} elseif ($latestSleep >= 7) {
    $energyLevel = rand(80, 90);
    $energyStatus = "Ready for the day";
} elseif ($latestSleep >= 6) {
    $energyLevel = rand(60, 75);
    $energyStatus = "Feeling average";
} elseif ($latestSleep > 0) {
    $energyLevel = rand(30, 50);
    $energyStatus = "Low energy";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestIQ | Dashboard</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #a855f7;
            --primary-dark: #7c3aed;
            --accent: #00ffcc;
            --bg-card: rgba(255, 255, 255, 0.06);
            --border: rgba(255, 255, 255, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 20px;
            border-radius: 20px;
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            transition: 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
        }

        .stat-card h4 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #aaa;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
        }

        .stat-card .trend {
            font-size: 12px;
            margin-top: 5px;
        }

        .dashboard-main {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        .chart-container {
            background: var(--bg-card);
            padding: 25px;
            border-radius: 24px;
            border: 1px solid var(--border);
            height: 400px;
        }

        .recent-table-container {
            background: var(--bg-card);
            padding: 25px;
            border-radius: 24px;
            border: 1px solid var(--border);
            margin-top: 25px;
        }

        .recent-table-container h3 {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px;
            color: var(--primary);
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 15px 12px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-good { background: rgba(0, 255, 204, 0.15); color: var(--accent); }
        .badge-warning { background: rgba(255, 165, 0, 0.15); color: #ffa500; }

        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .action-card {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.15), rgba(0, 0, 0, 0.2));
            padding: 25px;
            border-radius: 24px;
            border: 1px solid rgba(168, 85, 247, 0.3);
            position: relative;
            overflow: hidden;
            border-left: 5px solid var(--primary);
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .action-card:hover {
            transform: scale(1.02);
            border-color: var(--primary);
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.2), rgba(0, 0, 0, 0.3));
        }

        .action-card .icon-bg {
            position: absolute;
            right: -10px;
            top: -10px;
            font-size: 80px;
            opacity: 0.05;
            transform: rotate(-15deg);
            pointer-events: none;
        }

        .btn-large {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            margin-top: 15px;
        }

        @media (max-width: 1200px) {
            .dashboard-main {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="admin-container">
    <?php include 'sidebar.php'; ?>

    <div class="main-area">
        <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
            <div>
                <h1 style="font-size: 36px; font-weight: 800; background: linear-gradient(to right, #fff, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Dashboard
                </h1>
                <p style="color: #aaa; margin-top: 5px;">Welcome back, <?php echo htmlspecialchars($firstName); ?>. Here is your sleep data.</p>
            </div>
            <div style="text-align: right;">
                <div style="font-weight: 600;"><?php echo date('l, F d'); ?></div>
                <div style="font-size: 12px; color: #7c3aed;">System Online • RestIQ     v1.0</div>
            </div>
        </header>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <h4>🛌 Average Sleep</h4>
                <div class="value"><?php echo $avgSleep; ?> <span style="font-size: 14px; font-weight: 400; color: #888;">hrs</span></div>
                <div class="trend" style="color: <?php echo ($avgSleep >= 7) ? 'var(--accent)' : '#ff6b6b'; ?>">
                    <?php echo ($avgSleep >= 7) ? 'Good Amount' : 'Needs More'; ?>
                </div>
            </div>
            <div class="stat-card">
                <h4>🏆 Best Sleep</h4>
                <div class="value"><?php echo $maxSleep; ?> <span style="font-size: 14px; font-weight: 400; color: #888;">hrs</span></div>
                <div class="trend" style="color: var(--primary);">Personal Record</div>
            </div>
            <div class="stat-card">
                <h4>☕ Coffee Drink</h4>
                <div class="value"><?php echo $avgCaffeine; ?> <span style="font-size: 14px; font-weight: 400; color: #888;">mg</span></div>
                <div class="trend" style="color: #aaa;">Daily Average</div>
            </div>
            <div class="stat-card">
                <h4>📱 Phone Use</h4>
                <div class="value"><?php echo $avgPhone; ?> <span style="font-size: 14px; font-weight: 400; color: #888;">hrs</span></div>
                <div class="trend" style="color: <?php echo ($avgPhone > 3) ? '#ff6b6b' : 'var(--accent)'; ?>">
                    <?php echo ($avgPhone > 3) ? 'A bit high' : 'Great balance'; ?>
                </div>
            </div>
        </div>

        <!-- MIDDLE SECTION: CHART & QUICK INFO -->
        <div class="dashboard-main" style="margin-bottom: 25px;">
            <div class="chart-container" style="height: 350px;">
                <h3 style="margin-bottom: 15px; font-size: 16px;">📈 Sleep Trends</h3>
                <canvas id="sleepChart"></canvas>
            </div>

            <div class="side-content" style="display: flex; flex-direction: column; gap: 15px;">
                <div class="action-card">
                    <div class="icon-bg">🧠</div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <span style="font-size: 24px;">🧠</span>
                        <h3 style="font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">Check Tonight</h3>
                    </div>
                    <p style="color: #bbb; font-size: 13px; line-height: 1.5; margin-bottom: 20px;">
                        Input your activities today to see how much sleep you need.
                    </p>
                    <a href="sleep_prediction.php" class="btn" style="width: 100%; padding: 12px; border-radius: 12px; font-size: 14px; letter-spacing: 0.5px; background: linear-gradient(90deg, #7c3aed, #a855f7); border: none; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);">
                        START PREDICTION
                    </a>
                </div>

                <div class="card" style="padding: 15px; background: rgba(0, 255, 204, 0.05); border-color: rgba(0, 255, 204, 0.2);">
                    <h4 style="color: var(--accent); font-size: 12px; margin-bottom: 8px;">💡 Daily Tip</h4>
                    <p style="font-size: 12px; line-height: 1.4; color: #ddd;">
                        Using your phone less before bed could help you sleep longer.
                    </p>
                </div>

                <div class="card" style="padding: 15px; flex: 1; display: flex; flex-direction: column; justify-content: center; border-left: 5px solid var(--accent);">
                    <h4 style="font-size: 12px; color: #aaa; text-transform: uppercase; margin-bottom: 5px;">⚡ Energy Level</h4>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="font-size: 20px; font-weight: 800; color: var(--accent);"><?php echo $energyLevel; ?>%</div>
                        <div style="font-size: 11px; color: #888;"><?php echo $energyStatus; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIFESTYLE IMPACT (Compact Row) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px;">
             <div class="card" style="padding: 15px; display: flex; align-items: center; gap: 20px;">
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px;">
                        <span>🏃 Physical Activity</span>
                        <span style="color: var(--accent);">High Impact</span>
                    </div>
                    <div style="width: 100%; height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div style="width: 85%; height: 100%; background: var(--accent); border-radius: 10px;"></div>
                    </div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px;">
                        <span>🤳 Phone Exposure</span>
                        <span style="color: #ff6b6b;">Negative</span>
                    </div>
                    <div style="width: 100%; height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div style="width: 65%; height: 100%; background: #ff6b6b; border-radius: 10px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FULL WIDTH BOTTOM: RECENT PREDICTIONS -->
        <div class="recent-table-container" style="margin-top: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px;">📊 Recent Predictions</h3>
                <a href="history.php" style="font-size: 12px; color: var(--primary); text-decoration: none;">View Full History →</a>
            </div>
            <div style="overflow-x: auto;">
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
                                    <td>#<?php echo $row['id']; ?></td>
                                    <td><?php echo $row['workout']; ?>h</td>
                                    <td><?php echo $row['reading']; ?>h</td>
                                    <td><?php echo $row['phone']; ?>h</td>
                                    <td><?php echo $row['work_hours']; ?>h</td>
                                    <td><?php echo $row['caffeine']; ?>mg</td>
                                    <td style="font-weight: 700; color: #fff;"><?php echo number_format($row['predicted_sleep'], 1); ?>h</td>
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
                                <td colspan="8" style="text-align: center; padding: 40px; color: #666;">No data available yet.</td>
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
const gradient = ctx.createLinearGradient(0, 0, 0, 350);
gradient.addColorStop(0, 'rgba(168, 85, 247, 0.4)');
gradient.addColorStop(1, 'rgba(168, 85, 247, 0)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
            label: 'Predicted Sleep',
            data: <?php echo json_encode($chartValues); ?>,
            borderColor: '#a855f7',
            borderWidth: 3,
            backgroundColor: gradient,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#a855f7',
            pointRadius: 4,
            pointHoverRadius: 6
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
                beginAtZero: false,
                suggestedMin: 4,
                suggestedMax: 10,
                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                ticks: { color: '#888', font: { size: 10 } }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#888', font: { size: 10 } }
            }
        }
    }
});
</script>

</body>
</html>