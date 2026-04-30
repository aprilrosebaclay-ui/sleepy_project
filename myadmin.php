<?php
include 'connection.php';

// Fetch data FIRST
$query = "SELECT u.first_name, u.last_name, 
                 p.sleep_hours, p.predicted_category, p.prediction_date
          FROM predictions p
          JOIN users u ON u.user_id = p.user_id
          ORDER BY p.prediction_date DESC";

$result = mysqli_query($conn, $query);

// Store data in array (for stats + search)
$data = [];
while($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<style>
body {
    font-family: Arial;
    background: #f4f6f9;
    margin: 0;
}
header {
    background: #2c3e50;
    color: white;
    padding: 15px;
    text-align: center;
}
.container { padding: 20px; }
.cards {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}
.card {
    flex: 1;
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}
table {
    width: 100%;
    background: white;
    border-collapse: collapse;
}
th, td {
    padding: 10px;
    border: 1px solid #ddd;
}
th {
    background: #34495e;
    color: white;
}
</style>
</head>

<body>

<header>Admin Dashboard - Sleep Prediction</header>

<div class="container">

<!-- CARDS -->
<div class="cards">
    <div class="card">
        <h3>Total Users</h3>
        <p><?php echo count(array_unique(array_column($data, 'first_name'))); ?></p>
    </div>

    <div class="card">
        <h3>Total Predictions</h3>
        <p><?php echo count($data); ?></p>
    </div>

    <div class="card">
        <h3>Most Common Result</h3>
        <p>
        <?php
        $counts = [];
        foreach ($data as $d) {
            $counts[$d['predicted_category']] =
                ($counts[$d['predicted_category']] ?? 0) + 1;
        }
        arsort($counts);
        echo key($counts) ?? '-';
        ?>
        </p>
    </div>
</div>

<!-- SEARCH -->
<input type="text" id="search" placeholder="Search name..." onkeyup="searchTable()">

<!-- TABLE -->
<table id="dataTable">
<thead>
<tr>
    <th>Full Name</th>
    <th>Sleep Hours</th>
    <th>Prediction</th>
    <th>Date</th>
</tr>
</thead>

<tbody id="tableBody">
<?php foreach($data as $row): ?>
<tr>
    <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
    <td><?php echo $row['sleep_hours']; ?></td>
    <td><?php echo $row['predicted_category']; ?></td>
    <td><?php echo $row['prediction_date']; ?></td>
</tr>
<?php endforeach; ?>
</tbody>

</table>

</div>

<script>
function searchTable() {
    let input = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll("#tableBody tr");

    rows.forEach(row => {
        let name = row.cells[0].innerText.toLowerCase();
        row.style.display = name.includes(input) ? "" : "none";
    });
}
</script>

</body>
</html>