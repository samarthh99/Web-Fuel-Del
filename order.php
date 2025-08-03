<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$fuel = $_POST['fuel_type'];
$time = $_POST['datetime'];
$addr = $_POST['address'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO orders (user_id, fuel_type, delivery_datetime, address) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $fuel, $time, $addr);
if ($stmt->execute()) {
  echo "<script>alert('Order Confirmed!'); window.location.href='index.php';</script>";
} else {
  echo "Order failed: " . $conn->error;
}
?>
