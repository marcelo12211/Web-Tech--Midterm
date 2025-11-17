<?php
include __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $resident = htmlspecialchars($_POST['resident']);
    $purpose = htmlspecialchars($_POST['purpose']);

    // Check if file is uploaded
    if (isset($_FILES['docImage']) && $_FILES['docImage']['error'] === 0) {
        $fileTmpPath = $_FILES['docImage']['tmp_name'];
        $fileName = $_FILES['docImage']['name'];
        $fileSize = $_FILES['docImage']['size'];
        $fileType = $_FILES['docImage']['type'];

        // Allowed image extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            die("Error: Only JPG, PNG, GIF, and WEBP images are allowed.");
        }

        // Create upload directory if not exists
        $uploadDir = 'uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $newFileName = uniqid('doc_', true) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        // Move uploaded file
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Insert record into database
            $stmt = $conn->prepare("INSERT INTO documents (resident_name, file_path, purpose, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $resident, $destPath, $purpose);

            if ($stmt->execute()) {
                // Redirect back to documents.php with success message
                header("Location: documents.php?success=1");
                exit();
            } else {
                echo "Database error: " . $stmt->error;
            }
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
} else {
    echo "Invalid request method.";
}
?>
