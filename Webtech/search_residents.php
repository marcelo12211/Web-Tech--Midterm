<?php
include 'db_connect.php';

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";
    $sql = "SELECT first_name, surname FROM residents 
            WHERE CONCAT(first_name, ' ', surname) LIKE ? 
            OR first_name LIKE ? 
            OR surname LIKE ? 
            LIMIT 8";
            
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $fullName = $row['first_name'] . ' ' . $row['surname'];
                echo "<div class='search-item' onclick='selectResident(\"" . htmlspecialchars($fullName) . "\")'>" . htmlspecialchars($fullName) . "</div>";
            }
        } else {
            echo "<div class='search-item' style='cursor:default;'>No results found</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='search-item' style='color:red;'>SQL Error: " . htmlspecialchars($conn->error) . "</div>";
    }
}
$conn->close();
?>