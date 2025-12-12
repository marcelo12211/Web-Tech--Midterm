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
    $religion         = $conn->real_escape_string($_POST['religion'] ?? '');
    $purok            = intval($_POST['purok']);
    $address          = $conn->real_escape_string($_POST['address']);
    $education_level  = $conn->real_escape_string($_POST['education_level']);
    $occupation       = $conn->real_escape_string($_POST['occupation']);
    $vaccination      = $conn->real_escape_string($_POST['vaccination'] ?? '');
    $children_count   = intval($_POST['children_count'] ?? 0); 

    $special_status   = $conn->real_escape_string($_POST['special_status'] ?? 'None');

    $is_senior        = 0;
    $is_disabled      = 0;
    $is_pregnant      = 0;
    $health_insurance = 'N/A';

    switch ($special_status) {
        case 'Senior Citizen':
            $is_senior = 1;
            $health_insurance = 'Senior';
            break;
        case 'PWD':
            $is_disabled = 1;
            $health_insurance = 'PWD';
            break;
        case 'Pregnant':
            $is_pregnant = 1;
            $health_insurance = 'Pregnant';
            break;
        case 'Others':
            $health_insurance = 'Others';
            break;
        default:
            $health_insurance = 'N/A';
            break;
    }

    $current_date = date('Y-m-d');
    $religion_value = empty($religion) ? "''" : "'$religion'";

    if ($editMode) {
        $residency_update = "";
        if (empty($resData['residency_start_date']) || $resData['residency_start_date'] == '0000-00-00') {
             $residency_update = ", residency_start_date='$current_date'";
        }
        
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
            religion=$religion_value,
            purok=$purok,
            address='$address',
            education_level='$education_level',
            occupation='$occupation',
            vaccination='$vaccination',
            is_senior=$is_senior,
            is_disabled=$is_disabled,
            is_pregnant=$is_pregnant,
            health_insurance='$health_insurance', 
            children_count=$children_count
            $residency_update 
            WHERE person_id=$residentId";
        $actionMsg = "updated";
        
        if ($conn->query($sql)) {
            $_SESSION['status_success'] = "Resident $actionMsg successfully!";
            header("Location: residents.php"); 
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $sql = "INSERT INTO residents 
            (household_id, first_name, middle_name, surname, suffix, sex, birthdate, civil_status, nationality, religion, purok, address, education_level, occupation, vaccination, is_senior, is_disabled, is_pregnant, residency_start_date, health_insurance, children_count)
            VALUES 
            ($household_id, '$first_name', '$middle_name', '$surname', '$suffix', '$sex', '$birthdate', '$civil_status', '$nationality', $religion_value, $purok, '$address', '$education_level', '$occupation', '$vaccination', $is_senior, $is_disabled, $is_pregnant, '$current_date', '$health_insurance', $children_count)"; 
        
        if ($conn->query($sql)) {
            $new_resident_id = $conn->insert_id;
            
            if ($special_status === 'PWD') {
                $pwd_gov_id = $conn->real_escape_string($_POST['pwd_gov_id'] ?? '');
                $disability_type = $conn->real_escape_string($_POST['disability_type'] ?? '');
                
                $pwd_image_path = null;
                
                if (isset($_FILES['pwd_id_image']) && $_FILES['pwd_id_image']['error'] === 0) {
                    $fileTmpPath = $_FILES['pwd_id_image']['tmp_name'];
                    $fileName = $_FILES['pwd_id_image']['name'];
                    $fileSize = $_FILES['pwd_id_image']['size'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    
                    if (in_array($fileExtension, $allowedExtensions) && $fileSize <= 5242880) { // 5MB
                        $uploadDir = 'uploads/pwd_documents/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        
                        $newFileName = 'pwd_' . $new_resident_id . '_' . uniqid() . '.' . $fileExtension;
                        $destPath = $uploadDir . $newFileName;
                        
                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $pwd_image_path = $destPath;
                        }
                    }
                }
                
                $stmt = $conn->prepare("INSERT INTO disabled_persons (resident_id, pwd_gov_id, disability_type, id_picture_path, date_registered) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $new_resident_id, $pwd_gov_id, $disability_type, $pwd_image_path, $current_date);
                $stmt->execute();
                $stmt->close();
            }
            
            $actionMsg = "added";
            $_SESSION['status_success'] = "Resident $actionMsg successfully!";
            header("Location: residents.php"); 
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

function getSelectedStatus($resData) {
    if (!empty($resData['is_senior'])) return 'Senior Citizen';
    if (!empty($resData['is_disabled'])) return 'PWD';
    if (!empty($resData['is_pregnant'])) return 'Pregnant';
    if (($resData['health_insurance'] ?? '') === 'Others') return 'Others';
    return 'None';
}
$current_status = getSelectedStatus($resData);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $editMode ? "Edit" : "Add"; ?> Resident</title>
<link rel="stylesheet" href="css/style.css" />
<style>
.pwd-section {
    display: none;
    grid-column: 1 / -1;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px solid #ffc107;
    margin-top: 10px;
}
.pwd-section.show {
    display: block;
}
.pwd-section h4 {
    color: #333;
    margin-bottom: 15px;
    font-size: 1.1rem;
}
.pwd-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
</style>
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

<form id="addResidentForm" method="POST" enctype="multipart/form-data">
<h2 class="form-title"><?php echo $editMode ? "Edit Resident (ID: " . htmlspecialchars($residentId) . ")" : "Add New Resident"; ?></h2>

<div class="form-grid">

<div class="input-group">
    <label>Household</label>
    <select name="household_id" required>
        <option value="">Select Household</option>
        <?php 
        if ($householdResult->num_rows > 0) {
            $householdResult->data_seek(0);
        }
        while($row = $householdResult->fetch_assoc()): ?>
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
  <label>Religion (Optional)</label>
  <input type="text" name="religion" value="<?php echo htmlspecialchars($resData['religion'] ?? ''); ?>">
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

<div class="input-group" style="grid-column: 1 / -1;">
  <label>Special Status / Health Insurance</label>
  <select name="special_status" id="specialStatus" onchange="togglePWDSection()">
    <option value="None" <?php if($current_status == 'None') echo 'selected'; ?>>None</option>
    <option value="Senior Citizen" <?php if($current_status == 'Senior Citizen') echo 'selected'; ?>>Senior Citizen</option>
    <option value="PWD" <?php if($current_status == 'PWD') echo 'selected'; ?>>Person with Disability (PWD)</option>
    <option value="Pregnant" <?php if($current_status == 'Pregnant') echo 'selected'; ?>>Pregnant</option>
    <option value="Others" <?php if($current_status == 'Others') echo 'selected'; ?>>Others (Specify in notes if needed)</option>
  </select>
</div>

<?php if (!$editMode): ?>
<div class="pwd-section" id="pwdSection">
    <h4>📋 PWD Information & Document Upload</h4>
    <div class="pwd-fields">
        <div class="input-group">
            <label>PWD Government ID Number *</label>
            <input type="text" name="pwd_gov_id" id="pwdGovId" placeholder="e.g., PWD-2024-12345">
        </div>
        
        <div class="input-group">
            <label>Disability Type *</label>
            <select name="disability_type" id="disabilityType">
                <option value="">Select Disability Type</option>
                <option value="Visual Impairment">Visual Impairment</option>
                <option value="Hearing Impairment">Hearing Impairment</option>
                <option value="Physical Disability">Physical Disability</option>
                <option value="Intellectual Disability">Intellectual Disability</option>
                <option value="Mental Disability">Mental Disability</option>
                <option value="Multiple Disabilities">Multiple Disabilities</option>
                <option value="Others">Others</option>
            </select>
        </div>
        
        <div class="input-group" style="grid-column: 1 / -1;">
            <label>Upload PWD ID Image (JPG, PNG only) *</label>
            <input type="file" name="pwd_id_image" id="pwdIdImage" accept="image/jpeg,image/jpg,image/png">
            <small style="color: #6c757d; margin-top: 5px; display: block;">Maximum file size: 5MB</small>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="input-group">
  <label>Number of Children (Optional)</label>
  <input type="number" name="children_count" value="<?php echo htmlspecialchars($resData['children_count'] ?? '0'); ?>">
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
function togglePWDSection() {
    const specialStatus = document.getElementById('specialStatus').value;
    const pwdSection = document.getElementById('pwdSection');
    
    if (pwdSection) {
        if (specialStatus === 'PWD') {
            pwdSection.classList.add('show');
            document.getElementById('pwdGovId').required = true;
            document.getElementById('disabilityType').required = true;
            document.getElementById('pwdIdImage').required = true;
        } else {
            pwdSection.classList.remove('show');
            document.getElementById('pwdGovId').required = false;
            document.getElementById('disabilityType').required = false;
            document.getElementById('pwdIdImage').required = false;
        }
    }
}

window.onload = function() {
    togglePWDSection();
    setupLogout();
};

function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    logoutBtn.addEventListener("click", () => {
        window.location.href = "logout.php"; 
    });
}
</script>
</body>
</html>
