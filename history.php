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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            text-align: left;
            padding: 15px;
            background: rgba(168,85,247,0.1);
            color: #c084fc;
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 14px;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success { background: rgba(0,255,150,0.1); color: #00ffcc; border: 1px solid rgba(0,255,150,0.2); }
        .badge-warning { background: rgba(255,165,0,0.1); color: #ffa500; border: 1px solid rgba(255,165,0,0.2); }

        /* PAGINATION STYLES */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }
        .page-link {
            padding: 8px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #aaa;
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
        }
        .page-link:hover {
            background: rgba(168,85,247,0.1);
            color: #c084fc;
            border-color: #c084fc;
        }
        .page-link.active {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: white;
            border: none;
        }
        .page-link.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>

<body>
<div class="admin-container">
    <?php include 'sidebar.php'; ?>
    <div class="main-area">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="font-size: 28px;">Prediction History</h1>
                <p style="color: #aaa;">Your past AI-driven sleep predictions (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)</p>
            </div>
            <div style="color:#bbb;"><?php echo date('F d, Y'); ?></div>
        </div>

        <div class="card" style="padding: 10px;">
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
                                <td style="font-weight: bold; color: #c084fc;"><?php echo number_format($row['predicted_sleep'], 2); ?>h</td>
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
                <div style="text-align: center; padding: 50px; color: #999;">
                    <h3>No history found</h3>
                    <p>Start by making your first prediction!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>