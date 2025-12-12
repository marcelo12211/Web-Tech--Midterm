<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

include __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: household.php');
    exit();
}

$household_id = $conn->real_escape_string($_POST['household_id']);
$household_head = $conn->real_escape_string($_POST['household_head']);
$housing_ownership = $conn->real_escape_string($_POST['housing_ownership']);
$building_type = $conn->real_escape_string($_POST['building_type']);
$water_source = $conn->real_escape_string($_POST['water_source']);
$electricity_source = $conn->real_escape_string($_POST['electricity_source']);
$toilet_facility = $conn->real_escape_string($_POST['toilet_facility']);
$waste_disposal = $conn->real_escape_string($_POST['waste_disposal']);

$sql = "
    UPDATE household SET
        household_head = '$household_head',
        housing_ownership = '$housing_ownership',
        building_type = '$building_type',
        water_source = '$water_source',
        electricity_source = '$electricity_source',
        toilet_facility = '$toilet_facility',
        waste_disposal = '$waste_disposal'
    WHERE household_id = '$household_id'
";

if ($conn->query($sql) === TRUE) {
    header('Location: household.php?update=success');
    exit();
} else {
    header('Location: household.php?update=error&msg=' . urlencode($conn->error));
    exit();
}
?>