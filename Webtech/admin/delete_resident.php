<?php
include __DIR__ . '/db_connect.php'; 

session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Error: Resident ID not provided for deletion.";
    header('Location: residents.php');
    exit();
}

$person_id = intval($_GET['id']);

$sql = "DELETE FROM residents WHERE person_id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $person_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['status_success'] = "Resident ID $person_id successfully deleted.";
        } else {
            $_SESSION['error_message'] = "Error: Resident ID $person_id not found or already deleted.";
        }
    } else {
        $_SESSION['error_message'] = "Error executing deletion for ID $person_id: " . $stmt->error;
    }

    $stmt->close();
} else {
    $_SESSION['error_message'] = "Database error: Could not prepare statement for deletion.";
}

$conn->close();

header('Location: residents.php');
exit();
?>