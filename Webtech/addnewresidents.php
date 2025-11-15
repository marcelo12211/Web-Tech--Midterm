<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect fields from the form
    $firstName   = $conn->real_escape_string($_POST['firstName']);
    $middleName  = $conn->real_escape_string($_POST['middleName'] ?? '');
    $lastName    = $conn->real_escape_string($_POST['lastName']);
    $suffix      = $conn->real_escape_string($_POST['suffix'] ?? '');
    $province    = $conn->real_escape_string($_POST['province']);
    $city        = $conn->real_escape_string($_POST['city']);
    $barangay    = $conn->real_escape_string($_POST['barangay']);

    $gender      = $_POST['gender'];
    $civilStatus = $_POST['civilStatus'];
    $birthDate   = $_POST['birthDate'];
    $citizenship = $conn->real_escape_string($_POST['citizenship']);

    // Create full name for NAME column
    $fullName = trim("$firstName $middleName $lastName $suffix");

    // Calculate age
    $age = '';
    if (!empty($birthDate)) {
        $age = date_diff(date_create($birthDate), date_create('today'))->y;
    }

    // Fields not in the form → set to NULL
    $relationshipToHead = NULL;
    $birthPlace = "$barangay, $city, $province";  // Best possible match
    $ethnicity = NULL;
    $religion = NULL;
    $highestEducation = NULL;
    $isEnrolled = NULL;
    $schoolLevel = NULL;
    $schoolAddress = NULL;

    // Insert into household_members table
    $sql = "
        INSERT INTO household_members
        (
            IDENTIFICATION_ID,
            RELATIONSHIP_TO_HEAD,
            NAME,
            SEX,
            BIRTHDATE,
            AGE,
            BIRTHPLACE,
            NATIONALITY,
            ETHNICITY,
            RELIGION,
            `MARITAL-STATUS`,  -- 
            HIGHEST_ATTAINED_EDUCATION,
            IS_ENROLLED,
            SCHOOL_LEVEL,
            SCHOOL_ADDRESS
        )
        VALUES (
            NULL,               -- Assuming ID is auto-generated
            NULL,               -- Assuming no relationship to head provided
            '$fullName',
            '$gender',
            '$birthDate',
            '$age',
            '$birthPlace',
            '$citizenship',
            NULL,
            NULL,
            '$civilStatus',     -- Adjusted column name to match `MARITAL_STATUS`
            NULL,
            NULL,
            NULL,
            NULL
        )
    ";

    if ($conn->query($sql) === TRUE) {
        $success = "Resident added successfully!";
    } else {
        $error = "Error inserting data: " . $conn->error;
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
                <li><a href="addnewresidents.php"class="active">Add Resident</a></li>
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
      <div class="add-resident-form-container">
        <div class="form-modal-card">

          <a href="residents.php" class="close-btn">&times;</a>

          <?php
          if(isset($success)) echo "<p style='color:green;'>$success</p>";
          if(isset($error)) echo "<p style='color:red;'>$error</p>";
          ?>
<form id="addResidentForm" method="POST">

<h2 class="form-title">Add New Resident</h2>

<!-- PAGE 1 -->
<div id="page1">
  <h2>A. Identification</h2>

  <div class="form-grid">

    <div class="input-group">
      <label>First Name</label>
      <input type="text" name="firstName" id="firstName" required>
    </div>

    <div class="input-group">
      <label>Middle Name</label>
      <input type="text" name="middleName" id="middleName">
    </div>

    <div class="input-group">
      <label>Surname</label>
      <input type="text" name="lastName" id="lastName" required>
    </div>

    <div class="input-group">
      <label>Suffix</label>
      <input type="text" name="suffix" id="suffix">
    </div>

    <div class="input-group">
      <label>Province</label>
      <input type="text" name="province" id="province" required>
    </div>

    <div class="input-group">
      <label>City/Municipality</label>
      <input type="text" name="city" id="city" required>
    </div>

    <div class="input-group">
      <label>Barangay</label>
      <input type="text" name="barangay" id="barangay" required>
    </div>

  </div>

  <div class="button-group">
    <button type="button" id="nextBtn1" class="btn">Next</button>
  </div>
</div>

<!-- PAGE 2 -->
<div id="page2" class="hidden">
  <h2>B. Basic Details</h2>

  <div class="form-grid">
    <div class="input-group">
      <label>Birth Date</label>
      <input type="date" name="birthDate" required>
    </div>

    <div class="input-group">
      <label>Gender</label>
      <select name="gender" required>
        <option value="">Select</option>
        <option>Male</option>
        <option>Female</option>
      </select>
    </div>

    <div class="input-group">
      <label>Civil Status</label>
      <select name="civilStatus" required>
        <option value="">Select</option>
        <option>Single</option>
        <option>Married</option>
        <option>Widowed</option>
      </select>
    </div>

    <div class="input-group">
      <label>Citizenship</label>
      <select name="citizenship" required>
        <option value="">Select</option>
        <option>Filipino</option>
        <option>American</option>
        <option>Canadian</option>
        <option>Japanese</option>
        <option>Korean</option>
        <option>Chinese</option>
        <option>Australian</option>
        <option>British</option>
      </select>
    </div>
  </div>

  <div class="button-group">
    <button type="button" id="backBtn2" class="btn secondary">Back</button>
    <button type="button" id="nextBtn2" class="btn">Next</button>
  </div>

</div>

</form>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
// PAGE SYSTEM
const pages = ["page1", "page2"];
let currentPage = 0;

function showPage(index) {
  pages.forEach((id, i) => {
    const page = document.getElementById(id);
    if (i === index) {
      page.classList.remove("hidden");
    } else {
      page.classList.add("hidden");
    }
  });
}

// VALIDATION RULES
function isValidName(value) {
  // not pure symbols, letters + allowed symbols ok
  // allowed symbols: space, hyphen, apostrophe, period
  const validPattern = /^[A-Za-z .'-]+$/;
  const hasLetter = /[A-Za-z]/;

  return validPattern.test(value) && hasLetter.test(value);
}

function validatePage1() {
  const firstName = document.querySelector("input[name='firstName']").value.trim();
  const lastName = document.querySelector("input[name='lastName']").value.trim();
  const province = document.querySelector("input[name='province']").value.trim();
  const city = document.querySelector("input[name='city']").value.trim();
  const barangay = document.querySelector("input[name='barangay']").value.trim();

  if (!isValidName(firstName)) {
    alert("Enter a valid first name.");
    return false;
  }
  if (!isValidName(lastName)) {
    alert("Enter a valid surname.");
    return false;
  }
  if (province === "" || city === "" || barangay === "") {
    alert("Fill out all required fields.");
    return false;
  }
  return true;
}

function validatePage2() {
  const birthDate = document.querySelector("input[name='birthDate']").value;
  const gender = document.querySelector("select[name='gender']").value;
  const civilStatus = document.querySelector("select[name='civilStatus']").value;
  const citizenship = document.querySelector("select[name='citizenship']").value;

  if (birthDate === "" || gender === "" || civilStatus === "" || citizenship === "") {
    alert("Fill out all required fields.");
    return false;
  }
  return true;
}

// BUTTON EVENTS
document.getElementById("nextBtn1").onclick = () => {
  if (validatePage1()) {
    currentPage = 1;
    showPage(currentPage);
  }
};

document.getElementById("backBtn2").onclick = () => {
  currentPage = 0;
  showPage(currentPage);
};

document.getElementById("nextBtn2").onclick = () => {
  if (validatePage2()) {
    document.getElementById("addResidentForm").submit();
  }
};

// INIT
showPage(0);
</script>


</body>
</html>