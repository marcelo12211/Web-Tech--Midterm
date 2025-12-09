<?php
include __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident = htmlspecialchars($_POST['resident']);
    $purpose = htmlspecialchars($_POST['purpose']);

    if (isset($_FILES['docImage']) && $_FILES['docImage']['error'] === 0) {
        $fileTmpPath = $_FILES['docImage']['tmp_name'];
        $fileName = $_FILES['docImage']['name'];
        $fileSize = $_FILES['docImage']['size'];
        $fileType = $_FILES['docImage']['type'];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            die("Error: Only JPG, PNG, GIF, and WEBP images are allowed.");
        }

        $uploadDir = 'uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = uniqid('doc_', true) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $stmt = $conn->prepare("INSERT INTO documents (resident_name, file_path, purpose, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $resident, $destPath, $purpose);

            if ($stmt->execute()) {
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
