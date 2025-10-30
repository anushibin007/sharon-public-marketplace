<?php
session_start();

// Database configuration - use environment variables for Docker
$host = getenv('DB_HOST') ?: 'localhost';
$db = getenv('DB_NAME') ?: 'online_store';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>