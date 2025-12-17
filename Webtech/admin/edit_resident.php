<?php
include __DIR__ . '/db_connect.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_username = $_SESSION['user_name'] ?? 'User';

/* ===============================
   VALIDATE ID
================================ */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: residents.php");
    exit();
}

$residentId = intval($_GET['id']);

/* ===============================
   FETCH RESIDENT DATA (READ ONLY)
================================ */
$stmt = $conn->prepare("
    SELECT 
        person_id, household_id, first_name, middle_name, surname, suffix,
        sex, birthdate, civil_status, nationality, religion, purok, address,
        residency_start_date, education_level, occupation,
        is_senior, is_disabled, is_pregnant,
        health_insurance, vaccination, children_count
    FROM residents
    WHERE person_id = ?
");
$stmt->bind_param("i", $residentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = 'Resident not found.';
    header("Location: residents.php");
    exit();
}

$residentData = $result->fetch_assoc();
$stmt->close();

/* ===============================
   FETCH HOUSEHOLDS
================================ */
$householdResult = $conn->query("
    SELECT household_id, household_head
    FROM household
    ORDER BY household_id ASC
");

/* ===============================
   HELPERS
================================ */
function safeHtml($val) {
    return htmlspecialchars($val ?? '');
}

function isSelected($current, $target) {
    return ($current == $target) ? 'selected' : '';
}

function getSelectedStatus($res) {
    if (!empty($res['is_senior'])) return 'Senior Citizen';
    if (!empty($res['is_disabled'])) return 'PWD';
    if (!empty($res['is_pregnant'])) return 'Pregnant';
    if (!empty($res['health_insurance'])) return 'Others';
    return 'None';
}

$current_status = getSelectedStatus($residentData);

/* ===============================
   FLASH MESSAGES
================================ */
$status_success = $_SESSION['status_success'] ?? null;
$error_message  = $_SESSION['error_message'] ?? null;
unset($_SESSION['status_success'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Resident: <?php echo safeHtml($residentData['first_name'] . ' ' . $residentData['surname']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
:root {
    --primary-color: #226b8dff;
    --primary-dark: #1b546b;
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
.form-title {
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 10px;
    margin-bottom: 20px;
    font-size: 1.5rem;
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
.input-group input,
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

.section-title {
    grid-column: 1 / -1;
    margin-top: 25px;
    margin-bottom: 15px;
    color: var(--primary-dark);
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 5px;
    font-size: 1.2rem;
    font-weight: 700;
}

.form-group-full {
    grid-column: 1 / -1;
}

.form-actions {
    grid-column: 1 / -1;
    margin-top: 30px;
    text-align: right;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
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
.secondary-btn {
    background: white;
    color: var(--text-color);
    border-color: var(--border-color);
}
.secondary-btn:hover {
    background: var(--background-color);
}

.alert-success {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
    background-color: #d4edda;
    color: var(--success-color);
    border: 1px solid var(--success-color);
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
.alert-info {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
    background-color: #cce5ff;
    color: #004085;
    border: 1px solid #b8daff;
}

.special-status-display {
    padding: 10px;
    border-radius: 6px;
    margin-top: 5px;
    font-weight: 700;
    text-align: center;
}
.status-senior { background-color: #d4edda; color: var(--success-color); border: 1px solid var(--success-color); }
.status-pwd { background-color: #fff3cd; color: var(--warning-color); border: 1px solid var(--warning-color); }
.status-pregnant { background-color: #f8d7da; color: var(--danger-color); border: 1px solid var(--danger-color); }
.status-others { background-color: #cce5ff; color: var(--primary-color); border: 1px solid var(--primary-color); }
.status-none { background-color: #f0f0f0; color: var(--text-light); border: 1px solid var(--border-color); }

@media (max-width: 900px) {
    .form-grid {
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
                     Edit Resident: <?php echo safeHtml($residentData['first_name'] . ' ' . $residentData['surname']); ?> 
                </h2>
                
                <?php if ($status_success): ?>
                    <div class="alert-success"><i class="fas fa-check-circle"></i> <?php echo $status_success; ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert-error"><i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?></div>
                <?php endif; ?>

                
<form id="editResidentForm">

  <input type="hidden" name="person_id" value="<?php echo $residentId; ?>">

                    <div class="form-grid">
                        
                        <div class="section-title">Personal Information</div>

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
                                <option value="Male" <?php echo isSelected($residentData['sex'], 'Male'); ?>>Male</option>
                                <option value="Female" <?php echo isSelected($residentData['sex'], 'Female'); ?>>Female</option>
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
                        
                        <div class="section-title">Address & Affiliation</div>
                        
                        <div class="input-group">
                            <label>Household *</label>
                            <select name="household_id" required>
                                <option value="">Select Household</option>
                                <?php 
                                while($row = $householdResult->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($row['household_id']) ?>" 
                                    <?php echo isSelected($residentData['household_id'], $row['household_id']); ?>>
                                    <?= htmlspecialchars($row['household_id']) ?> - <?= htmlspecialchars($row['household_head'] ?? 'No Head') ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="purok">Purok *</label>
                            <select id="purok" name="purok" required>
                                <option value="">Select Purok</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo isSelected($residentData['purok'], $i); ?>>
                                        Purok <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="input-group form-group-full">
                            <label for="address">Address *</label>
                            <input type="text" id="address" name="address" 
                                        value="<?php echo safeHtml($residentData['address']); ?>" required>
                            <small class="text-light">Current barangay address details.</small>
                        </div>

                        <div class="section-title">Education & Employment</div>
                        
                        <div class="input-group">
                            <label for="education_level">Education Level</label>
                            <select id="education_level" name="education_level">
                                <option value="">Select Education Level</option>
                                <option value="Elementary" <?php echo isSelected($residentData['education_level'], 'Elementary'); ?>>Elementary</option>
                                <option value="High School" <?php echo isSelected($residentData['education_level'], 'High School'); ?>>High School</option>
                                <option value="Vocational" <?php echo isSelected($residentData['education_level'], 'Vocational'); ?>>Vocational</option>
                                <option value="College" <?php echo isSelected($residentData['education_level'], 'College'); ?>>College</option>
                                <option value="Graduate Studies" <?php echo isSelected($residentData['education_level'], 'Graduate Studies'); ?>>Graduate Studies</option>
                            </select>
                        </div>
                        
                        <div class="input-group">
                            <label for="occupation">Occupation</label>
                            <input type="text" id="occupation" name="occupation" 
                                        value="<?php echo safeHtml($residentData['occupation']); ?>">
                        </div>
                        

                        <div class="section-title">Health & Special Status</div>
                        
                        <div class="input-group form-group-full">
                            <label>Special Status / Health Insurance</label>
                            <select name="special_status" id="specialStatus">
                                <option value="None" <?php echo isSelected($current_status, 'None'); ?>>None</option>
                                <option value="Senior Citizen" <?php echo isSelected($current_status, 'Senior Citizen'); ?>>Senior Citizen</option>
                                <option value="PWD" <?php echo isSelected($current_status, 'PWD'); ?>>Person with Disability (PWD)</option>
                                <option value="Pregnant" <?php echo isSelected($current_status, 'Pregnant'); ?>>Pregnant</option>
                                <option value="Others" <?php echo isSelected($current_status, 'Others'); ?>>Others (Health Insurance)</option>
                            </select>
                            <small class="text-light">Automatically updates is_senior/is_disabled/is_pregnant/health_insurance fields.</small>
                        </div>
                        
                        <div class="input-group">
                            <label for="vaccination">Vaccination Status</label>
                            <input type="text" id="vaccination" name="vaccination" 
                                        value="<?php echo safeHtml($residentData['vaccination']); ?>" placeholder="e.g., Fully Vaccinated (3 doses)">
                        </div>

                        <div class="input-group">
                            <label for="health_insurance">Health Insurance Type/Notes</label>
                            <input type="text" id="health_insurance" name="health_insurance" 
                                        value="<?php echo safeHtml($residentData['health_insurance']); ?>" 
                                        placeholder="e.g., PhilHealth, Private, N/A">
                            <small class="text-light">This is manually updateable.</small>
                        </div>
                        
                        <div class="form-actions">
                            <a href="residents.php" class="btn secondary-btn"><i class="fas fa-arrow-alt-circle-left"></i> Back to List</a>
                            <button type="submit" class="btn primary-btn"><i class="fas fa-save"></i> Save Changes</button>
                        </div>
                    </div>
                </form>

            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("editResidentForm");
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const residentId = data.person_id;

    try {
      const res = await fetch(
        `http://127.0.0.1:5000/admin/residents/${residentId}`,
        {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
        }
      );

      const result = await res.json();

      if (result.success) {
        window.location.href = "residents.php";
      } else {
        alert(result.error || "Failed to update resident");
      }

    } catch (err) {
      console.error(err);
      alert("Cannot connect to Node server");
    }
  });
});
</script>
</body>
</html>