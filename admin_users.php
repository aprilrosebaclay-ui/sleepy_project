<?php
session_start();
include 'connection.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Delete User logic
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    if ($del_id != $_SESSION['user_id']) { // Don't delete self
        $del_query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($del_query);
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        header("Location: admin_users.php?deleted=success");
        exit();
    }
}

// PAGINATION LOGIC
$limit = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total count for pagination
$count_res = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$total_rows = $count_res->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch all users (exclude admin accounts) with pagination
$users_query = "SELECT user_id, first_name, last_name, email, user_name, gender, role, timestamp FROM users WHERE role = 'user' ORDER BY user_id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($users_query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$users_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestIQ | User Registry</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <link rel="stylesheet" href="CSS/pages/users.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="admin-container">
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-area">
        <header class="page-header">
            <h1 class="users-title">User Registry</h1>
            <p class="users-subtitle">Comprehensive list of all system entities</p>
        </header>

        <div class="table-card">
            <div class="users-toolbar">
                <h2 class="users-heading">Management Console</h2>
                <input type="text" class="search-box" placeholder="Search by name, email or username..." onkeyup="filterTable(this.value)">
            </div>

            <div class="table-scroll">
                <table id="userTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar"><?php echo strtoupper(substr($user['first_name'], 0, 1)); ?></div>
                                    <div>
                                        <div class="user-name"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></div>
                                        <div class="user-username">@<?php echo $user['user_name']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $user['email']; ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($user['role']); ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="user-joined">
                                <?php echo date('M d, Y', strtotime($user['timestamp'])); ?>
                            </td>
                            <td>
                                <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                <a href="admin_users.php?delete_id=<?php echo $user['user_id']; ?>" 
                                   class="action-btn btn-del" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION NAV -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <a href="?page=<?php echo $page - 1; ?>" class="page-link <?php echo ($page <= 1) ? 'disabled' : ''; ?>">Prev</a>
                    
                    <?php 
                    // Show up to 5 page numbers
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $start + 4);
                    if ($end - $start < 4) $start = max(1, $end - 4);

                    for ($i = $start; $i <= $end; $i++): 
                    ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <a href="?page=<?php echo $page + 1; ?>" class="page-link <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">Next</a>
                </div>
            <?php endif; ?>
    </div>
</div>

<script>
    function filterTable(query) {
        const rows = document.querySelectorAll('#userTable tbody tr');
        query = query.toLowerCase();
        
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }
</script>

</body>
</html>
