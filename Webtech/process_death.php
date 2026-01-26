<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $record_number = $_POST['record_number'];
    $name = $_POST['resident_name'];
    $age = $_POST['resident_age'];
    $date_of_death = $_POST['date_of_death'];
    $cause_of_death = $_POST['cause_of_death'];
    $sql = "INSERT INTO deaths (record_number, name, age, date_of_death, cause_of_death) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ssiss", $record_number, $name, $age, $date_of_death, $cause_of_death);
        
        if ($stmt->execute()) {
            $_SESSION['status_success'] = "Record successfully added!";
        } else {
            $_SESSION['status_error'] = "Error saving record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['status_error'] = "Database error: " . $conn->error;
    }
    header("Location: deaths.php");
    exit();
} else {
    header("Location: deaths.php");
    exit();
}
?>