<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve common data
    $residentId = $conn->real_escape_string($_POST['resident_id']);

    // --- Identification Table Data ---
    $respondentName = $conn->real_escape_string($_POST['RESPONDENT_NAME']);
    $gender = $conn->real_escape_string($_POST['GENDER']);
    // BIRTHDAY IS REMOVED HERE, which fixes the "Undefined array key" warning (Line 10 in your error)
    // You no longer expect 'BIRTHDAY' in the $_POST array.
    
    // Retrieve other fields (assuming they are present from your edit form):
    $address = $conn->real_escape_string($_POST['ADDRESS']);
    $province = $conn->real_escape_string($_POST['PROVINCE']);
    $municipality = $conn->real_escape_string($_POST['MUNICIPALITY']);
    $barangay = $conn->real_escape_string($_POST['BARANGAY']);
    
    // --- Demographics Table Data ---
    $purok = $conn->real_escape_string($_POST['PUROK']);
    $residentType = $conn->real_escape_string($_POST['RESIDENT_TYPE']);
    $isSenior = $conn->real_escape_string($_POST['IS_REGISTERED_SENIOR']);
    $isDisabled = $conn->real_escape_string($_POST['IS_DISABLED']);
    
    // 2. Identification Table Update (T1)
    $sql_id = "
        UPDATE identification 
        SET 
            RESPONDENT_NAME = '$respondentName', 
            GENDER = '$gender',
            -- BIRTHDAY IS REMOVED FROM THE SQL QUERY, which fixes the Fatal error (Line 25 in your error)
            ADDRESS = '$address',
            PROVINCE = '$province',
            MUNICIPALITY = '$municipality',
            BARANGAY = '$barangay'
        WHERE ID = '$residentId'
    ";
    
    // Execute Identification update
    if (!$conn->query($sql_id)) {
        // Output detailed MySQL error to help you debug
        die("Error updating identification data: " . $conn->error);
    } 

    // 3. Demographics Table Update (T2)
    
    // Check if a demographics record exists for the resident ID
    $check_sql = "SELECT MEMBER_ID FROM demographics WHERE MEMBER_ID = '$residentId'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Record exists, run UPDATE
        $sql_demo = "
            UPDATE demographics
            SET
                PUROK = '$purok',
                RESIDENT_TYPE = '$residentType',
                IS_REGISTERED_SENIOR = '$isSenior',
                IS_DISABLED = '$isDisabled'
            WHERE MEMBER_ID = '$residentId'
        ";
    } else {
        // Record does NOT exist, run INSERT
        $sql_demo = "
            INSERT INTO demographics (MEMBER_ID, PUROK, RESIDENT_TYPE, IS_REGISTERED_SENIOR, IS_DISABLED)
            VALUES ('$residentId', '$purok', '$residentType', '$isSenior', '$isDisabled')
        ";
    }

    // Execute Demographics update/insert
    if (!$conn->query($sql_demo)) {
        die("Error updating demographics data: " . $conn->error);
    }
    
    // 4. Redirect back to the residents list upon success
    header('Location: residents.php?status=updated&id=' . urlencode($residentId));
    exit();

} else {
    // Not a POST request (user accessed directly), redirect to the list
    header('Location: residents.php');
    exit();
}
?>