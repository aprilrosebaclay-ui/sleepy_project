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
    
    <a href="logout.php" class="menu-item" style="margin-top: auto; color: #ff6b6b; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
        🚪 <span>Logout</span>
    </a>
</div>
