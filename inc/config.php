<?php
// Database configuration
$servername = "localhost";
$username = "root"; // 数据库用户名
$password = ""; // 数据库密码
$dbname = "SuperConcert";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
