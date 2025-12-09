<?php
include 'db_connect.php';

$editMode = false;
$residentId = $_GET['id'] ?? null;
$resData = [];

if ($residentId) {
    $editMode = true;
    $residentId = intval($residentId);
    $resData = $conn->query("SELECT * FROM residents WHERE resident_id=$residentId")->fetch_assoc();
}

$householdsResult = $conn->query("SELECT household_id, household_head FROM household ORDER BY household_id ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName   = $conn->real_escape_string($_POST['firstName']);
    $middleName  = $conn->real_escape_string($_POST['middleName'] ?? '');
    $lastName    = $conn->real_escape_string($_POST['lastName']);
    $suffix      = $conn->real_escape_string($_POST['suffix'] ?? '');
    $fullName    = trim("$firstName $middleName $lastName $suffix");

    $householdOption = $_POST['householdOption'];
    $newHouseholdHead = null;
    $householdId = null;

    if ($householdOption == 'existing') {
        $householdId = intval($_POST['existingHousehold']);
    } else {
        $newHouseholdHead = $fullName;
        $conn->query("INSERT INTO household (household_head) VALUES ('$newHouseholdHead')");
        $householdId = $conn->insert_id;
    }

    $purok      = $_POST['purok'];
    $address    = $conn->real_escape_string($_POST['address']);
    $birthDate  = $_POST['birthDate'];
    $gender     = $_POST['gender'];
    $civilStatus= $_POST['civilStatus'];
    $education  = $_POST['educationLevel'];
    $occupation = $_POST['occupation'];
    $isSenior   = isset($_POST['isSenior']) ? 1 : 0;
    $isPWD      = isset($_POST['isPWD']) ? 1 : 0;
    $isPregnant = isset($_POST['isPregnant']) ? 1 : 0;
    $childrenCount = intval($_POST['childrenCount'] ?? 0);
    $healthInsurance = $_POST['healthInsurance'] ?? '';
    $vaccination = $_POST['vaccination'] ?? '';

    $pwdFilePath = $resData['pwd_image'] ?? null;

    if ($isPWD && isset($_FILES['pwdImage']) && $_FILES['pwdImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/pwd_ids/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileTmp  = $_FILES['pwdImage']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['pwdImage']['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $filePath)) {
            $pwdFilePath = $filePath;
        }
    }

    if ($editMode) {
        $sql = "UPDATE residents SET
                household_id=$householdId,
                first_name='$firstName',
                middle_name='$middleName',
                surname='$lastName',
                suffix='$suffix',
                sex='$gender',
                birthdate='$birthDate',
                civil_status='$civilStatus',
                purok='$purok',
                address='$address',
                education_level='$education',
                occupation='$occupation',
                is_senior=$isSenior,
                is_disabled=$isPWD,
                is_pregnant=$isPregnant,
                children_count=$childrenCount,
                health_insurance='$healthInsurance',
                vaccination='$vaccination',
                pwd_image='$pwdFilePath'
                WHERE resident_id=$residentId";
        $actionMsg = "updated";
    } else {
        $sql = "INSERT INTO residents 
                (household_id, first_name, middle_name, surname, suffix, sex, birthdate, civil_status, purok, address, education_level, occupation, is_senior, is_disabled, is_pregnant, children_count, health_insurance, vaccination, pwd_image)
                VALUES
                ($householdId, '$firstName', '$middleName', '$lastName', '$suffix', '$gender', '$birthDate', '$civilStatus', '$purok', '$address', '$education', '$occupation', $isSenior, $isPWD, $isPregnant, $childrenCount, '$healthInsurance', '$vaccination', '$pwdFilePath')";
        $actionMsg = "added";
    }

    if ($conn->query($sql)) {
        $success = "Resident $actionMsg successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $editMode ? "Edit" : "Add"; ?> Resident</title>
<link rel="stylesheet" href="css/style.css">
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

<div class="form-grid">

<!-- Household Selection -->
<div class="input-group">
<label>Household</label>
<select name="householdOption" id="householdOption" required>
    <option value="">Select</option>
    <option value="existing" <?php if(isset($_POST['householdOption']) && $_POST['householdOption']=='existing') echo 'selected'; ?>>Existing Household</option>
    <option value="new" <?php if(isset($_POST['householdOption']) && $_POST['householdOption']=='new') echo 'selected'; ?>>New Household</option>
</select>
</div>

<div class="input-group" id="existingHouseholdGroup" style="display:none;">
<label>Select Household</label>
<select name="existingHousehold">
<?php while($household = $householdsResult->fetch_assoc()): ?>
    <option value="<?php echo $household['household_id']; ?>" <?php if(($resData['household_id'] ?? '')==$household['household_id']) echo 'selected'; ?>>
        <?php echo $household['household_head'] . " (" . $household['household_id'] . ")"; ?>
    </option>
<?php endwhile; ?>
</select>
</div>

<!-- Resident Details -->
<div class="input-group">
<label>First Name</label>
<input type="text" name="firstName" required value="<?php echo $resData['first_name'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Middle Name</label>
<input type="text" name="middleName" value="<?php echo $resData['middle_name'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Last Name</label>
<input type="text" name="lastName" required value="<?php echo $resData['surname'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Suffix</label>
<input type="text" name="suffix" value="<?php echo $resData['suffix'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Purok</label>
<select name="purok" required>
    <option value="">Select</option>
    <?php for($i=1;$i<=5;$i++): ?>
    <option value="<?php echo $i; ?>" <?php if(($resData['purok']??'')==$i) echo 'selected'; ?>>Purok <?php echo $i; ?></option>
    <?php endfor; ?>
</select>
</div>
<div class="input-group">
<label>Address</label>
<input type="text" name="address" required value="<?php echo $resData['address'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Birth Date</label>
<input type="date" name="birthDate" required value="<?php echo $resData['birthdate'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Gender</label>
<select name="gender" required>
<option value="">Select</option>
<option value="M" <?php if(($resData['sex']??'')=='M') echo 'selected'; ?>>Male</option>
<option value="F" <?php if(($resData['sex']??'')=='F') echo 'selected'; ?>>Female</option>
</select>
</div>
<div class="input-group">
<label>Civil Status</label>
<select name="civilStatus" required>
<option value="">Select</option>
<option value="Single" <?php if(($resData['civil_status']??'')=='Single') echo 'selected'; ?>>Single</option>
<option value="Married" <?php if(($resData['civil_status']??'')=='Married') echo 'selected'; ?>>Married</option>
<option value="Widowed" <?php if(($resData['civil_status']??'')=='Widowed') echo 'selected'; ?>>Widowed</option>
</select>
</div>
<div class="input-group">
<label>Education Level</label>
<input type="text" name="educationLevel" value="<?php echo $resData['education_level'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Occupation</label>
<input type="text" name="occupation" value="<?php echo $resData['occupation'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Senior Citizen</label>
<input type="checkbox" name="isSenior" value="1" <?php if(!empty($resData['is_senior'])) echo 'checked'; ?>>
</div>
<div class="input-group">
<label>PWD</label>
<input type="checkbox" name="isPWD" id="isPWD" value="1" <?php if(!empty($resData['is_disabled'])) echo 'checked'; ?>>
</div>
<div class="input-group" id="pwdUploadGroup" style="display:none;">
<label>Upload PWD ID</label>
<input type="file" name="pwdImage" accept="image/*">
<?php if(!empty($resData['pwd_image'])) echo "<p>Current: <a href='{$resData['pwd_image']}' target='_blank'>View</a></p>"; ?>
</div>
<div class="input-group">
<label>Pregnant</label>
<input type="checkbox" name="isPregnant" value="1" <?php if(!empty($resData['is_pregnant'])) echo 'checked'; ?>>
</div>
<div class="input-group">
<label>Children Count</label>
<input type="number" name="childrenCount" min="0" value="<?php echo $resData['children_count'] ?? 0; ?>">
</div>
<div class="input-group">
<label>Health Insurance</label>
<input type="text" name="healthInsurance" value="<?php echo $resData['health_insurance'] ?? ''; ?>">
</div>
<div class="input-group">
<label>Vaccination Status</label>
<input type="text" name="vaccination" value="<?php echo $resData['vaccination'] ?? ''; ?>">
</div>

</div>

<div class="button-group">
<button type="submit" class="btn"><?php echo $editMode ? "Update" : "Submit"; ?></button>
</div>
</form>
</div>
</div>
</main>
</div>
</div>

<script>
const householdOption = document.getElementById("householdOption");
const existingHouseholdGroup = document.getElementById("existingHouseholdGroup");

function toggleHousehold() {
    if(householdOption.value === 'existing') {
        existingHouseholdGroup.style.display = 'block';
    } else {
        existingHouseholdGroup.style.display = 'none';
    }
}
householdOption.addEventListener('change', toggleHousehold);
toggleHousehold();

const isPWDCheckbox = document.getElementById("isPWD");
const pwdUploadGroup = document.getElementById("pwdUploadGroup");

function togglePWD() {
    pwdUploadGroup.style.display = isPWDCheckbox.checked ? 'block' : 'none';
}
isPWDCheckbox.addEventListener('change', togglePWD);
togglePWD();
</script>
</body>
</html>
