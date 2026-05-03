<?php
include 'connection.php';
$r = mysqli_query($conn, 'DESCRIBE users');
while($row = mysqli_fetch_assoc($r)) {
    echo $row['Field'] . ': ' . $row['Type'] . PHP_EOL;
}
