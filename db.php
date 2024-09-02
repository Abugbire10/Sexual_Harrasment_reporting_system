<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sexual_harassment_system";
$port = 3308;  


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Database connection failed: " . $conn->connect_error);
}
?>
