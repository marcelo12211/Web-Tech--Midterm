<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

include __DIR__ . '/db_connect.php'; 

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: household.php');
    exit();
}

$householdId = $conn->real_escape_string($_GET['id']);

$sql = "
    SELECT 
        household_id,
        household_head,
        housing_ownership,
        water_source,
        toilet_facility,
        electricity_source,
        waste_disposal,
        building_type
    FROM household
    WHERE household_id = '$householdId'
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p>Error: Household not found.</p>";
    exit();
}

$householdData = $result->fetch_assoc();

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
    <title>Edit Household: <?php echo safeHtml($householdData['household_id']); ?></title>
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
        .info-box {
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }
        .info-box p {
            margin: 5px 0;
            color: #1e40af;
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
            <li><a href="household.php" class="active">Households</a></li>
            <li><a href="residents.php">Residents</a></li>
            <li><a href="addnewresidents.php">Add Resident</a></li>
            <li><a href="deaths.php">Deaths</a></li>
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
                <h2>Edit Household Information</h2>
                
                <div class="info-box">
                    <p><strong>Household ID:</strong> <?php echo safeHtml($householdData['household_id']); ?></p>
                    <p><strong>Current Household Head:</strong> <?php echo safeHtml($householdData['household_head']); ?></p>
                </div>
                
                <hr>
                
                <form action="update_household.php" method="POST">
                    <input type="hidden" name="household_id" value="<?php echo safeHtml($householdData['household_id']); ?>">

                    <h3 class="section-title">Basic Information</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="household_head">Household Head</label>
                            <input type="text" id="household_head" name="household_head" 
                                   value="<?php echo safeHtml($householdData['household_head']); ?>" required>
                        </div>
                        
                        <div class="input-group">
                            <label for="housing_ownership">Housing Ownership</label>
                            <select id="housing_ownership" name="housing_ownership" required>
                                <option value="">Select Ownership</option>
                                <option value="Owned" <?php echo isSelected($householdData['housing_ownership'], 'Owned'); ?>>Owned</option>
                                <option value="Rented" <?php echo isSelected($householdData['housing_ownership'], 'Rented'); ?>>Rented</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="building_type">Building Type</label>
                            <select id="building_type" name="building_type" required>
                                <option value="">Select Building Type</option>
                                <option value="Concrete" <?php echo isSelected($householdData['building_type'], 'Concrete'); ?>>Concrete</option>
                                <option value="Semi-Concrete" <?php echo isSelected($householdData['building_type'], 'Semi-Concrete'); ?>>Semi-Concrete</option>
                                <option value="Wood" <?php echo isSelected($householdData['building_type'], 'Wood'); ?>>Wood</option>
                                <option value="Light Materials" <?php echo isSelected($householdData['building_type'], 'Light Materials'); ?>>Light Materials</option>
                                <option value="Mixed" <?php echo isSelected($householdData['building_type'], 'Mixed'); ?>>Mixed</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="section-title">Utilities & Facilities</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="water_source">Water Source</label>
                            <select id="water_source" name="water_source" required>
                                <option value="">Select Water Source</option>
                                <option value="BAWADI" <?php echo isSelected($householdData['water_source'], 'BAWADI'); ?>>BAWADI</option>
                                <option value="Deep Well" <?php echo isSelected($householdData['water_source'], 'Deep Well'); ?>>Deep Well</option>
                                <option value="Spring" <?php echo isSelected($householdData['water_source'], 'Spring'); ?>>Spring</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="electricity_source">Electricity Source</label>
                            <select id="electricity_source" name="electricity_source" required>
                                <option value="">Select Electricity Source</option>
                                <option value="BENECO" <?php echo isSelected($householdData['electricity_source'], 'BENECO'); ?>>BENECO</option>
                                <option value="Solar" <?php echo isSelected($householdData['electricity_source'], 'Solar'); ?>>Solar</option>
                                <option value="Generator" <?php echo isSelected($householdData['electricity_source'], 'Generator'); ?>>Generator</option>
                                <option value="None" <?php echo isSelected($householdData['electricity_source'], 'None'); ?>>None</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="toilet_facility">Toilet Facility</label>
                            <select id="toilet_facility" name="toilet_facility" required>
                                <option value="">Select Toilet Facility</option>
                                <option value="Water Sealed" <?php echo isSelected($householdData['toilet_facility'], 'Water Sealed'); ?>>Water Sealed</option>
                                <option value="Pit Latrine" <?php echo isSelected($householdData['toilet_facility'], 'Pit Latrine'); ?>>Pit Latrine</option>
                                <option value="Communal" <?php echo isSelected($householdData['toilet_facility'], 'Communal'); ?>>Communal</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="waste_disposal">Waste Disposal</label>
                            <select id="waste_disposal" name="waste_disposal" required>
                                <option value="">Select Waste Disposal</option>
                                <option value="Collected by Garbage Truck" <?php echo isSelected($householdData['waste_disposal'], 'Collected by Garbage Truck'); ?>>Collected by Garbage Truck</option>
                                <option value="Composting" <?php echo isSelected($householdData['waste_disposal'], 'Composting'); ?>>Composting</option>
                                <option value="Burning" <?php echo isSelected($householdData['waste_disposal'], 'Burning'); ?>>Burning</option>
                                <option value="Recycling" <?php echo isSelected($householdData['waste_disposal'], 'Recycling'); ?>>Recycling</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="household.php" class="btn secondary-btn">Cancel</a>
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
        window.location.href = "logout.php";
    });
}

window.onload = function () {
    setupLogout();
};
</script>
</body>
</html>