<?php
session_start();
date_default_timezone_set('Asia/Manila');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ADMIN CANNOT ACCESS USER HISTORY
if ($_SESSION['role'] === 'admin') {
    header("Location: myadmin.php");
    exit();
}

$userName = $_SESSION['full_name'] ?? "User";
$firstName = explode(' ', $userName)[0];

// PAGINATION LOGIC
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total count for pagination
$count_res = $conn->query("SELECT COUNT(*) as total FROM history");
$total_rows = $count_res->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch limited results
$stmt = $conn->prepare("SELECT * FROM history ORDER BY id DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result_data = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>RestIQ - History</title>
    <link rel="stylesheet" href="CSS/restiq.css">
    <link rel="stylesheet" href="CSS/pages/history.css">
</head>

<body>
<div class="admin-container">
    <?php include 'sidebar.php'; ?>
    <div class="main-area">
        <div class="page-header">
            <div>
                <h1 class="table-page-title">Prediction History</h1>
                <p class="table-page-subtitle">Your past AI-driven sleep predictions (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)</p>
            </div>
            <div class="page-meta"><?php echo date('F d, Y'); ?></div>
        </div>

        <div class="table-shell">
            <?php if ($result_data && $result_data->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Workout</th>
                            <th>Phone</th>
                            <th>Work</th>
                            <th>Caffeine</th>
                            <th>Prediction</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result_data->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo $row['workout']; ?>h</td>
                                <td><?php echo $row['phone']; ?>h</td>
                                <td><?php echo $row['work_hours']; ?>h</td>
                                <td><?php echo $row['caffeine']; ?>mg</td>
                                <td class="predicted-value"><?php echo number_format($row['predicted_sleep'], 2); ?>h</td>
                                <td>
                                    <?php if ($row['predicted_sleep'] >= 7): ?>
                                        <span class="badge badge-success">Healthy ✨</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Needs Rest 😴</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

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

                    <?php else: ?>
                <div class="empty-state">
                    <h3>No history found</h3>
                    <p>Start by making your first prediction!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>