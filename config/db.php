<?php
$host = "localhost";
$dbname = "hmsdraft3"; // change this to your db name
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, 3307);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
