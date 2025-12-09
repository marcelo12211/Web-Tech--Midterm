<?php
include 'db_connect.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: residents.php');
    exit();
}

$residentId = $conn->real_escape_string($_GET['id']);

$sql_demo_delete = "DELETE FROM demographics WHERE MEMBER_ID = '$residentId'";

if (!$conn->query($sql_demo_delete)) {
    die("Error deleting demographic data: " . $conn->error);
}

$sql_id_delete = "DELETE FROM identification WHERE ID = '$residentId'";

if (!$conn->query($sql_id_delete)) {
    die("Error deleting resident identification data: " . $conn->error);
}

header('Location: residents.php?status=deleted&id=' . urlencode($residentId));
exit();
?>