<?php
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

include __DIR__ . '/db_connect.php'; 

function isSelected($formValue, $targetValue) {
    return ($formValue == $targetValue) ? 'selected' : '';
}

function safeHtml($value) {
    return htmlspecialchars($value ?? '');
}

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $formData['housing_ownership'] = $_POST['housing_ownership'] ?? '';
    $formData['building_type'] = $_POST['building_type'] ?? '';
    $formData['water_source'] = $_POST['water_source'] ?? '';
    $formData['electricity_source'] = $_POST['electricity_source'] ?? '';
    $formData['toilet_facility'] = $_POST['toilet_facility'] ?? '';
    $formData['waste_disposal'] = $_POST['waste_disposal'] ?? '';
    
    if (empty($formData['housing_ownership'])) {
        $errors[] = "Housing Ownership is required.";
    }
    
    if (empty($formData['building_type'])) {
        $errors[] = "Building Type is required.";
    }
    
    if (empty($formData['water_source'])) {
        $errors[] = "Water Source is required.";
    }
    
    if (empty($formData['electricity_source'])) {
        $errors[] = "Electricity Source is required.";
    }
    
    if (empty($formData['toilet_facility'])) {
        $errors[] = "Toilet Facility is required.";
    }
    
    if (empty($formData['waste_disposal'])) {
        $errors[] = "Waste Disposal is required.";
    }
    
    if (empty($errors)) {
        $insertSql = "INSERT INTO household (housing_ownership, water_source, toilet_facility, electricity_source, waste_disposal, building_type) 
                      VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ssssss", 
            $formData['housing_ownership'],
            $formData['water_source'],
            $formData['toilet_facility'],
            $formData['electricity_source'],
            $formData['waste_disposal'],
            $formData['building_type']
        );
        
        if ($stmt->execute()) {
            $new_household_id = $conn->insert_id;
            $_SESSION['add_success'] = true;
            $_SESSION['new_household_id'] = $new_household_id;
            
            header("Location: household.php");
            exit();
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New Household</title>
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
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 500;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .form-card {
                padding: 20px;
            }
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
                <h2>Create New Household</h2>
                
                <div class="info-box">
                    <p><strong>Important:</strong> Create household first, then add residents to it.</p>
                    <p>After creating this household, go to "Add Resident" to add residents to it.</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert error">
                        <strong>Please fix the following errors:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo safeHtml($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <form action="add_household.php" method="POST">
                    <h3 class="section-title">Property Details</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="housing_ownership">Housing Ownership *</label>
                            <select id="housing_ownership" name="housing_ownership" required>
                                <option value="">Select Ownership</option>
                                <option value="Owned" <?php echo isSelected($formData['housing_ownership'] ?? '', 'Owned'); ?>>Owned</option>
                                <option value="Rented" <?php echo isSelected($formData['housing_ownership'] ?? '', 'Rented'); ?>>Rented</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="building_type">Building Type *</label>
                            <select id="building_type" name="building_type" required>
                                <option value="">Select Building Type</option>
                                <option value="Concrete" <?php echo isSelected($formData['building_type'] ?? '', 'Concrete'); ?>>Concrete</option>
                                <option value="Semi-Concrete" <?php echo isSelected($formData['building_type'] ?? '', 'Semi-Concrete'); ?>>Semi-Concrete</option>
                                <option value="Wood" <?php echo isSelected($formData['building_type'] ?? '', 'Wood'); ?>>Wood</option>
                                <option value="Light Materials" <?php echo isSelected($formData['building_type'] ?? '', 'Light Materials'); ?>>Light Materials</option>
                                <option value="Mixed" <?php echo isSelected($formData['building_type'] ?? '', 'Mixed'); ?>>Mixed</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="section-title">Utilities & Facilities</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="water_source">Water Source *</label>
                            <select id="water_source" name="water_source" required>
                                <option value="">Select Water Source</option>
                                <option value="BAWADI" <?php echo isSelected($formData['water_source'] ?? '', 'BAWADI'); ?>>BAWADI</option>
                                <option value="Deep Well" <?php echo isSelected($formData['water_source'] ?? '', 'Deep Well'); ?>>Deep Well</option>
                                <option value="Spring" <?php echo isSelected($formData['water_source'] ?? '', 'Spring'); ?>>Spring</option>
                                <option value="River" <?php echo isSelected($formData['water_source'] ?? '', 'River'); ?>>River</option>
                                <option value="Well" <?php echo isSelected($formData['water_source'] ?? '', 'Well'); ?>>Well</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="electricity_source">Electricity Source *</label>
                            <select id="electricity_source" name="electricity_source" required>
                                <option value="">Select Electricity Source</option>
                                <option value="BENECO" <?php echo isSelected($formData['electricity_source'] ?? '', 'BENECO'); ?>>BENECO</option>
                                <option value="Solar" <?php echo isSelected($formData['electricity_source'] ?? '', 'Solar'); ?>>Solar</option>
                                <option value="Generator" <?php echo isSelected($formData['electricity_source'] ?? '', 'Generator'); ?>>Generator</option>
                                <option value="Electricity" <?php echo isSelected($formData['electricity_source'] ?? '', 'Electricity'); ?>>Electricity</option>
                                <option value="None" <?php echo isSelected($formData['electricity_source'] ?? '', 'None'); ?>>None</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="toilet_facility">Toilet Facility *</label>
                            <select id="toilet_facility" name="toilet_facility" required>
                                <option value="">Select Toilet Facility</option>
                                <option value="Water Sealed" <?php echo isSelected($formData['toilet_facility'] ?? '', 'Water Sealed'); ?>>Water Sealed</option>
                                <option value="Pit Latrine" <?php echo isSelected($formData['toilet_facility'] ?? '', 'Pit Latrine'); ?>>Pit Latrine</option>
                                <option value="Communal" <?php echo isSelected($formData['toilet_facility'] ?? '', 'Communal'); ?>>Communal</option>
                                <option value="Flush Toilet" <?php echo isSelected($formData['toilet_facility'] ?? '', 'Flush Toilet'); ?>>Flush Toilet</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="waste_disposal">Waste Disposal *</label>
                            <select id="waste_disposal" name="waste_disposal" required>
                                <option value="">Select Waste Disposal</option>
                                <option value="Collected by Garbage Truck" <?php echo isSelected($formData['waste_disposal'] ?? '', 'Collected by Garbage Truck'); ?>>Collected by Garbage Truck</option>
                                <option value="Composting" <?php echo isSelected($formData['waste_disposal'] ?? '', 'Composting'); ?>>Composting</option>
                                <option value="Burning" <?php echo isSelected($formData['waste_disposal'] ?? '', 'Burning'); ?>>Burning</option>
                                <option value="Recycling" <?php echo isSelected($formData['waste_disposal'] ?? '', 'Recycling'); ?>>Recycling</option>
                                <option value="Regular Collection" <?php echo isSelected($formData['waste_disposal'] ?? '', 'Regular Collection'); ?>>Regular Collection</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; color: #666; font-size: 0.9rem;">
                        <small>* Required fields</small>
                    </div>
                    
                    <div class="form-actions">
                        <a href="household.php" class="btn secondary-btn">Cancel</a>
                        <button type="submit" class="btn primary-btn">Create Household</button>
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