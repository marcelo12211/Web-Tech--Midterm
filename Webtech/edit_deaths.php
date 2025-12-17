<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

include __DIR__ . '/db_connect.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: deaths.php');
    exit();
}

$deathId = $conn->real_escape_string($_GET['id']);

$sql = "
    SELECT 
        id,
        record_number,
        name,
        age,
        date_of_death,
        cause_of_death,
        is_pwd,
        is_senior,
        pwd_id,
        ncsc_rrn,
        osca_id
    FROM deaths
    WHERE id = '$deathId'
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p>Error: Death record not found.</p>";
    exit();
}

$deathData = $result->fetch_assoc();

function isSelected($currentValue, $targetValue) {
    $safeCurrentValue = $currentValue ?? '';
    return ($safeCurrentValue == $targetValue) ? 'selected' : '';
}

function safeHtml($value) {
    return htmlspecialchars($value ?? '');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Death Record: <?php echo safeHtml($deathData['record_number']); ?></title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/residents-details.css" />
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
        .info-box {
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            background-color: #fff5f5;
        }
        .info-box p {
            margin: 5px 0;
            color: #991b1b;
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
            <li><a href="addnewresidents.php">Add Resident</a></li>
            <li><a href="deaths.php" class="active">Deaths</a></li>
            <li><a href="documents.php">Documents</a></li>
            <li><a href="staff_documents.php">Generate Certificates</a></li>
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
            <div class="card form-card">
                <h2>Edit Death Record</h2>
                                
                <form action="update_deaths.php" method="POST">
                    <input type="hidden" name="death_id" value="<?php echo safeHtml($deathData['id']); ?>">

                    <h3 class="section-title">Basic Information</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="record_number">Record Number</label>
                            <input type="text" id="record_number" name="record_number" 
                                   value="<?php echo safeHtml($deathData['record_number']); ?>" disabled>
                        </div>
                        
                        <div class="input-group">
                            <label for="resident_name">Full Name</label>
                            <input type="text" id="resident_name" name="resident_name" 
                                   value="<?php echo safeHtml($deathData['name']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="resident_age">Age</label>
                            <input type="number" id="resident_age" name="resident_age" min="0"
                                   value="<?php echo safeHtml($deathData['age']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="date_of_death">Date of Death</label>
                            <input type="date" id="date_of_death" name="date_of_death" 
                                   value="<?php echo safeHtml($deathData['date_of_death']); ?>" required>
                        </div>
                        
                        <div class="input-group form-group-full">
                            <label for="cause_of_death">Cause of Death</label>
                            <select id="cause_of_death" name="cause_of_death" required>
                                <option value="">Select Cause</option>
                                <option value="Kidney Failure" <?php echo isSelected($deathData['cause_of_death'], 'Kidney Failure'); ?>>Kidney Failure</option>
                                <option value="Old Age (Natural)" <?php echo isSelected($deathData['cause_of_death'], 'Old Age (Natural)'); ?>>Old Age (Natural)</option>
                                <option value="Accident" <?php echo isSelected($deathData['cause_of_death'], 'Accident'); ?>>Accident</option>
                                <option value="Other" <?php echo isSelected($deathData['cause_of_death'], 'Other'); ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="section-title">Special Status</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="is_pwd">Person with Disability</label>
                            <select id="is_pwd" name="is_pwd" onchange="toggleIdFields()">
                                <option value="no" <?php echo isSelected($deathData['is_pwd'], 'no'); ?>>No</option>
                                <option value="yes" <?php echo isSelected($deathData['is_pwd'], 'yes'); ?>>Yes</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="is_senior">Senior Citizen</label>
                            <select id="is_senior" name="is_senior" onchange="toggleIdFields()">
                                <option value="no" <?php echo isSelected($deathData['is_senior'], 'no'); ?>>No</option>
                                <option value="yes" <?php echo isSelected($deathData['is_senior'], 'yes'); ?>>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div id="pwdIdField" class="input-group" style="display: none; margin-top: 20px">
                        <label for="pwd_id">PWD ID Number</label>
                        <input type="text" id="pwd_id" name="pwd_id" 
                               value="<?php echo safeHtml($deathData['pwd_id']); ?>">
                    </div>
                    
                    <div id="seniorCitizenIdFields" class="form-grid" style="display: none; margin-top: 20px">
                        <div class="input-group">
                            <label for="ncsc_rrn">NCSC-RRN Number</label>
                            <input type="text" id="ncsc_rrn" name="ncsc_rrn" 
                                   value="<?php echo safeHtml($deathData['ncsc_rrn']); ?>">
                        </div>
                        <div class="input-group">
                            <label for="osca_id">OSCA ID Number</label>
                            <input type="text" id="osca_id" name="osca_id" 
                                   value="<?php echo safeHtml($deathData['osca_id']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="deaths.php" class="btn secondary">Cancel</a>
                        <button type="submit" class="btn primary-btn">Save Changes</button>
                    </div>
                </form>

            </div>
        </main>
    </div>
</div>

<script>
function toggleIdFields() {
    const isPwd = document.getElementById("is_pwd").value === "yes";
    const isSeniorCitizen = document.getElementById("is_senior").value === "yes";
    document.getElementById("pwdIdField").style.display = isPwd ? "flex" : "none";
    document.getElementById("seniorCitizenIdFields").style.display = isSeniorCitizen ? "grid" : "none";
}

function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    logoutBtn.addEventListener("click", () => {
        window.location.href = "logout.php";
    });
}

window.onload = function () {
    setupLogout();
    toggleIdFields();
};
</script>
</body>
</html>