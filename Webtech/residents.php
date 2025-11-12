<?php
include 'db_connect.php'; // make $conn available
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Residents Directory</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-container">
  <aside class="sidebar">
        <div class="logo">Happy Hallow Barangay System</div>
        <nav class="main-nav">
          <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="residents.php" class="active">Residents</a></li>
            <li><a href="addnewresidents.php">Add Resident</a></li>
            <li><a href="deaths.html">Deaths</a></li>
            <li><a href="documents.html">Documents</a></li>
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
      <div class="residents-directory card">
        <div class="directory-header">
          <h2>Resident Directory</h2>
          <a href="addnewresidents.php" class="btn primary-btn">+ Add New</a>
        </div>

        <div class="filter-section">
          <div class="search-bar-group">
            <input type="text" placeholder="Search by name, ID, or purok..." class="search-input" id="searchInput" />
          </div>

          <div class="filter-dropdowns">
            <div class="input-group">
              <label for="category-filter">Category</label>
              <select id="category-filter">
                <option value="">-- Select Category --</option>
                <option value="senior">Senior Citizen</option>
                <option value="solo">Solo Parent</option>
                <option value="pwd">PWD</option>
              </select>
            </div>
            <div class="input-group">
              <label for="purok-filter">Purok</label>
              <select id="purok-filter">
                <option value="">-- Select Purok --</option>
                <option value="1">Purok 1</option>
                <option value="2">Purok 2</option>
                <option value="3">Purok 3</option>
                <option value="4">Purok 4</option>
                <option value="5">Purok 5</option>
              </select>
            </div>
            <div class="input-group">
              <label for="period-filter">Period of Residence</label>
              <select id="period-filter">
                <option value="">-- Select Period of Residence --</option>
                <option value="0-1">1-12 Months</option>
                <option value="1-5">1-5 Years</option>
                <option value="5-10">5-10 Years</option>
                <option value="10+">10+ Years</option>
              </select>
            </div>
          </div>
        </div>

        <div class="data-table-card">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Health Insurance</th>
                <th>Age</th>
                <th>Purok</th>
                <th>Category</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="residentsTableBody">
              <?php
              $sql = "SELECT * FROM demographics ORDER BY MEMBER_ID ASC";
              $result = $conn->query($sql);

              if ($result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                      $category = '';
                      if ($row['IS_REGISTERED_SENIOR']) $category = 'Senior Citizen';
                      elseif ($row['RESIDENT_TYPE'] == 'Solo Parent') $category = 'Solo Parent';
                      elseif ($row['IS_DISABLED']) $category = 'PWD';
                      else $category = 'Regular';

                      echo "<tr>";
                      echo "<td>{$row['MEMBER_ID']}</td>";
                      echo "<td>{$row['HEALTH_INSURANCE']}</td>";
                      echo "<td>--</td>"; // You can calculate age later if you have DOB
                      echo "<td>Purok {$row['PUROK']}</td>";
                      echo "<td>{$category}</td>";
                      echo "<td>
                              <button class='btn small-btn edit-btn'>Edit</button>
                              <button class='btn small-btn delete-btn'>Delete</button>
                            </td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='6'>No residents found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
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
  // JS for filtering/searching can be added here later
};
</script>
</body>
</html>