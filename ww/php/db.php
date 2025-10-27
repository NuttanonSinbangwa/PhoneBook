<?php
$host = 'db';
$user = 'user';
$pass = 'pass';
$dbname = 'pb_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
