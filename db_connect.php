<?php
$servername = "localhost";
$username = "root"; // Default username for local servers
$password = "";     // Leave blank unless you set one
$dbname = "xelqx_db"; // Use your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
