<?php
// search_resident.php

include 'db_connect.php'; 

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
$results = [];

if (!empty($query)) {
    // Prepare search query
    $search_query = "%" . $conn->real_escape_string($query) . "%";
    
    // Select person_id and full name
    $sql = "SELECT person_id, CONCAT(first_name, ' ', surname) AS full_name 
            FROM residents 
            WHERE first_name LIKE ? OR surname LIKE ? OR CONCAT(first_name, ' ', surname) LIKE ?
            LIMIT 10"; 
            
    $stmt = $conn->prepare($sql);
    // Bind search term three times
    $stmt->bind_param("sss", $search_query, $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()) {
        $results[] = [
            'id' => $row['person_id'],
            'name' => htmlspecialchars($row['full_name'])
        ];
    }
    $stmt->close();
}

echo json_encode($results);
?>