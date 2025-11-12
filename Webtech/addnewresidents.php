<?php
include 'db_connect.php'; // makes $conn available

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign POST data
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $middleName = $conn->real_escape_string($_POST['middleName'] ?? '');
    $suffix = $conn->real_escape_string($_POST['suffix'] ?? '');
    $birthDate = $_POST['birthDate'];
    $gender = $_POST['gender'];
    $civilStatus = $_POST['civilStatus'];
    $citizenship = $conn->real_escape_string($_POST['citizenship']);
    $contactNumber = $_POST['contactNumber'] ?? '';
    $email = $_POST['email'] ?? '';

    $houseNumber = $conn->real_escape_string($_POST['houseNumber'] ?? '');
    $street = $conn->real_escape_string($_POST['street']);
    $purok = $_POST['purok'];
    $barangay = $conn->real_escape_string($_POST['barangay']);
    $city = $conn->real_escape_string($_POST['city']);
    $province = $conn->real_escape_string($_POST['province']);

    $category = $_POST['category'];
    $idNumber = $_POST['idNumber'] ?? '';
    $occupation = $conn->real_escape_string($_POST['occupation'] ?? '');

    // Insert into residents table
    $sql_residents = "INSERT INTO residents (FIRST_NAME, LAST_NAME, MIDDLE_NAME, SUFFIX, BIRTH_DATE, GENDER, CIVIL_STATUS, CITIZENSHIP, CONTACT_NUMBER, EMAIL)
                      VALUES ('$firstName', '$lastName', '$middleName', '$suffix', '$birthDate', '$gender', '$civilStatus', '$citizenship', '$contactNumber', '$email')";
    if ($conn->query($sql_residents) === TRUE) {
        $memberId = $conn->insert_id; // get last inserted ID

        // Determine flags for demographics
        $isSenior = ($category === 'senior') ? 1 : 0;
        $isPWD = ($category === 'pwd') ? 1 : 0;
        $residentType = ($category === 'solo_parent') ? 'Solo Parent' : (($category !== 'senior' && $category !== 'pwd') ? 'Regular' : '');

        // Insert into demographics
        $sql_demo = "INSERT INTO demographics (MEMBER_ID, IS_REGISTERED_SENIOR, IS_DISABLED, RESIDENT_TYPE, HEALTH_INSURANCE, PUROK)
    VALUES ($memberId, $isSenior, $isPWD, '$residentType', '$idNumber', '$purok')";
        if ($conn->query($sql_demo) === TRUE) {
            $success = "Resident added successfully!";
        } else {
            $error = "Error adding to demographics: " . $conn->error;
        }
    } else {
        $error = "Error adding resident: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Add New Resident</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-container">
  <aside class="sidebar">
    <div class="logo">Happy Hallow Barangay System</div>
    <nav class="main-nav">
      <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="residents.php">Residents</a></li>
        <li><a href="addnewresidents.php" class="active">Add Resident</a></li>
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
      <div class="add-resident-form-container">
        <div class="form-modal-card">
          <a href="residents.php" class="close-btn">&times;</a>

          <?php
          if(isset($success)) echo "<p style='color:green;'>$success</p>";
          if(isset($error)) echo "<p style='color:red;'>$error</p>";
          ?>

          <form id="addResidentForm" method="POST" novalidate>
            <h2 class="form-title">Add New Resident</h2>

            <!-- Form sections (same as before, just keep all inputs) -->
            <!-- Personal Info -->
            <!-- Address Info -->
            <!-- Other Info -->
            <!-- Copy your HTML inputs here as-is from your previous HTML -->

            <!-- Example for First Name -->
            <div class="input-group">
              <label for="firstName">First Name</label>
              <input type="text" id="firstName" name="firstName" placeholder="First Name" required />
            </div>
            <!-- Repeat other fields... -->

            <div class="form-actions">
              <button type="submit" class="btn primary-btn create">Save Resident</button>
              <button type="button" class="btn secondary" onclick="window.location.href='residents.php'">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
function showUser() {
  const user = JSON.parse(localStorage.getItem("rms_user"));
  const userNameSpan = document.getElementById("userName");
  userNameSpan.textContent = user && user.name ? `Welcome, ${user.name}` : "Welcome, Guest";
}

function setupLogout() {
  const logoutBtn = document.getElementById("logoutBtn");
  logoutBtn.addEventListener("click", () => {
    localStorage.removeItem("rms_user");
    window.location.href = "login.html";
  });
}

window.onload = function () {
  showUser();
  setupLogout();

  const categorySelect = document.getElementById('category');
  const idGroup = document.getElementById('idNumberGroup');
  const idInput = document.getElementById('idNumber');
  function updateIdField() {
    const value = categorySelect.value;
    if (value === 'senior' || value === 'pwd') {
      idGroup.style.display = 'block';
      idInput.required = true;
    } else {
      idGroup.style.display = 'none';
      idInput.required = false;
      idInput.value = '';
    }
  }
  updateIdField();
  categorySelect.addEventListener('change', updateIdField);
};
</script>
</body>
</html>
