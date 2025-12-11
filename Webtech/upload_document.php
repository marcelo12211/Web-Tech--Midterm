<?php
session_start();
include 'db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: documents.php");
    exit();
}
$resident_id = $_POST['resident_id'] ?? null;
$resident_name = $_POST['resident_name'] ?? 'N/A';
$doc_number = $_POST['docNumber'] ?? null;
$doc_type = $_POST['docType'] ?? 'Other';
$purpose = $_POST['purpose'] ?? '';
$file_path = null;
$file_type = null;
$target_dir = "uploads/documents/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}
if (isset($_FILES['docImage']) && $_FILES['docImage']['error'] == 0) {
    $file = $_FILES['docImage'];
    $file_name = basename($file["name"]);
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_file_name = $doc_number . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_file_name;
    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf', 'xlsx', 'xls'];
    
    if (!in_array($file_extension, $allowed_types)) {
        $_SESSION['status_error'] = "File type not allowed. Only JPG, PNG, PDF, XLSX, XLS are accepted.";
        header("Location: documents.php");
        exit();
    }
    if ($file["size"] > 5000000) {
        $_SESSION['status_error'] = "File is too large (max 5MB).";
        header("Location: documents.php");
        exit();
    }
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $file_path = $target_file;
        $file_type = strtoupper($file_extension);
    } else {
        $_SESSION['status_error'] = "Error uploading file. Please try again.";
        header("Location: documents.php");
        exit();
    }
} 
$sql = "INSERT INTO documents (resident_id, resident_name, doc_number, purpose, file_path, file_type, doc_type) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $resident_id, $resident_name, $doc_number, $purpose, $file_path, $file_type, $doc_type);

if ($stmt->execute()) {
    $_SESSION['status_success'] = "Document record successfully saved!";
} else {
    $_SESSION['status_error'] = "Error saving document record: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: documents.php");
exit();
?>