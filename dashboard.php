<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['full_name'] ?? "User";
$firstName = explode(' ', $userName)[0];

$totalPredictions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM history"))['t'] ?? 0;
$avgSleep = round(mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(predicted_sleep) as a FROM history"))['a'] ?? 0,1);
?>

<!DOCTYPE html>
<html>
<head>
<title>RestIQ Dashboard</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    min-height: 100vh;
    background: radial-gradient(circle at top, #3b0a6b, #12001f 70%);
    color: #fff;
}

/* MAIN LAYOUT */
.admin-container {
    display: flex;
}

/* SIDEBAR */
.sidebar {
    width: 260px;
    height: 100vh;
    position: fixed;
    background: rgba(25, 10, 50, 0.7);
    backdrop-filter: blur(18px);
    padding: 25px;
    border-right: 1px solid rgba(255,255,255,0.08);
}

.sidebar-brand {
    text-align: center;
    margin-bottom: 40px;
}

.sidebar-brand h2 {
    font-size: 24px;
    color: #c084fc;
}

.sidebar-brand p {
    font-size: 12px;
    color: #aaa;
}

.menu-item {
    display: block;
    padding: 12px 15px;
    border-radius: 12px;
    text-decoration: none;
    color: #ddd;
    margin-bottom: 10px;
    transition: 0.3s ease;
}

.menu-item:hover {
    background: rgba(168,85,247,0.15);
    transform: translateX(5px);
}

.menu-item.active {
    background: linear-gradient(90deg, #7c3aed, #a855f7);
    box-shadow: 0 5px 15px rgba(168,85,247,0.3);
}

/* MAIN AREA */
.main-area {
    margin-left: 260px;
    padding: 30px;
    width: 100%;
}

/* HEADER */
.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-title h1 {
    font-size: 32px;
}

.page-title p {
    color: #bbb;
}

/* STATS */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: rgba(255,255,255,0.06);
    padding: 20px;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.08);
    transition: 0.3s ease;
    backdrop-filter: blur(10px);
}

.stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(168,85,247,0.2);
}

.stat-card h4 {
    color: #c084fc;
    margin-bottom: 10px;
    font-size: 14px;
}

.stat-card p {
    font-size: 22px;
    font-weight: bold;
}

/* CONTENT CARD */
.content-card {
    background: rgba(255,255,255,0.06);
    padding: 25px;
    border-radius: 18px;
    border: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
}

/* BUTTONS */
.action-btn {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 10px;
    margin-top: 12px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s ease;
}

.action-btn.primary {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: white;
}

.action-btn.primary:hover {
    transform: scale(1.02);
    box-shadow: 0 8px 20px rgba(168,85,247,0.3);
}

.action-btn.secondary {
    background: rgba(255,255,255,0.08);
    color: white;
}

.action-btn.secondary:hover {
    background: rgba(255,255,255,0.15);
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .sidebar {
        display: none;
    }
    .main-area {
        margin-left: 0;
    }
}

</style>

</head>

<body>

<div class="admin-container">

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">
        <h2>🌙 RestIQ</h2>
        <p>AI Sleep System</p>
    </div>

    <a href="dashboard.php" class="menu-item active">📊 Dashboard</a>
    <a href="sleep_prediction.php" class="menu-item">🧠 Predict Sleep</a>
    <a href="history.php" class="menu-item">📁 History</a>
    <a href="profile.php" class="menu-item">👤 Profile</a>
</div>

<!-- MAIN -->
<div class="main-area">

<div class="top-header">
    <div class="page-title">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($firstName); ?> ✨</p>
    </div>
    <div style="color:#bbb;"><?php echo date('F d, Y'); ?></div>
</div>

<!-- STATS -->
<div class="stats-grid">

    <div class="stat-card">
        <h4>Average Sleep</h4>
        <p><?php echo $avgSleep; ?> hrs</p>
    </div>

    <div class="stat-card">
        <h4>Total Predictions</h4>
        <p><?php echo $totalPredictions; ?></p>
    </div>

    <div class="stat-card">
        <h4>Status</h4>
        <p><?php echo ($avgSleep >= 7) ? "Healthy ✨" : "Needs Rest 😴"; ?></p>
    </div>

    <div class="stat-card">
        <h4>Daily Tip</h4>
        <p>Sleep before 11PM</p>
    </div>

</div>

<!-- ACTIONS -->
<div class="content-card">
    <h3 style="margin-bottom:10px;">Quick Actions</h3>

    <button class="action-btn primary" onclick="location.href='sleep_prediction.php'">
        ➕ New Prediction
    </button>

    <button class="action-btn secondary" onclick="location.href='history.php'">
        📊 View History
    </button>
</div>

</div>
</div>

</body>
</html>