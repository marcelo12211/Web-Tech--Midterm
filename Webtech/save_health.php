<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $id = intval($_GET['delete_id']);
    $type = $_GET['type'];
    
    if ($type == 'maintenance') {
        $query = "DELETE FROM maintenance_profiles WHERE id = $id";
    } else {
        $query = "DELETE FROM vaccination_records WHERE id = $id";
    }
    
    if (mysqli_query($conn, $query)) {
        header("Location: health_tracking.php?msg=deleted");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_type = $_POST['form_type'];
    $record_id = $_POST['record_id'];
    $resident_name = mysqli_real_escape_string($conn, $_POST['resident_name']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if ($form_type == 'maintenance') {
        $condition = mysqli_real_escape_string($conn, $_POST['medical_condition']);
        $medicine = mysqli_real_escape_string($conn, $_POST['medicine']);
        $last_checkup = $_POST['last_checkup'];

        if (!empty($record_id)) {
            $query = "UPDATE maintenance_profiles SET resident_name='$resident_name', medical_condition='$condition', medicine='$medicine', last_checkup='$last_checkup', status='$status' WHERE id=$record_id";
        } else {
            $query = "INSERT INTO maintenance_profiles (resident_name, medical_condition, medicine, last_checkup, status) VALUES ('$resident_name', '$condition', '$medicine', '$last_checkup', '$status')";
        }
    } else {
        $vaccine_type = mysqli_real_escape_string($conn, $_POST['vaccine_type']);
        $dose = mysqli_real_escape_string($conn, $_POST['dose']);
        $date_administered = $_POST['date_administered'];

        if (!empty($record_id)) {
            $query = "UPDATE vaccination_records SET resident_name='$resident_name', vaccine_type='$vaccine_type', dose='$dose', date_administered='$date_administered', status='$status' WHERE id=$record_id";
        } else {
            $query = "INSERT INTO vaccination_records (resident_name, vaccine_type, dose, date_administered, status) VALUES ('$resident_name', '$vaccine_type', '$dose', '$date_administered', '$status')";
        }
    }

    if (mysqli_query($conn, $query)) {
        header("Location: health_tracking.php?msg=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>