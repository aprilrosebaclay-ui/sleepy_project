<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <h2>🛡️ Admin</h2>
        <p>Control Panel</p>
    </div>

    <a href="myadmin.php" class="menu-item <?php echo ($current_page == 'myadmin.php') ? 'active' : ''; ?>">
        📊 <span>Dashboard</span>
    </a>
    <a href="admin_users.php" class="menu-item <?php echo ($current_page == 'admin_users.php' || $current_page == 'view_user.php') ? 'active' : ''; ?>">
        👥 <span>User Registry</span>
    </a>
    <a href="admin_profile.php" class="menu-item <?php echo ($current_page == 'admin_profile.php') ? 'active' : ''; ?>">
        👤 <span>Profile</span>
    </a>
    
    <a href="logout.php" class="menu-item menu-item--logout">
        🚪 <span>Logout</span>
    </a>
</div>
