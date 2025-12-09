<?php
include __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: residents.php');
    exit();
}

$person_id = $conn->real_escape_string($_POST['person_id']);

$first_name = $conn->real_escape_string($_POST['first_name']);
$middle_name = $conn->real_escape_string($_POST['middle_name'] ?? '');
$surname = $conn->real_escape_string($_POST['surname']);
$suffix = $conn->real_escape_string($_POST['suffix'] ?? '');
$sex = $conn->real_escape_string($_POST['sex']);
$birthdate = $conn->real_escape_string($_POST['birthdate']);
$civil_status = $conn->real_escape_string($_POST['civil_status']);
$nationality = $conn->real_escape_string($_POST['nationality'] ?? '');
$religion = $conn->real_escape_string($_POST['religion'] ?? '');
$children_count = $conn->real_escape_string($_POST['children_count'] ?? '0');

$household_id = $conn->real_escape_string($_POST['household_id'] ?? '');
$purok = $conn->real_escape_string($_POST['purok']);
$address = $conn->real_escape_string($_POST['address']);

$education_level = $conn->real_escape_string($_POST['education_level'] ?? '');
$occupation = $conn->real_escape_string($_POST['occupation'] ?? '');

$is_senior = $conn->real_escape_string($_POST['is_senior']);
$is_disabled = $conn->real_escape_string($_POST['is_disabled']);
$is_pregnant = $conn->real_escape_string($_POST['is_pregnant']);
$vaccination = $conn->real_escape_string($_POST['vaccination']);
$health_insurance = $conn->real_escape_string($_POST['health_insurance'] ?? '');

$sql = "
    UPDATE residents SET
        first_name = '$first_name',
        middle_name = '$middle_name',
        surname = '$surname',
        suffix = '$suffix',
        sex = '$sex',
        birthdate = '$birthdate',
        civil_status = '$civil_status',
        nationality = '$nationality',
        religion = '$religion',
        children_count = '$children_count',
        household_id = '$household_id',
        purok = '$purok',
        address = '$address',
        education_level = '$education_level',
        occupation = '$occupation',
        is_senior = '$is_senior',
        is_disabled = '$is_disabled',
        is_pregnant = '$is_pregnant',
        vaccination = '$vaccination',
        health_insurance = '$health_insurance'
    WHERE person_id = '$person_id'
";

if ($conn->query($sql) === TRUE) {
    header('Location: residents.php?update=success');
    exit();
} else {
    header('Location: residents.php?update=error&msg=' . urlencode($conn->error));
    exit();
}
?>