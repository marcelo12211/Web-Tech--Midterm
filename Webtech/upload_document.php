<?php
include __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident = htmlspecialchars($_POST['resident_name']);
    $purpose = htmlspecialchars($_POST['purpose']);
    $doc_number = htmlspecialchars($_POST['docNumber']);

    //   'docFile' to match the form
    if (isset($_FILES['docFile']) && $_FILES['docFile']['error'] === 0) {
        $fileTmpPath = $_FILES['docFile']['tmp_name'];
        $fileName = $_FILES['docFile']['name'];
        $fileSize = $_FILES['docFile']['size'];
        $fileType = $_FILES['docFile']['type'];

        // Allow PDFs and other document types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            header("Location: documents.php?error=" . urlencode("Only JPG, PNG, GIF, WEBP, PDF, DOC, and DOCX files are allowed."));
            exit();
        }

        // Check file size (10MB max)
        if ($fileSize > 10485760) {
            header("Location: documents.php?error=" . urlencode("File size must be less than 10MB."));
            exit();
        }

        $uploadDir = 'uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = uniqid('doc_', true) . '.' . $fileExtension;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            //  Insert with doc_number and file_type
            $stmt = $conn->prepare("INSERT INTO documents (resident_name, doc_number, file_path, file_type, purpose, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("sssss", $resident, $doc_number, $destPath, $fileExtension, $purpose);

            if ($stmt->execute()) {
                header("Location: documents.php?success=1");
                exit();
            } else {
                header("Location: documents.php?error=" . urlencode("Database error: " . $stmt->error));
                exit();
            }
            $stmt->close();
        } else {
            header("Location: documents.php?error=" . urlencode("Error moving uploaded file. Check directory permissions."));
            exit();
        }
    } else {
        // error handling
        $error_code = isset($_FILES['docFile']['error']) ? $_FILES['docFile']['error'] : 'No file uploaded';
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        $error_msg = isset($error_messages[$error_code]) ? $error_messages[$error_code] : "Unknown upload error (Code: $error_code)";
        header("Location: documents.php?error=" . urlencode($error_msg));
        exit();
    }
} else {
    header("Location: documents.php?error=" . urlencode("Invalid request method."));
    exit();
}
?>
