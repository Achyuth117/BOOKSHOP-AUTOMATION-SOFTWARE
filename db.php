<?php
// Database connection file
$host = "localhost";
$user = "root";       // your phpMyAdmin username
$pass = "";           // your phpMyAdmin password
$dbname = "bas_db";   // database name

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>

