<?php
// search_residents.php
include 'db_conn.php';

$search = isset($_GET['q']) ? $_GET['q'] : '';

if (!empty($search)) {
    // Hahanap sa residents table. Palitan ang 'full_name' kung iba ang column name mo.
    $stmt = $pdo->prepare("SELECT full_name FROM residents WHERE full_name LIKE ? LIMIT 5");
    $stmt->execute(["%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($results);
} else {
    echo json_encode([]);
}
?>