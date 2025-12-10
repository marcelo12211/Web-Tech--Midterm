<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); 
header('Pragma: no-cache');   
header('Expires: 0');         
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

include 'db_connect.php';

$editMode = false;
$residentId = isset($_GET['id']) ? intval($_GET['id']) : null;
$resData = [];

if ($residentId) {
    $editMode = true;
    $stmt = $conn->prepare("SELECT * FROM residents WHERE person_id = ?");
    $stmt->bind_param("i", $residentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $resData = $result->fetch_assoc();
    $stmt->close();
    if (!$resData) {
        header("Location: residents.php");
        exit();
    }
}
$householdResult = $conn->query("SELECT household_id, household_head FROM household ORDER BY household_id ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $household_id     = intval($_POST['household_id']);
    $first_name       = $conn->real_escape_string($_POST['first_name']);
    $middle_name      = $conn->real_escape_string($_POST['middle_name'] ?? '');
    $surname          = $conn->real_escape_string($_POST['surname']);
    $suffix           = $conn->real_escape_string($_POST['suffix'] ?? '');
    $sex              = $conn->real_escape_string($_POST['sex']);
    $birthdate        = $conn->real_escape_string($_POST['birthdate']);
    $civil_status     = $conn->real_escape_string($_POST['civil_status']);
    $nationality      = $conn->real_escape_string($_POST['nationality']);
    $purok            = intval($_POST['purok']);
    $address          = $conn->real_escape_string($_POST['address']);
    $education_level  = $conn->real_escape_string($_POST['education_level']);
    $occupation       = $conn->real_escape_string($_POST['occupation']);
    $vaccination      = $conn->real_escape_string($_POST['vaccination']);

    $is_senior        = isset($_POST['is_senior']) ? 1 : 0;
    $is_disabled      = isset($_POST['is_disabled']) ? 1 : 0;
    $is_pregnant      = isset($_POST['is_pregnant']) ? 1 : 0;

    if ($editMode) {
        $sql = "UPDATE residents SET 
            household_id=$household_id,
            first_name='$first_name',
            middle_name='$middle_name',
            surname='$surname',
            suffix='$suffix',
            sex='$sex',
            birthdate='$birthdate',
            civil_status='$civil_status',
            nationality='$nationality',
            purok=$purok,
            address='$address',
            education_level='$education_level',
            occupation='$occupation',
            vaccination='$vaccination',
            is_senior=$is_senior,
            is_disabled=$is_disabled,
            is_pregnant=$is_pregnant
            WHERE person_id=$residentId";
        $actionMsg = "updated";
    } else {
        $sql = "INSERT INTO residents 
            (household_id, first_name, middle_name, surname, suffix, sex, birthdate, civil_status, nationality, purok, address, education_level, occupation, vaccination, is_senior, is_disabled, is_pregnant)
            VALUES 
            ($household_id, '$first_name', '$middle_name', '$surname', '$suffix', '$sex', '$birthdate', '$civil_status', '$nationality', $purok, '$address', '$education_level', '$occupation', '$vaccination', $is_senior, $is_disabled, $is_pregnant)";
        $actionMsg = "added";
    }

    if ($conn->query($sql)) {
        $_SESSION['status_success'] = "Resident $actionMsg successfully!";
        header("Location: residents.php"); 
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $editMode ? "Edit" : "Add"; ?> Resident</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-container">
<aside class="sidebar">
    <div class="logo">Happy Hallow Barangay System</div>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="household.php">Households</a></li>
            <li><a href="residents.php">Residents</a></li>
            <li><a href="<?php echo $editMode ? "edit_resident.php?id=$residentId" : "addnewresidents.php"; ?>" class="active"><?php echo $editMode ? "Edit Resident" : "Add Resident"; ?></a></li>
            <li><a href="deaths.php">Deaths</a></li>
            <li><a href="documents.php">Documents</a></li>
        </ul>
    </nav>
</aside>

<div class="main-content">
<header class="topbar">
  <div class="topbar-right">
    <span id="userName" class="user-info">Welcome, <?php echo htmlspecialchars($logged_in_username); ?></span>
    <button id="logoutBtn" class="btn logout-btn">Logout</button>
  </div>
</header>

<main class="page-content">
<div class="add-resident-form-container">
<div class="form-modal-card">
<a href="residents.php" class="close-btn">&times;</a>

<?php
if(isset($error)) echo "<p style='color:red;'>$error</p>";
?>

<form id="addResidentForm" method="POST">
<h2 class="form-title"><?php echo $editMode ? "Edit Resident (ID: " . htmlspecialchars($residentId) . ")" : "Add New Resident"; ?></h2>

<div class="form-grid">

<div class="input-group">
    <label>Household</label>
    <select name="household_id" required>
        <option value="">Select Household</option>
        <?php while($row = $householdResult->fetch_assoc()): ?>
            <option value="<?= $row['household_id'] ?>" <?php if(($resData['household_id'] ?? '') == $row['household_id']) echo 'selected'; ?>>
                <?= htmlspecialchars($row['household_id']) ?> - <?= htmlspecialchars($row['household_head'] ?? 'No Head') ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<div class="input-group">
  <label>First Name</label>
  <input type="text" name="first_name" required value="<?php echo htmlspecialchars($resData['first_name'] ?? ''); ?>">
</div>

<div class="input-group">
  <label>Middle Name</label>
  <input type="text" name="middle_name" value="<?php echo htmlspecialchars($resData['middle_name'] ?? ''); ?>">
</div>

<div class="input-group">
  <label>Surname</label>
  <input type="text" name="surname" required value="<?php echo htmlspecialchars($resData['surname'] ?? ''); ?>">
</div>

<div class="input-group">
  <label>Suffix</label>
  <input type="text" name="suffix" value="<?php echo htmlspecialchars($resData['suffix'] ?? ''); ?>">
</div>

<div class="input-group">
  <label>Sex</label>
  <select name="sex" required>
    <option value="">Select</option>
    <option value="Male" <?php if(($resData['sex'] ?? '')=='Male') echo 'selected'; ?>>Male</option>
    <option value="Female" <?php if(($resData['sex'] ?? '')=='Female') echo 'selected'; ?>>Female</option>
  </select>
</div>

<div class="input-group">
  <label>Birth Date</label>
  <input type="date" name="birthdate" required value="<?php echo htmlspecialchars($resData['birthdate'] ?? ''); ?>">
</div>

<div class="input-group">
  <label>Civil Status</label>
  <select name="civil_status" required>
    <option value="">Select</option>
    <option value="Single" <?php if(($resData['civil_status'] ?? '')=='Single') echo 'selected'; ?>>Single</option>
    <option value="Married" <?php if(($resData['civil_status'] ?? '')=='Married') echo 'selected'; ?>>Married</option>
    <option value="Widowed" <?php if(($resData['civil_status'] ?? '')=='Widowed') echo 'selected'; ?>>Widowed</option>
  </select>
</div>

<div class="input-group">
  <label>Nationality</label>
  <input type="text" name="nationality" required value="<?php echo htmlspecialchars($resData['nationality'] ?? 'Filipino'); ?>">
</div>

<div class="input-group">
  <label>Purok</label>
  <select name="purok" required>
    <option value="">Select</option>
    <?php for($i=1;$i<=5;$i++): ?>
      <option value="<?= $i ?>" <?php if(($resData['purok'] ?? '')==$i) echo 'selected'; ?>>Purok <?= $i ?></option>
    <?php endfor; ?>
  </select>
</div>

<div class="input-group">
  <label>Address</label>
  <input type="text" name="address" required value="<?php echo htmlspecialchars($resData['address'] ?? ''); ?>">
</div>

<div class="input-group">
  <label>Education Level</label>
  <select name="education_level" required>
    <option value="">Select</option>
    <option value="Elementary" <?php if(($resData['education_level'] ?? '')=='Elementary') echo 'selected'; ?>>Elementary</option>
    <option value="High School" <?php if(($resData['education_level'] ?? '')=='High School') echo 'selected'; ?>>High School</option>
    <option value="Vocational" <?php if(($resData['education_level'] ?? '')=='Vocational') echo 'selected'; ?>>Vocational</option>
    <option value="College" <?php if(($resData['education_level'] ?? '')=='College') echo 'selected'; ?>>College</option>
    <option value="Graduate Studies" <?php if(($resData['education_level'] ?? '')=='Graduate Studies') echo 'selected'; ?>>Graduate Studies</option>
  </select>
</div>

<div class="input-group">
  <label>Occupation</label>
  <input type="text" name="occupation" value="<?php echo htmlspecialchars($resData['occupation'] ?? ''); ?>">
</div>

<div class="input-group">
  <label>Vaccinations</label>
  <input type="text" name="vaccination" value="<?php echo htmlspecialchars($resData['vaccination'] ?? ''); ?>">
</div>

<div class="input-group checkbox-group" style="grid-column: 1 / -1; display:flex; gap:30px;">
    <label>
        <input type="checkbox" name="is_senior" <?php if(!empty($resData['is_senior'])) echo 'checked'; ?>> 
        Senior Citizen
    </label>
    <label>
        <input type="checkbox" name="is_disabled" <?php if(!empty($resData['is_disabled'])) echo 'checked'; ?>> 
        Person with Disability (PWD)
    </label>
    <label>
        <input type="checkbox" name="is_pregnant" <?php if(!empty($resData['is_pregnant'])) echo 'checked'; ?>> 
        Pregnant
    </label>
</div>

<div class="button-group" style="grid-column: 1 / -1; text-align: center;">
  <button type="submit" class="btn primary-btn"><?php echo $editMode ? "Update Resident" : "Submit Resident"; ?></button>
</div>

</div>
</form>
</div>
</div>
</main>
</div>
</div>

<script>
function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    logoutBtn.addEventListener("click", () => {
        window.location.href = "logout.php"; 
    });
}
window.onload = function () {
    setupLogout();
};
</script>
</body>
</html>