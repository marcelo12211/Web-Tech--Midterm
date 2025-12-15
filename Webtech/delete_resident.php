<?php
include 'db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: residents.php');
    exit();
}

$residentId = intval($_GET['id']);

$conn->begin_transaction();

try {

    $stmt = $conn->prepare("DELETE FROM disabled_persons WHERE resident_id = ?");
    $stmt->bind_param("i", $residentId);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM senior_citizens WHERE resident_id = ?");
    $stmt->bind_param("i", $residentId);
    $stmt->execute();
    $stmt->close();

    if ($conn->query("SHOW TABLES LIKE 'death_records'")->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM death_records WHERE resident_id = ?");
        $stmt->bind_param("i", $residentId);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("DELETE FROM residents WHERE person_id = ?");
    $stmt->bind_param("i", $residentId);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    header("Location: residents.php?status=deleted&id=" . urlencode($residentId));
    exit();

} catch (Exception $e) {

    $conn->rollback();
    die("Error deleting resident: " . $e->getMessage());
}
?>