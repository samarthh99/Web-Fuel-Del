<?php
$conn = new mysqli("localhost", "root", "", "fuel_delivery");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
