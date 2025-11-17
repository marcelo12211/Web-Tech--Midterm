<?php
include 'db_connect.php';

$editMode = false;
$residentId = $_GET['id'] ?? null;
$resData = [];

if ($residentId) {
    $editMode = true;
    $residentId = intval($residentId);
    $resData = $conn->query("SELECT * FROM identification WHERE id=$residentId")->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName   = $conn->real_escape_string($_POST['firstName']);
    $middleName  = $conn->real_escape_string($_POST['middleName'] ?? '');
    $lastName    = $conn->real_escape_string($_POST['lastName']);
    $suffix      = $conn->real_escape_string($_POST['suffix'] ?? '');
    $fullName    = trim("$firstName $middleName $lastName $suffix");

    $province    = $conn->real_escape_string($_POST['province']);
    $city        = $conn->real_escape_string($_POST['city']);
    $barangay    = $conn->real_escape_string($_POST['barangay']);
    $fullAddress = "$barangay, $city, $province";

    $gender      = $_POST['gender'];
    $civilStatus = $_POST['civilStatus'];
    $birthDate   = $_POST['birthDate'];
    $citizenship = $conn->real_escape_string($_POST['citizenship']);

    $isPWD = isset($_POST['isPWD']) ? 1 : 0;
    $pwdFilePath = $resData['pwdImage'] ?? null;

    // Handle PWD image upload
    if ($isPWD && isset($_FILES['pwdImage']) && $_FILES['pwdImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/pwd_ids/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileTmp  = $_FILES['pwdImage']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['pwdImage']['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $filePath)) {
            $pwdFilePath = $filePath;
        } else {
            $error = "Failed to upload PWD ID.";
        }
    }

    if (!isset($error)) {
        if ($editMode) {
            $sql = "UPDATE identification SET 
                RESPONDENT_NAME='$fullName',
                PROVINCE='$province',
                MUNICIPALITY='$city',
                BARANGAY='$barangay',
                ADDRESS='$fullAddress',
                GENDER='$gender',
                BIRTHDATE='$birthDate',
                CIVIL_STATUS='$civilStatus',
                CITIZENSHIP='$citizenship',
                isPWD=$isPWD,
                pwdImage='$pwdFilePath'
                WHERE id=$residentId";
            $actionMsg = "updated";
        } else {
            $sql = "INSERT INTO identification
                (PROVINCE, MUNICIPALITY, BARANGAY, ADDRESS, RESPONDENT_NAME, GENDER, BIRTHDATE, CIVIL_STATUS, CITIZENSHIP, HOUSEHOLD_HEAD, HOUSEHOLD_MEMBERS, isPWD, pwdImage)
                VALUES ('$province','$city','$barangay','$fullAddress','$fullName','$gender','$birthDate','$civilStatus','$citizenship','$fullName',1,$isPWD,'$pwdFilePath')";
            $actionMsg = "added";
        }

        if ($conn->query($sql)) {
            $success = "Resident $actionMsg successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
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
            <li><a href="residents.php">Residents</a></li>
            <li><a href="addnewresidents.php" class="active"><?php echo $editMode ? "Edit" : "Add"; ?> Resident</a></li>
            <li><a href="deaths.php">Deaths</a></li>
            <li><a href="documents.php">Documents</a></li>
        </ul>
    </nav>
</aside>

<div class="main-content">
<header class="topbar">
  <div class="topbar-right">
    <span id="userName" class="user-info">Welcome, User</span>
    <button id="logoutBtn" class="btn logout-btn">Logout</button>
  </div>
</header>

<main class="page-content">
<div class="add-resident-form-container">
<div class="form-modal-card">
<a href="residents.php" class="close-btn">&times;</a>

<?php
if(isset($success)) echo "<p style='color:green;'>$success</p>";
if(isset($error)) echo "<p style='color:red;'>$error</p>";
?>

<form id="addResidentForm" method="POST" enctype="multipart/form-data">
<h2 class="form-title"><?php echo $editMode ? "Edit" : "Add New"; ?> Resident</h2>

<!-- PAGE 1 -->
<div id="page1">
<h2>A. Identification</h2>
<div class="form-grid">

<div class="input-group">
  <label>First Name</label>
  <input type="text" name="firstName" id="firstName" required value="<?php echo $resData['RESPONDENT_NAME'] ?? ''; ?>">
</div>
<div class="input-group">
  <label>Middle Name</label>
  <input type="text" name="middleName" id="middleName" value="<?php echo $resData['middleName'] ?? ''; ?>">
</div>
<div class="input-group">
  <label>Surname</label>
  <input type="text" name="lastName" id="lastName" required value="<?php echo $resData['lastName'] ?? ''; ?>">
</div>
<div class="input-group">
  <label>Suffix</label>
  <input type="text" name="suffix" id="suffix" value="<?php echo $resData['suffix'] ?? ''; ?>">
</div>
<div class="input-group">
  <label>Province</label>
  <input type="text" name="province" id="province" required value="<?php echo $resData['PROVINCE'] ?? ''; ?>">
</div>
<div class="input-group">
  <label>City/Municipality</label>
  <input type="text" name="city" id="city" required value="<?php echo $resData['MUNICIPALITY'] ?? ''; ?>">
</div>
<div class="input-group">
  <label>Barangay</label>
  <input type="text" name="barangay" id="barangay" required value="<?php echo $resData['BARANGAY'] ?? ''; ?>">
</div>

</div>
<div class="button-group">
  <button type="button" id="nextBtn1" class="btn">Next</button>
</div>
</div>

<!-- PAGE 2 -->
<div id="page2" style="display:none">
<h2>B. Basic Details</h2>
<div class="form-grid">

<div class="input-group">
  <label>Birth Date</label>
  <input type="date" name="birthDate" required value="<?php echo $resData['BIRTHDATE'] ?? ''; ?>">
</div>

<div class="input-group">
  <label>Gender</label>
  <select name="gender" required>
    <option value="">Select</option>
    <option value="Male" <?php if(($resData['GENDER'] ?? '')=='Male') echo 'selected'; ?>>Male</option>
    <option value="Female" <?php if(($resData['GENDER'] ?? '')=='Female') echo 'selected'; ?>>Female</option>
  </select>
</div>

<div class="input-group">
  <label>Civil Status</label>
  <select name="civilStatus" required>
    <option value="">Select</option>
    <option value="Single" <?php if(($resData['CIVIL_STATUS'] ?? '')=='Single') echo 'selected'; ?>>Single</option>
    <option value="Married" <?php if(($resData['CIVIL_STATUS'] ?? '')=='Married') echo 'selected'; ?>>Married</option>
    <option value="Widowed" <?php if(($resData['CIVIL_STATUS'] ?? '')=='Widowed') echo 'selected'; ?>>Widowed</option>
  </select>
</div>

<div class="input-group">
  <label>Citizenship</label>
  <select name="citizenship" required>
    <option value="">Select</option>
    <option value="Filipino" <?php if(($resData['CITIZENSHIP'] ?? '')=='Filipino') echo 'selected'; ?>>Filipino</option>
    <option value="American" <?php if(($resData['CITIZENSHIP'] ?? '')=='American') echo 'selected'; ?>>American</option>
    <option value="Canadian" <?php if(($resData['CITIZENSHIP'] ?? '')=='Canadian') echo 'selected'; ?>>Canadian</option>
    <option value="Japanese" <?php if(($resData['CITIZENSHIP'] ?? '')=='Japanese') echo 'selected'; ?>>Japanese</option>
    <option value="Korean" <?php if(($resData['CITIZENSHIP'] ?? '')=='Korean') echo 'selected'; ?>>Korean</option>
    <option value="Chinese" <?php if(($resData['CITIZENSHIP'] ?? '')=='Chinese') echo 'selected'; ?>>Chinese</option>
    <option value="Australian" <?php if(($resData['CITIZENSHIP'] ?? '')=='Australian') echo 'selected'; ?>>Australian</option>
    <option value="British" <?php if(($resData['CITIZENSHIP'] ?? '')=='British') echo 'selected'; ?>>British</option>
  </select>
</div>


<div class="input-group" style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; align-items: center;">
  <div>
    <label>Are you a PWD?</label>
    <input type="checkbox" name="isPWD" id="isPWD" value="1"
      <?php if(!empty($resData['isPWD']) && $resData['isPWD']==1) echo 'checked'; ?>>
  </div>

  <div id="pwdUploadGroup" style="display:none;">
    <label>Upload PWD ID</label>
    <input type="file" name="pwdImage" id="pwdImage" accept="image/*">
    <?php if(!empty($resData['pwdImage'])) echo "<p>Current: <a href='{$resData['pwdImage']}' target='_blank'>View</a></p>"; ?>
  </div>
</div>


<div class="button-group">
  <button type="button" id="backBtn2" class="btn secondary">Back</button>
  <button type="submit" class="btn"><?php echo $editMode ? "Update" : "Submit"; ?></button>
</div>
</div>

</form>
</div>
</div>
</main>
</div>
</div>

<script>
// Multi-page navigation
document.getElementById("nextBtn1").onclick = function() {
    document.getElementById("page1").style.display = "none";
    document.getElementById("page2").style.display = "block";
};
document.getElementById("backBtn2").onclick = function() {
    document.getElementById("page2").style.display = "none";
    document.getElementById("page1").style.display = "block";
};

// Show page 1 at start
document.getElementById("page1").style.display = "block";
document.getElementById("page2").style.display = "none";

// PWD toggle
const isPWDCheckbox = document.getElementById("isPWD");
const pwdUploadGroup = document.getElementById("pwdUploadGroup");

function togglePWD() {
    if (isPWDCheckbox.checked) pwdUploadGroup.style.display = "block";
    else pwdUploadGroup.style.display = "none";
}
isPWDCheckbox.addEventListener("change", togglePWD);

// Show upload if editing and isPWD
togglePWD();
</script>
</body>
</html>
