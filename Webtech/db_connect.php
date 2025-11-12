<?php
$servername = "localhost";
$username = "root";        // WAMP default username
$password = "";            // WAMP default password (empty is default but can change if needed)
$dbname = "happyhallow";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>