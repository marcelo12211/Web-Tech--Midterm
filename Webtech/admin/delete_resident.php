<?php
// Include the database connection file.
include __DIR__ . '/db_connect.php'; 

// Start session and check login status
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

// Check if resident ID is provided in GET request for deletion
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Error: Resident ID not provided for deletion.";
    header('Location: residents.php');
    exit();
}

// Sanitize the ID and convert to integer
$person_id = intval($_GET['id']);

// Use Prepared Statements for secure deletion
$sql = "DELETE FROM residents WHERE person_id = ?";

// Prepare statement
if ($stmt = $conn->prepare($sql)) {
    // Bind the integer parameter
    $stmt->bind_param("i", $person_id);

    // Execute the statement
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

// Redirect back to the residents list page
header('Location: residents.php');
exit();
?>