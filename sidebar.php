<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <h2>🌙 RestIQ</h2>
        <p>AI Sleep System</p>
    </div>

    <a href="dashboard.php" class="menu-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        📊 <span>Dashboard</span>
    </a>
    <a href="sleep_prediction.php" class="menu-item <?php echo ($current_page == 'sleep_prediction.php') ? 'active' : ''; ?>">
        🧠 <span>Predict Sleep</span>
    </a>
    <a href="history.php" class="menu-item <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>">
        📁 <span>History</span>
    </a>
    <a href="profile.php" class="menu-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
        👤 <span>Profile</span>
    </a>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="myadmin.php" class="menu-item menu-item--accent <?php echo ($current_page == 'myadmin.php' || $current_page == 'view_user.php') ? 'active' : ''; ?>">
        🛡️ <span>Admin Panel</span>
    </a>
    <?php endif; ?>
    
    <a href="logout.php" class="menu-item menu-item--logout">
        🚪 <span>Logout</span>
    </a>
</div>
