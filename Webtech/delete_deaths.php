<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

include 'db_connect.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: deaths.php');
    exit();
}

$deathId = $conn->real_escape_string($_GET['id']);

$sql_delete = "DELETE FROM deaths WHERE id = '$deathId'";

if ($conn->query($sql_delete)) {
    $_SESSION['status_success'] = 'Death record deleted successfully.';
} else {
    $_SESSION['status_error'] = 'Error deleting death record: ' . $conn->error;
}

header('Location: deaths.php');
exit();
?>