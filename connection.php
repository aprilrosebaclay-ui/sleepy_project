<?php
// REMOVED: session_start(); ← Let individual pages handle sessions

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restiq_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8"); // Added for better text support
?>