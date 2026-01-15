<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized");
}

if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $id = intval($_GET['delete_id']);
    $type = $_GET['type'];
    $table = ($type == 'maintenance') ? 'maintenance_profiles' : 'vaccination_records';
    
    $query = "DELETE FROM $table WHERE id = $id";
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

    if ($form_type == 'maintenance') {
        $res_name = mysqli_real_escape_string($conn, $_POST['resident_name']);
        $condition = mysqli_real_escape_string($conn, $_POST['medical_condition']);
        $medicine = mysqli_real_escape_string($conn, $_POST['medicine']);
        $checkup = $_POST['last_checkup'];
        $status = $_POST['status'];

        if (!empty($record_id)) {
            $query = "UPDATE maintenance_profiles SET 
                      resident_name='$res_name', 
                      medical_condition='$condition', 
                      medicine='$medicine', 
                      last_checkup='$checkup', 
                      status='$status' 
                      WHERE id=$record_id";
        } else {
            $query = "INSERT INTO maintenance_profiles (resident_name, medical_condition, medicine, last_checkup, status) 
                      VALUES ('$res_name', '$condition', '$medicine', '$checkup', '$status')";
        }
    } 
    
    elseif ($form_type == 'vaccine') {
        $res_name = mysqli_real_escape_string($conn, $_POST['resident_name']);
        $v_type = mysqli_real_escape_string($conn, $_POST['vaccine_type']);
        $dose = mysqli_real_escape_string($conn, $_POST['dose']);
        $date = $_POST['date_administered'];
        $status = $_POST['status'];

        if (!empty($record_id)) {
            $query = "UPDATE vaccination_records SET 
                      resident_name='$res_name', 
                      vaccine_type='$v_type', 
                      dose='$dose', 
                      date_administered='$date', 
                      status='$status' 
                      WHERE id=$record_id";
        } else {
            $query = "INSERT INTO vaccination_records (resident_name, vaccine_type, dose, date_administered, status) 
                      VALUES ('$res_name', '$v_type', '$dose', '$date', '$status')";
        }
    }

    if (mysqli_query($conn, $query)) {
        header("Location: health_tracking.php?msg=saved");
    } else {
        echo "Error saving record: " . mysqli_error($conn);
    }
}
?>