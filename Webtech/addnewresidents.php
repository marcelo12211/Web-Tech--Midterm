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

<div id="page1">
  <h2>A. Identification</h2>

  <div class="form-grid">

    <div class="input-group">
      <label for="firstName">First Name</label>
      <input type="text" id="newFirst" name="firstName" required>
    </div>

    <div class="input-group">
      <label for="middleName">Middle Name</label>
      <input type="text" id="newMiddle" name="middleName">
    </div>

    <div class="input-group">
      <label for="lastName">Surname</label>
      <input type="text" id="newLast" name="lastName" required>
    </div>

    <div class="input-group">
      <label for="suffix">Suffix</label>
      <input type="text" id="newSuffix" name="suffix">
    </div>

    <div class="input-group">
      <label>Province</label>
      <div class="suggestions">
        <input type="text" id="newProvince" name="province" autocomplete="off" required>
        <div id="provinceList" class="listbox hidden"></div>
      </div>
    </div>

    <div class="input-group">
      <label>City/Municipality</label>
      <div class="suggestions">
        <input type="text" id="newCity" name="city" autocomplete="off" required>
        <div id="cityList" class="listbox hidden"></div>
      </div>
    </div>

    <div class="input-group">
      <label>Barangay</label>
      <input type="text" id="newBarangay" name="barangay" required>
    </div>

    <div class="input-group">
      <label>Address</label>
      <input type="text" id="newAddress" name="street" required>
    </div>

    <div class="input-group">
      <label>Household Head</label>
      <input type="text" id="newHouseholdHead" name="houseNumber" required>
    </div>

    <div class="input-group">
      <label>Total Number of Household Members</label>
      <input type="text" id="newNumOfHouseholdMem" name="purok" required>
    </div>

  </div>

  <div class="button-group">
    <button type="reset" class="btn secondary">Clear</button>
    <button type="button" id="nextBtn1" class="btn">Next</button>
  </div>
</div>

<div id="page2" class="hidden">
  <h2>B. Interview Information</h2>

  <table class="interview-table">
    <thead>
      <tr>
        <th>Visit</th>
        <th>Date of Visit</th>
        <th>Time Start</th>
        <th>Time End</th>
        <th>Result</th>
        <th>Date of Next Visit</th>
        <th>Name of Interviewer</th>
        <th>Name of Supervisor</th>
      </tr>
    </thead>
    <tbody>

      <tr>
        <td>1st Visit</td>
        <td><input type="date" id="visit1Date"></td>
        <td><input type="time" id="visit1Start"></td>
        <td><input type="time" id="visit1End"></td>
        <td>
          <select id="visit1Result">
            <option value="">Select</option>
            <option value="C">C</option>
            <option value="CB">CB</option>
            <option value="R">R</option>
          </select>
        </td>
        <td><input type="date" id="visit1NextDate"></td>
        <td><input type="text" id="visit1Interviewer"></td>
        <td><input type="text" id="visit1Supervisor"></td>
      </tr>

      <tr>
        <td>2nd Visit</td>
        <td><input type="date" id="visit2Date"></td>
        <td><input type="time" id="visit2Start"></td>
        <td><input type="time" id="visit2End"></td>
        <td>
          <select id="visit2Result">
            <option value="">Select</option>
            <option value="C">C</option>
            <option value="CB">CB</option>
            <option value="R">R</option>
          </select>
        </td>
        <td><input type="date" id="visit2NextDate"></td>
        <td><input type="text" id="visit2Interviewer"></td>
        <td><input type="text" id="visit2Supervisor"></td>
      </tr>

    </tbody>
  </table>

  <div class="button-group">
    <button type="reset" class="btn secondary">Clear</button>
    <button type="button" id="backBtn2" class="btn secondary">Back</button>
    <button type="button" id="nextBtn2" class="btn">Next</button>
  </div>
</div>

<div id="page3" class="hidden">

  <h2>C. Encoding Information</h2>

  <div class="input-group">
    <label>Civil Status</label>
    <select name="civilStatus" required>
      <option value="">Select</option>
      <option value="Single">Single</option>
      <option value="Married">Married</option>
      <option value="Widowed">Widowed</option>
    </select>
  </div>

  <div class="input-group">
    <label>Birth Date</label>
    <input type="date" name="birthDate" required>
  </div>

  <div class="input-group">
    <label>Gender</label>
    <select name="gender" required>
      <option value="">Select</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>
  </div>

  <div class="input-group">
    <label>Citizenship</label>
    <input type="text" name="citizenship" required>
  </div>

  <div class="input-group">
    <label>Contact Number</label>
    <input type="text" name="contactNumber">
  </div>

  <div class="input-group">
    <label>Email</label>
    <input type="email" name="email">
  </div>

  <div class="input-group">
    <label>Category</label>
    <select name="category" id="category" required>
      <option value="">Select</option>
      <option value="senior">Senior</option>
      <option value="pwd">PWD</option>
      <option value="solo_parent">Solo Parent</option>
      <option value="regular">Regular</option>
    </select>
  </div>

  <div class="input-group" id="idNumberGroup">
    <label>ID Number</label>
    <input type="text" id="idNumber" name="idNumber">
  </div>

  <div class="input-group">
    <label>Occupation</label>
    <input type="text" name="occupation">
  </div>

  <div class="button-group">
    <button type="reset" class="btn secondary">Clear</button>
    <button type="button" id="backBtn3" class="btn secondary">Back</button>
    <button type="submit" id="submitBtn" class="btn">Submit</button>
  </div>
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
