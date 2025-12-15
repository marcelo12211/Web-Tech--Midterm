<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

include __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: deaths.php');
    exit();
}

$death_id = $conn->real_escape_string($_POST['death_id']);
$resident_name = $conn->real_escape_string($_POST['resident_name']);
$resident_age = $conn->real_escape_string($_POST['resident_age']);
$date_of_death = $conn->real_escape_string($_POST['date_of_death']);
$cause_of_death = $conn->real_escape_string($_POST['cause_of_death']);
$is_pwd = $conn->real_escape_string($_POST['is_pwd']);
$is_senior = $conn->real_escape_string($_POST['is_senior']);
$pwd_id = isset($_POST['pwd_id']) ? $conn->real_escape_string($_POST['pwd_id']) : '';
$ncsc_rrn = isset($_POST['ncsc_rrn']) ? $conn->real_escape_string($_POST['ncsc_rrn']) : '';
$osca_id = isset($_POST['osca_id']) ? $conn->real_escape_string($_POST['osca_id']) : '';

$sql = "
    UPDATE deaths SET
        name = '$resident_name',
        age = '$resident_age',
        date_of_death = '$date_of_death',
        cause_of_death = '$cause_of_death',
        is_pwd = '$is_pwd',
        is_senior = '$is_senior',
        pwd_id = '$pwd_id',
        ncsc_rrn = '$ncsc_rrn',
        osca_id = '$osca_id'
    WHERE id = '$death_id'
";
?>