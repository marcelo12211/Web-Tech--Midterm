<?php
include __DIR__ . '/db_connect.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: residents.php');
    exit();
}

$residentId = $conn->real_escape_string($_GET['id']);

$sql = "
    SELECT 
        person_id,
        household_id,
        first_name,
        middle_name,
        surname,
        suffix,
        sex,
        birthdate,
        civil_status,
        nationality,
        religion,
        purok,
        address,
        education_level,
        occupation,
        is_senior,
        is_disabled,
        health_insurance,
        vaccination,
        is_pregnant,
        children_count
    FROM residents
    WHERE person_id = '$residentId'
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p>Error: Resident not found.</p>";
    exit();
}

$residentData = $result->fetch_assoc();

function isSelected($currentValue, $targetValue) {
    $safeCurrentValue = $currentValue ?? '';
    return ($safeCurrentValue == $targetValue) ? 'selected' : '';
}

function safeHtml($value) {
    return htmlspecialchars($value ?? '');
}

$householdsSql = " SELECT household_id, household_head 
FROM household WHERE household_head 
IS NOT NULL ORDER BY household_head ASC ";
$householdsResult = $conn->query($householdsSql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Resident: <?php echo safeHtml($residentData['first_name'] . ' ' . $residentData['surname']); ?></title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group-full {
            grid-column: 1 / -1;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }
        .form-card {
            padding: 30px;
        }
        .section-title {
            margin-top: 30px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
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
            <li><a href="residents.php" class="active">Residents</a></li>
            <li><a href="addnewresidents.php">Add Resident</a></li>
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
            <div class="card form-card">
                <h2>Edit Resident Information</h2>
                <p>ID: <?php echo safeHtml($residentData['person_id']); ?> - <?php echo safeHtml($residentData['first_name'] . ' ' . $residentData['surname']); ?></p>
                <hr>
                
                <form action="update_resident.php" method="POST">
                    <input type="hidden" name="person_id" value="<?php echo safeHtml($residentData['person_id']); ?>">

                    <h3 class="section-title">Personal Information</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" 
                                   value="<?php echo safeHtml($residentData['first_name']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name" 
                                   value="<?php echo safeHtml($residentData['middle_name']); ?>">
                        </div>
                        
                        <div class="input-group">
                            <label for="surname">Surname *</label>
                            <input type="text" id="surname" name="surname" 
                                   value="<?php echo safeHtml($residentData['surname']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="suffix">Suffix</label>
                            <input type="text" id="suffix" name="suffix" 
                                   value="<?php echo safeHtml($residentData['suffix']); ?>" 
                                   placeholder="Jr., Sr., III, etc.">
                        </div>
                        
                        <div class="input-group">
                            <label for="sex">Sex *</label>
                            <select id="sex" name="sex" required>
                                <option value="">Select Sex</option>
                                <option value="M" <?php echo isSelected($residentData['sex'], 'M'); ?>>Male</option>
                                <option value="F" <?php echo isSelected($residentData['sex'], 'F'); ?>>Female</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="birthdate">Birthdate *</label>
                            <input type="date" id="birthdate" name="birthdate" 
                                   value="<?php echo safeHtml($residentData['birthdate']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="civil_status">Civil Status *</label>
                            <select id="civil_status" name="civil_status" required>
                                <option value="">Select Civil Status</option>
                                <option value="Single" <?php echo isSelected($residentData['civil_status'], 'Single'); ?>>Single</option>
                                <option value="Married" <?php echo isSelected($residentData['civil_status'], 'Married'); ?>>Married</option>
                                <option value="Widowed" <?php echo isSelected($residentData['civil_status'], 'Widowed'); ?>>Widowed</option>
                                <option value="Separated" <?php echo isSelected($residentData['civil_status'], 'Separated'); ?>>Separated</option>
                                <option value="Divorced" <?php echo isSelected($residentData['civil_status'], 'Divorced'); ?>>Divorced</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="nationality">Nationality</label>
                            <input type="text" id="nationality" name="nationality" 
                                   value="<?php echo safeHtml($residentData['nationality']); ?>">
                        </div>
                        
                        <div class="input-group">
                            <label for="religion">Religion</label>
                            <input type="text" id="religion" name="religion" 
                                   value="<?php echo safeHtml($residentData['religion']); ?>">
                        </div>
                        
                        <div class="input-group">
                            <label for="children_count">Number of Children</label>
                            <input type="number" id="children_count" name="children_count" 
                                   value="<?php echo safeHtml($residentData['children_count']); ?>" min="0">
                        </div>
                    </div>

                    <h3 class="section-title">Address & Location</h3>
                    <div class="form-grid">
                        <div class="input-group"> 
                            <label for="household_id">Household Head *</label> 
                            <select id="household_id" name="household_id" required> 
                                <option value="">Select household head</option> 
                                <?php while ($row = $householdsResult->fetch_assoc()) {
                                     $selected = ($row['household_id'] == $residentData['household_id']) ? 'selected' : '';
                                      echo '<option value="' 
                                      . safeHtml($row['household_id']) 
                                      . '" ' . $selected 
                                      . '>' 
                                      . safeHtml($row['household_head']) 
                                      . ' (ID ' . safeHtml($row['household_id']) 
                                      . ')' 
                                      . '</option>';
                                       } ?> </select> </div>
                        
                        <div class="input-group">
                            <label for="purok">Purok *</label>
                            <select id="purok" name="purok" required>
                                <option value="">Select Purok</option>
                                <?php for ($i = 0; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo isSelected($residentData['purok'], $i); ?>>
                                        <?php echo $i == 0 ? 'None' : "Purok $i"; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="input-group form-group-full">
                            <label for="address">Address *</label>
                            <input type="text" id="address" name="address" 
                                   value="<?php echo safeHtml($residentData['address']); ?>" required>
                        </div>
                    </div>

                    <h3 class="section-title">Education & Employment</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="education_level">Education Level</label>
                            <select id="education_level" name="education_level">
                                <option value="">Select Education Level</option>
                                <option value="Elementary" <?php echo isSelected($residentData['education_level'], 'Elementary'); ?>>Elementary</option>
                                <option value="High School" <?php echo isSelected($residentData['education_level'], 'High School'); ?>>High School</option>
                                <option value="College" <?php echo isSelected($residentData['education_level'], 'College'); ?>>College</option>
                                <option value="Vocational" <?php echo isSelected($residentData['education_level'], 'Vocational'); ?>>Vocational</option>
                                <option value="Graduate" <?php echo isSelected($residentData['education_level'], 'Graduate'); ?>>Graduate</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="occupation">Occupation</label>
                            <input type="text" id="occupation" name="occupation" 
                                   value="<?php echo safeHtml($residentData['occupation']); ?>">
                        </div>
                    </div>

                    <h3 class="section-title">Health & Status</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="is_senior">Senior Citizen?</label>
                            <select id="is_senior" name="is_senior">
                                <option value="0" <?php echo isSelected($residentData['is_senior'], '0'); ?>>No</option>
                                <option value="1" <?php echo isSelected($residentData['is_senior'], '1'); ?>>Yes</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="is_disabled">Person With Disability (PWD)?</label>
                            <select id="is_disabled" name="is_disabled">
                                <option value="0" <?php echo isSelected($residentData['is_disabled'], '0'); ?>>No</option>
                                <option value="1" <?php echo isSelected($residentData['is_disabled'], '1'); ?>>Yes</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="is_pregnant">Pregnant?</label>
                            <select id="is_pregnant" name="is_pregnant">
                                <option value="0" <?php echo isSelected($residentData['is_pregnant'], '0'); ?>>No</option>
                                <option value="1" <?php echo isSelected($residentData['is_pregnant'], '1'); ?>>Yes</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="vaccination">Vaccinated?</label>
                            <select id="vaccination" name="vaccination">
                                <option value="0" <?php echo isSelected($residentData['vaccination'], '0'); ?>>No</option>
                                <option value="1" <?php echo isSelected($residentData['vaccination'], '1'); ?>>Yes</option>
                            </select>
                        </div>
                        
                        <div class="input-group form-group-full">
                            <label for="health_insurance">Health Insurance</label>
                            <input type="text" id="health_insurance" name="health_insurance" 
                                   value="<?php echo safeHtml($residentData['health_insurance']); ?>" 
                                   placeholder="e.g., PhilHealth, Private Insurance">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="residents.php" class="btn secondary-btn">Cancel</a>
                        <button type="submit" class="btn primary-btn">Save Changes</button>
                    </div>
                </form>

            </div>
        </main>
    </div>
</div>

<script>
function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    logoutBtn.addEventListener("click", () => {
        localStorage.removeItem("rms_user");
        window.location.href = "login.html";
    });
}

function showUser() {
    const user = JSON.parse(localStorage.getItem("rms_user"));
    const userNameSpan = document.getElementById("userName");
    if (user && user.name) {
        userNameSpan.textContent = `Welcome, ${user.name}`;
    } else {
        userNameSpan.textContent = `Welcome, Guest`;
    }
}

window.onload = function () {
    showUser();
    setupLogout();
};
</script>
</body>
</html>