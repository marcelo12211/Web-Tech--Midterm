<?php
include 'db_connect.php'; 

// Check for the Resident ID in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // If no ID is provided, redirect back to the residents list
    header('Location: residents.php');
    exit();
}

$residentId = $conn->real_escape_string($_GET['id']);

// --- 1. Delete from Demographics Table (Child Table) ---
// Note: You must delete from the child table first to avoid foreign key constraints.
$sql_demo_delete = "DELETE FROM demographics WHERE MEMBER_ID = '$residentId'";

if (!$conn->query($sql_demo_delete)) {
    // Optionally log or display an error
    die("Error deleting demographic data: " . $conn->error);
}

// --- 2. Delete from Identification Table (Main Table) ---
$sql_id_delete = "DELETE FROM identification WHERE ID = '$residentId'";

if (!$conn->query($sql_id_delete)) {
    // If the identification delete fails, something is seriously wrong
    die("Error deleting resident identification data: " . $conn->error);
}

// --- 3. Redirect back to residents page with a success message ---
header('Location: residents.php?status=deleted&id=' . urlencode($residentId));
exit();
?>