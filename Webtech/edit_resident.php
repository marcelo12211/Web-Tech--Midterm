<?php
include 'db_connect.php'; 

// 1. Get the Resident ID from the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: residents.php');
    exit();
}

$residentId = $conn->real_escape_string($_GET['id']);

// 2. Fetch the resident's data from both tables
$sql = "
    SELECT 
        T1.*, 
        T2.IS_DISABLED, T2.IS_REGISTERED_SENIOR, T2.RESIDENT_TYPE, T2.PUROK
    FROM residents T1
    LEFT JOIN demographics T2 ON T1.ID = T2.MEMBER_ID
    WHERE T1.ID = '$residentId'
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p>Error: Resident not found.</p>";
    exit();
}

$residentData = $result->fetch_assoc();

/**
 * Helper function to check for selected option, safely handles NULL values.
 */
function isSelected($currentValue, $targetValue) {
    $safeCurrentValue = $currentValue ?? '';
    return ($safeCurrentValue == $targetValue) ? 'selected' : '';
}

/**
 * Helper function to safely output HTML data, treating NULL as an empty string.
 */
function safeHtml($value) {
    return htmlspecialchars($value ?? '');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Resident: <?php echo $residentId; ?></title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        /* Styles added for form layout */
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
        }
        .form-card {
            padding: 30px;
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
            <li><a href="residents.php">Residents</a></li>
            <li><a href="addnewresidents.php">Add Resident</a></li>
            <li><a href="deaths.php">Deaths</a></li>
            <li><a href="documents.php">Documents</a></li>
            <li class="nav-divider"></li>
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
                <h2>Edit Resident Information (ID: <?php echo $residentId; ?>)</h2>
                <p>Update the identification and demographic details below.</p>
                <hr>
                
                <form action="update_resident.php" method="POST">
                    <input type="hidden" name="resident_id" value="<?php echo $residentId; ?>">

                    <h3>Personal Identification</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="respondent_name">Respondent Name</label>
                            <input type="text" id="respondent_name" name="RESPONDENT_NAME" 
                                   value="<?php echo safeHtml($residentData['RESPONDENT_NAME']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="GENDER" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo isSelected($residentData['GENDER'], 'Male'); ?>>Male</option>
                                <option value="Female" <?php echo isSelected($residentData['GENDER'], 'Female'); ?>>Female</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="ADDRESS" 
                                   value="<?php echo safeHtml($residentData['ADDRESS']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="province">Province</label>
                            <input type="text" id="province" name="PROVINCE" 
                                   value="<?php echo safeHtml($residentData['PROVINCE']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="municipality">Municipality</label>
                            <input type="text" id="municipality" name="MUNICIPALITY" 
                                   value="<?php echo safeHtml($residentData['MUNICIPALITY']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="barangay">Barangay</label>
                            <input type="text" id="barangay" name="BARANGAY" 
                                   value="<?php echo safeHtml($residentData['BARANGAY']); ?>" required>
                        </div>
                        
                    </div>

                    <h3>Demographics & Status</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="purok">Purok</label>
                            <select id="purok" name="PUROK">
                                <option value="">Select Purok</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo isSelected($residentData['PUROK'], $i); ?>>Purok <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="resident_type">Resident Type (e.g., Solo Parent)</label>
                            <select id="resident_type" name="RESIDENT_TYPE" required>
                                <option value="Regular" <?php echo isSelected($residentData['RESIDENT_TYPE'], 'Regular'); ?>>Regular</option>
                                <option value="Solo Parent" <?php echo isSelected($residentData['RESIDENT_TYPE'], 'Solo Parent'); ?>>Solo Parent</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="senior_status">Registered Senior Citizen?</label>
                            <select id="senior_status" name="IS_REGISTERED_SENIOR">
                                <option value="0" <?php echo isSelected($residentData['IS_REGISTERED_SENIOR'], '0'); ?>>No</option>
                                <option value="1" <?php echo isSelected($residentData['IS_REGISTERED_SENIOR'], '1'); ?>>Yes</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="disabled_status">Person With Disability (PWD)?</label>
                            <select id="disabled_status" name="IS_DISABLED">
                                <option value="0" <?php echo isSelected($residentData['IS_DISABLED'], '0'); ?>>No</option>
                                <option value="1" <?php echo isSelected($residentData['IS_DISABLED'], '1'); ?>>Yes</option>
                            </select>
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