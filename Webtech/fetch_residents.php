<?php
include 'db_connect.php';

if (isset($_POST['query'])) {
    $search = "%" . $_POST['query'] . "%";
    $sql = "SELECT CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', surname) AS full_name 
            FROM residents 
            HAVING full_name LIKE ? 
            LIMIT 8";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $clean_name = preg_replace('/\s+/', ' ', trim($row['full_name']));
            echo "<div class='suggestion-item' style='padding:10px; cursor:pointer; border-bottom:1px solid #eee;'>" . htmlspecialchars($clean_name) . "</div>";
        }
    } else {
        echo "<div style='padding:10px; color:#999;'>No resident found.</div>";
    }
}
?>