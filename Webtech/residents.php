<?php
include 'db_connect.php'; // make $conn available

// 1. Capture the search term from the URL (GET request)
$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// 2. Build the WHERE clause for the SQL query
$whereClause = '';
if (!empty($searchTerm)) {
    // Search across ID, PROVINCE, MUNICIPALITY, BARANGAY, ADDRESS, and RESPONDENT_NAME
    $whereClause = "
        WHERE 
            ID LIKE '%$searchTerm%' OR
            PROVINCE LIKE '%$searchTerm%' OR
            MUNICIPALITY LIKE '%$searchTerm%' OR
            BARANGAY LIKE '%$searchTerm%' OR
            ADDRESS LIKE '%$searchTerm%' OR
            RESPONDENT_NAME LIKE '%$searchTerm%'
    ";
}

// 3. Construct the final SQL query
// NOTE: Since you only selected columns from the 'identification' table, 
// the Age, Purok, and Category columns are NOT available in this version.
// I have removed those columns from the table to match the SQL query.
// To re-add them, you must include the JOIN query from the previous answer.
$sql = "
    SELECT ID, PROVINCE, MUNICIPALITY, BARANGAY, ADDRESS, RESPONDENT_NAME, GENDER 
    FROM identification 
    $whereClause
    ORDER BY ID ASC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Residents Directory</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        /* ADDED INLINE CSS FOR STRUCTURE AND BUTTONS */
        .search-bar-group {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .filter-dropdowns {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .small-btn {
            padding: 5px 10px;
            font-size: 0.85rem;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.2s;
            margin: 0 2px;
        }
        .edit-btn {
            background-color: var(--warning-color, #ffc107);
            color: #212529;
            border: 1px solid var(--warning-color, #ffc107);
        }
        .delete-btn {
            background-color: var(--danger-color, #dc3545);
            color: white;
            border: 1px solid var(--danger-color, #dc3545);
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
            <li><a href="residents.php" class="active">Residents</a></li>
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
        <div class="residents-directory card">
          <div class="directory-header">
            <h2>Resident Directory (Identification Data)</h2>
            <a href="addnewresidents.php" class="btn primary-btn">+ Add New</a>
          </div>

          <div class="filter-section">
            <form method="GET" action="residents.php" id="searchForm" class="search-bar-group">
                <input 
                    type="text" 
                    placeholder="Search by name, ID, address, or location..." 
                    class="search-input" 
                    id="searchInput"
                    name="search"
                    value="<?php echo htmlspecialchars($searchTerm); ?>"
                />
            </form>

            <div class="filter-dropdowns">
              <div class="input-group">
                <label for="category-filter">Category</label>
                <select id="category-filter" disabled>
                  <option value="">-- Select Category --</option>
                  <option value="senior">Senior Citizen</option>
                  <option value="solo">Solo Parent</option>
                  <option value="pwd">PWD</option>
                </select>
              </div>
              <div class="input-group">
                <label for="purok-filter">Purok</label>
                <select id="purok-filter" disabled>
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
                <select id="period-filter" disabled>
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
                  <th>Province</th>
                  <th>Municipality</th>
                  <th>Barangay</th>
                  <th>Address</th>
                  <th>Respondent Name</th>
                  <th>Gender</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="residentsTableBody">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $residentId = htmlspecialchars($row['ID']); // Capture ID safely
                        echo "<tr>";
                        echo "<td>{$residentId}</td>";
                        echo "<td>{$row['PROVINCE']}</td>";
                        echo "<td>{$row['MUNICIPALITY']}</td>";
                        echo "<td>{$row['BARANGAY']}</td>";
                        echo "<td>{$row['ADDRESS']}</td>";
                        echo "<td>{$row['RESPONDENT_NAME']}</td>";
                        echo "<td>{$row['GENDER']}</td>";
                        echo "<td>
                                <button 
                                    class='btn small-btn edit-btn' 
                                    onclick='editResident(\"{$residentId}\")'
                                >Edit</button>
                                <button 
                                    class='btn small-btn delete-btn' 
                                    onclick='deleteResident(\"{$residentId}\")'
                                >Delete</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No residents found matching: **" . htmlspecialchars($searchTerm) . "**</td></tr>";
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
// Declare a variable to hold the timer/timeout
let searchTimeout = null;

// --- Resident Action Functions ---

// Function to handle the Edit action
function editResident(id) {
    // You must create an 'edit_resident.php' file to handle the form pre-population
    window.location.href = `edit_resident.php?id=${id}`;
}

// Function to handle the Delete action
function deleteResident(id) {
    if (confirm(`Are you sure you want to delete resident with ID: ${id}? This action cannot be undone.`)) {
        // Redirect to a PHP script that handles the database deletion
        // You must create a 'delete_resident.php' file to execute the SQL DELETE command
        window.location.href = `delete_resident.php?id=${id}`;
    }
}

// --- General Utility Functions ---

function setupLiveSearch() {
    const searchInput = document.getElementById("searchInput");
    const searchForm = document.getElementById("searchForm");

    searchInput.addEventListener("input", function() {
        // 1. Clear the previous timer (if one exists)
        clearTimeout(searchTimeout);

        // 2. Set a new timer
        searchTimeout = setTimeout(function() {
            searchForm.submit();
        }, 500); // Submits after 0.5 seconds of no typing
    });
}

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
    setupLiveSearch();
};
</script>
</body>
</html>