<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "happyhallow";
$conn = mysqli_connect($servername, $username, $password, $dbname, 3306);
mysqli_set_charset($conn, 'utf8mb4');

if (!$conn) die("Connection failed: " . mysqli_connect_error());

function clean($value) {
    return htmlspecialchars(trim($value));
}

$first  = clean($_POST['firstName']);
$middle = clean($_POST['middleName']);
$last   = clean($_POST['lastName']);
$suffix = clean($_POST['suffix']);
$province = clean($_POST['province']);
$municipality = clean($_POST['municipality']);
$barangay = clean($_POST['barangay']);
$address = clean($_POST['address']);
$household_head = clean($_POST['householdHead']);
$household_members = clean($_POST['householdMembers']);

$respondent_name = trim("$first $middle $last $suffix");

$errors = [];

if ($first === "") $errors[] = "First name is required.";
if ($last === "") $errors[] = "Last name is required.";
if ($province === "") $errors[] = "Province is required.";
if ($municipality === "") $errors[] = "Municipality is required.";
if ($barangay === "") $errors[] = "Barangay is required.";
if ($address === "") $errors[] = "Address is required.";
if ($household_head === "") $errors[] = "Household head is required.";

if ($household_members === "" || !is_numeric($household_members) || $household_members <= 0) {
    $errors[] = "Total household members must be a valid number.";
}

if (count($errors) > 0) {
    $message = implode("\\n", $errors);
    echo "<script>alert('Error:\\n$message'); window.history.back();</script>";
    exit;
}

$sql = "INSERT INTO identification 
        (PROVINCE, MUNICIPALITY, BARANGAY, ADDRESS, RESPONDENT_NAME, HOUSEHOLD_HEAD, HOUSEHOLD_MEMBERS)
        VALUES (?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssi", 
    $province,
    $municipality,
    $barangay,
    $address,
    $respondent_name,
    $household_head,
    $household_members
);

if ($stmt->execute()) {
    echo "<script>alert('Record saved successfully!'); window.location='client_main.php';</script>";
} else {
    echo "<script>alert('Database Error: {$stmt->error}'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
