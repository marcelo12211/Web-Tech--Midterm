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

function safeHtml($value) {
    return htmlspecialchars($value ?? '');
}

function isSelected($current, $target) {
    return ((string)$current === (string)$target) ? 'selected' : '';
}

$sexValue = strtoupper(substr($residentData['sex'] ?? '', 0, 1));

$householdsSql = "
    SELECT household_id, household_head
    FROM household
    WHERE household_head IS NOT NULL
    ORDER BY household_head ASC
";
$householdsResult = $conn->query($householdsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Resident</title>
    <link rel="stylesheet" href="css/style.css" />
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
                <p>
                    ID: <?= safeHtml($residentData['person_id']); ?> -
                    <?= safeHtml($residentData['first_name'] . ' ' . $residentData['surname']); ?>
                </p>
                <hr>

                <form action="update_resident.php" method="POST">
                    <input type="hidden" name="person_id" value="<?= safeHtml($residentData['person_id']); ?>">

                    <h3 class="section-title">Personal Information</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>First Name *</label>
                            <input type="text" name="first_name" value="<?= safeHtml($residentData['first_name']); ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Middle Name</label>
                            <input type="text" name="middle_name" value="<?= safeHtml($residentData['middle_name']); ?>">
                        </div>

                        <div class="input-group">
                            <label>Surname *</label>
                            <input type="text" name="surname" value="<?= safeHtml($residentData['surname']); ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Suffix</label>
                            <input type="text" name="suffix" value="<?= safeHtml($residentData['suffix']); ?>">
                        </div>

                        <div class="input-group">
                            <label>Sex *</label>
                            <select name="sex" required>
                                <option value="">Select Sex</option>
                                <option value="M" <?= isSelected($sexValue, 'M'); ?>>Male</option>
                                <option value="F" <?= isSelected($sexValue, 'F'); ?>>Female</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Birthdate *</label>
                            <input type="date" name="birthdate" value="<?= safeHtml($residentData['birthdate']); ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Civil Status *</label>
                            <select name="civil_status" required>
                                <option value="">Select</option>
                                <option value="Single" <?= isSelected($residentData['civil_status'], 'Single'); ?>>Single</option>
                                <option value="Married" <?= isSelected($residentData['civil_status'], 'Married'); ?>>Married</option>
                                <option value="Widowed" <?= isSelected($residentData['civil_status'], 'Widowed'); ?>>Widowed</option>
                                <option value="Separated" <?= isSelected($residentData['civil_status'], 'Separated'); ?>>Separated</option>
                                <option value="Divorced" <?= isSelected($residentData['civil_status'], 'Divorced'); ?>>Divorced</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Nationality</label>
                            <input type="text" name="nationality" value="<?= safeHtml($residentData['nationality']); ?>">
                        </div>

                        <div class="input-group">
                            <label>Religion</label>
                            <input type="text" name="religion" value="<?= safeHtml($residentData['religion']); ?>">
                        </div>

                        <div class="input-group">
                            <label>Number of Children</label>
                            <input type="number" name="children_count" min="0" value="<?= safeHtml($residentData['children_count']); ?>">
                        </div>
                    </div>

                    <h3 class="section-title">Address & Location</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Household Head</label>
                            <select name="household_id">
                                <option value="">No household head</option>
                                <?php while ($row = $householdsResult->fetch_assoc()): ?>
                                    <option value="<?= safeHtml($row['household_id']); ?>"
                                        <?= isSelected($row['household_id'], $residentData['household_id']); ?>>
                                        <?= safeHtml($row['household_head']); ?> (ID <?= safeHtml($row['household_id']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Purok *</label>
                            <select name="purok" required>
                                <option value="">Select Purok</option>
                                <?php for ($i = 0; $i <= 5; $i++): ?>
                                    <option value="<?= $i; ?>" <?= isSelected($residentData['purok'], $i); ?>>
                                        <?= $i == 0 ? 'None' : "Purok $i"; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="input-group form-group-full">
                            <label>Address *</label>
                            <input type="text" name="address" value="<?= safeHtml($residentData['address']); ?>" required>
                        </div>
                    </div>

                    <h3 class="section-title">Education & Employment</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Education Level</label>
                            <select name="education_level">
                                <option value="">Select</option>
                                <option value="Elementary" <?= isSelected($residentData['education_level'], 'Elementary'); ?>>Elementary</option>
                                <option value="High School" <?= isSelected($residentData['education_level'], 'High School'); ?>>High School</option>
                                <option value="College" <?= isSelected($residentData['education_level'], 'College'); ?>>College</option>
                                <option value="Vocational" <?= isSelected($residentData['education_level'], 'Vocational'); ?>>Vocational</option>
                                <option value="Graduate" <?= isSelected($residentData['education_level'], 'Graduate'); ?>>Graduate</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Occupation</label>
                            <input type="text" name="occupation" value="<?= safeHtml($residentData['occupation']); ?>">
                        </div>
                    </div>

                    <h3 class="section-title">Health & Status</h3>
                    <div class="form-grid">
                        <div class="input-group">
                            <label>Senior Citizen</label>
                            <select name="is_senior">
                                <option value="0" <?= isSelected($residentData['is_senior'], 0); ?>>No</option>
                                <option value="1" <?= isSelected($residentData['is_senior'], 1); ?>>Yes</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>PWD</label>
                            <select name="is_disabled">
                                <option value="0" <?= isSelected($residentData['is_disabled'], 0); ?>>No</option>
                                <option value="1" <?= isSelected($residentData['is_disabled'], 1); ?>>Yes</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Pregnant</label>
                            <select name="is_pregnant">
                                <option value="0" <?= isSelected($residentData['is_pregnant'], 0); ?>>No</option>
                                <option value="1" <?= isSelected($residentData['is_pregnant'], 1); ?>>Yes</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Vaccinated</label>
                            <select name="vaccination">
                                <option value="0" <?= isSelected($residentData['vaccination'], 0); ?>>No</option>
                                <option value="1" <?= isSelected($residentData['vaccination'], 1); ?>>Yes</option>
                            </select>
                        </div>

                        <div class="input-group form-group-full">
                            <label>Health Insurance</label>
                            <input type="text" name="health_insurance" value="<?= safeHtml($residentData['health_insurance']); ?>">
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
    document.getElementById("logoutBtn").onclick = () => {
        localStorage.removeItem("rms_user");
        window.location.href = "login.php";
    };
}
function showUser() {
    const user = JSON.parse(localStorage.getItem("rms_user"));
    document.getElementById("userName").textContent =
        user && user.name ? `Welcome, ${user.name}` : "Welcome, Guest";
}
window.onload = function () {
    showUser();
    setupLogout();
};
</script>
</body>
</html>
