<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0 || $id == $_SESSION['user_id']) {
    header("Location: users.php");
    exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: users.php?deleted=1");
exit;
