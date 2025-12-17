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

include '../db_connect.php'; 

$error = null;
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
        $_SESSION['error_message'] = "Resident not found or invalid ID.";
        header("Location: residents.php");
        exit();
    }
}
$householdResult = $conn->query("SELECT household_id, household_head FROM household ORDER BY household_id ASC");

function getSelectedStatus($resData) {
    if (isset($resData['is_senior']) && $resData['is_senior']) return 'Senior Citizen';
    if (isset($resData['is_disabled']) && $resData['is_disabled']) return 'PWD';
    if (isset($resData['is_pregnant']) && $resData['is_pregnant']) return 'Pregnant';
    if (isset($resData['health_insurance']) && $resData['health_insurance'] === 'Others') {
    return 'Others';
}

}
$current_status = getSelectedStatus($resData);

if ($householdResult->num_rows > 0) {
    $householdResult->data_seek(0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo $editMode ? "Edit" : "Add"; ?> Resident</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<style>
:root {
    --primary-color: #226b8dff;
    --primary-dark: #226b8dff;
    --secondary-color: #5f6368;
    --danger-color: #ea4335;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --background-color: #f8f9fa;
    --card-background: #ffffff;
    --sidebar-bg: #212121;
    --text-color: #202124;
    --text-light: #5f6368;
    --border-color: #dadce0;
    --radius: 10px;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: "Roboto", Arial, sans-serif;
    background: var(--background-color);
    color: var(--text-color);
}
a { text-decoration: none; }
.app-container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background: var(--sidebar-bg);
    color: white;
}
.logo {
    padding: 25px;
    text-align: center;
    font-weight: 700;
    font-size: 1.15rem;
    line-height: 1.3;
}
.main-nav ul { list-style: none; padding: 0; margin: 0; }
.main-nav a {
    display: block;
    padding: 14px 20px;
    color: #bdc1c6;
}
.main-nav a:hover,
.main-nav a.active {
    background: var(--primary-dark);
    color: white;
}

.main-content { flex: 1; }
.topbar {
    background: white;
    padding: 15px 30px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
.topbar-right { display: flex; align-items: center; }
.user-info { margin-right: 15px; color: var(--text-light); }
.logout-btn {
    padding: 8px 15px;
    border: 1px solid var(--border-color);
    background: transparent;
    color: var(--text-color);
    font-size: 0.9rem;
    cursor: pointer;
    border-radius: 6px;
    transition: background-color 0.2s;
    font-weight: 500;
}
.logout-btn:hover { background: var(--background-color); }

.page-content { padding: 30px; }
.page-content h2 { 
    margin-top: 0; 
    margin-bottom: 25px; 
    color: var(--text-color); 
    display: flex; 
    align-items: center; 
    gap: 10px; 
}
.page-content .form-title {
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
    margin-bottom: 30px;
}

.alert-error {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
    background-color: #fce4e4;
    color: var(--danger-color);
    border: 1px solid var(--danger-color);
}

.form-card {
    background: var(--card-background);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 30px;
    max-width: 900px;
    margin: 0 auto; 
    position: relative;
}
.close-btn {
    position: absolute;
    top: 15px;
    right: 25px;
    font-size: 24px;
    font-weight: bold;
    color: var(--text-light);
    transition: color 0.2s;
}
.close-btn:hover {
    color: var(--danger-color);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}
.input-group {
    margin-bottom: 0;
}
.input-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-color);
}
.input-group input[type="text"],
.input-group input[type="date"],
.input-group input[type="number"],
.input-group input[type="email"],
.input-group select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    background-color: #ffffff;
}
.input-group input:focus,
.input-group select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(34, 107, 141, 0.2); 
}

.pwd-section, .senior-section {
    display: none;
    grid-column: 1 / -1; 
    padding: 20px;
    border-radius: 8px;
    margin-top: 10px;
}
.pwd-section {
    background: #fff3cd; 
    border: 2px solid var(--warning-color);
}
.senior-section {
    background: #d4edda;
    border: 2px solid var(--success-color);
}
.pwd-section.show, .senior-section.show {
    display: block;
}
.pwd-section h4, .senior-section h4 {
    color: var(--text-color);
    margin-bottom: 15px;
    font-size: 1.1rem;
    font-weight: 700;
}
.pwd-fields, .senior-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.button-group {
    margin-top: 30px;
    text-align: right;
}
.btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
}
.primary-btn {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}
.primary-btn:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
}

@media (max-width: 900px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    .pwd-fields, .senior-fields {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 768px) {
    .sidebar { width: 100%; height: auto; }
    .app-container { flex-direction: column; }
    .page-content { padding: 20px; }
    .form-card { padding: 20px; }
    .topbar { padding: 15px 20px; }
}
</style>
</head>
<body>
<div class="app-container">
<aside class="sidebar">
    <div class="logo">Happy Hallow<br />Barangay System</div>
    <nav class="main-nav">
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="residents.php" class="active">Manage Residents</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="documents.php">Documents</a></li>
            <li><a href="logout.php">Logout</a></li>
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
<div class="form-card">
<a href="residents.php" class="close-btn" title="Back to Residents List">&times;</a>

<h2 class="form-title">
    <i class="fas fa-user-<?php echo $editMode ? "edit" : "plus"; ?>"></i> 
    <?php echo $editMode ? "Edit Resident Data" : "Add New Resident"; ?>
</h2>

<?php
if(isset($error)) echo "<div class='alert-error'><strong>Error!</strong> " . htmlspecialchars($error) . "</div>";
?>

<form id="residentForm" enctype="multipart/form-data">

<div class="form-grid">

<div class="input-group">
    <label>Household</label>
    <select name="household_id" required>
        <option value="">Select Household</option>
        <?php while($row = $householdResult->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['household_id']) ?>" 
                <?php if(($resData['household_id'] ?? '') == $row['household_id']) echo 'selected'; ?>>
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
    <input type="text" name="vaccination" placeholder="e.g., COVID-19 (3 doses)" value="<?php echo htmlspecialchars($resData['vaccination'] ?? ''); ?>">
</div>

<div class="input-group">
    <label>Number of Children (Optional)</label>
    <input type="number" name="children_count" value="<?php echo htmlspecialchars($resData['children_count'] ?? '0'); ?>">
</div>

<div class="input-group" style="grid-column: 1 / -1;">
    <label>Special Status / Health Insurance</label>
    <select name="special_status" id="specialStatus" onchange="toggleSpecialSections()">
        <option value="None" <?php if($current_status == 'None') echo 'selected'; ?>>None</option>
        <option value="Senior Citizen" <?php if($current_status == 'Senior Citizen') echo 'selected'; ?>>Senior Citizen</option>
        <option value="PWD" <?php if($current_status == 'PWD') echo 'selected'; ?>>Person with Disability (PWD)</option>
        <option value="Pregnant" <?php if($current_status == 'Pregnant') echo 'selected'; ?>>Pregnant</option>
        <option value="Others" <?php if($current_status == 'Others') echo 'selected'; ?>>Others (Specify in notes if needed)</option>
    </select>
</div>

<?php if (!$editMode): ?>

<div class="pwd-section" id="pwdSection">
    <h4><i class="fas fa-wheelchair"></i> PWD Information & Document Upload</h4>
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
            <small style="color: var(--text-light); margin-top: 5px; display: block;">Maximum file size: 5MB</small>
        </div>
    </div>
</div>

<div class="senior-section" id="seniorSection">
    <h4><i class="fas fa-user-tie"></i> Senior Citizen Information & Document Upload</h4>
    <div class="senior-fields">
        <div class="input-group" style="grid-column: 1 / -1;">
            <label>Senior Citizen Government ID Number *</label>
            <input type="text" name="senior_gov_id" id="seniorGovId" placeholder="e.g., OSCA-2024-12345">
        </div>
        
        <div class="input-group" style="grid-column: 1 / -1;">
            <label>Upload Senior Citizen ID Image (JPG, PNG only) *</label>
            <input type="file" name="senior_id_image" id="seniorIdImage" accept="image/jpeg,image/jpg,image/png">
            <small style="color: var(--text-light); margin-top: 5px; display: block;">Maximum file size: 5MB</small>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="button-group" style="grid-column: 1 / -1;">
    <a href="residents.php" class="btn"><i class="fas fa-arrow-alt-circle-left"></i> Back to List</a>
    <button type="submit" class="btn primary-btn"><i class="fas fa-save"></i> <?php echo $editMode ? "Update Resident" : "Submit Resident"; ?></button>
</div>

</div>
</form>
</div>
</main>
</div>
</div>

<script>
document.getElementById("residentForm").addEventListener("submit", async e => {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  const res = await fetch("http://127.0.0.1:5000/admin/residents", {
    method: "POST",
    body: formData
  });

  const data = await res.json();

  if (data.success) {
    window.location.href = "residents.php";
  } else {
    alert(data.error || "Failed to add resident");
  }
});
function toggleSpecialSections() {
    const specialStatus = document.getElementById('specialStatus').value;
    const pwdSection = document.getElementById('pwdSection');
    const seniorSection = document.getElementById('seniorSection');
    
    if (pwdSection) {
        pwdSection.classList.remove('show');
        document.getElementById('pwdGovId').required = false;
        document.getElementById('disabilityType').required = false;
        document.getElementById('pwdIdImage').required = false;
        
        if (specialStatus === 'PWD') {
            pwdSection.classList.add('show');
            document.getElementById('pwdGovId').required = true;
            document.getElementById('disabilityType').required = true;
            document.getElementById('pwdIdImage').required = true;
        }
    }
    
    if (seniorSection) {
        seniorSection.classList.remove('show');
        document.getElementById('seniorGovId').required = false;
        document.getElementById('seniorIdImage').required = false;
        
        if (specialStatus === 'Senior Citizen') {
            seniorSection.classList.add('show');
            document.getElementById('seniorGovId').required = true;
            document.getElementById('seniorIdImage').required = true;
        }
    }
}

window.onload = function() {
    <?php if (!$editMode): ?>
        toggleSpecialSections(); 
    <?php endif; ?>
   
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