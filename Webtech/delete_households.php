<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

include 'db_connect.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: household.php');
    exit();
}

$householdId = $conn->real_escape_string($_GET['id']);

$check_residents = "SELECT COUNT(*) as resident_count FROM residents WHERE household_id = '$householdId'";
$result = $conn->query($check_residents);
$row = $result->fetch_assoc();

if ($row['resident_count'] > 0) {
    $_SESSION['update_error'] = true;
    $_SESSION['error_msg'] = 'Cannot delete household with existing residents. Please remove or reassign all residents first.';
    header('Location: household.php');
    exit();
}

$sql_delete = "DELETE FROM household WHERE household_id = '$householdId'";

if ($conn->query($sql_delete)) {
    $_SESSION['update_success'] = true;
    header('Location: household.php');
} else {
    $_SESSION['update_error'] = true;
    $_SESSION['error_msg'] = $conn->error;
    header('Location: household.php');
}

exit();
?>