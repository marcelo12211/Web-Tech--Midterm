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
$death_id      = $_POST['death_id'] ?? '';
$resident_name = $_POST['resident_name'] ?? '';
$resident_age  = $_POST['resident_age'] ?? '';
$date_of_death = $_POST['date_of_death'] ?? '';
$cause_of_death = $_POST['cause_of_death'] ?? '';
$is_pwd        = $_POST['is_pwd'] ?? 'no';
$is_senior     = $_POST['is_senior'] ?? 'no';
$pwd_id        = $_POST['pwd_id'] ?? '';
$ncsc_rrn      = $_POST['ncsc_rrn'] ?? '';
$osca_id       = $_POST['osca_id'] ?? '';
$sql = "UPDATE deaths SET 
            name = ?, 
            age = ?, 
            date_of_death = ?, 
            cause_of_death = ?, 
            is_pwd = ?, 
            is_senior = ?, 
            pwd_id = ?, 
            ncsc_rrn = ?, 
            osca_id = ? 
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sisssssssi", 
        $resident_name, 
        $resident_age, 
        $date_of_death, 
        $cause_of_death, 
        $is_pwd, 
        $is_senior, 
        $pwd_id, 
        $ncsc_rrn, 
        $osca_id, 
        $death_id
    );

    if ($stmt->execute()) {
        $_SESSION['status_success'] = "Record updated successfully!";
    } else {
        $_SESSION['status_error'] = "Failed to update record: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['status_error'] = "Database error: " . $conn->error;
}

header('Location: deaths.php');
exit();
?>