<?php
include 'db_connect.php'; 

$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$selectedPurok = isset($_GET['purok']) ? $conn->real_escape_string($_GET['purok']) : '';

$whereClauses = [];

if (!empty($searchTerm)) {
    $whereClauses[] = "
        (T1.ID LIKE '%$searchTerm%' OR
         T1.PROVINCE LIKE '%$searchTerm%' OR
         T1.MUNICIPALITY LIKE '%$searchTerm%' OR
         T1.BARANGAY LIKE '%$searchTerm%' OR
         T1.ADDRESS LIKE '%$searchTerm%' OR
         T1.RESPONDENT_NAME LIKE '%$searchTerm%')
    ";
}

if (!empty($selectedPurok)) {
    $whereClauses[] = "T2.PUROK = '$selectedPurok'";
}

if (!empty($selectedCategory)) {
    switch ($selectedCategory) {
        case 'senior':
            $whereClauses[] = "T2.IS_REGISTERED_SENIOR = 1";
            break;
        case 'solo':

            $whereClauses[] = "T2.RESIDENT_TYPE = 'Solo Parent'";
            break;
        case 'pwd':
            $whereClauses[] = "T2.IS_DISABLED = 1";
            break;
    }
}

$whereClause = '';
if (!empty($whereClauses)) {
    $whereClause = " WHERE " . implode(" AND ", $whereClauses);
}

$sql = "
    SELECT 
        T1.ID, T1.PROVINCE, T1.MUNICIPALITY, T1.BARANGAY, T1.ADDRESS, T1.RESPONDENT_NAME, T1.GENDER,
        T2.RESIDENT_TYPE, T2.PUROK, T2.IS_DISABLED, T2.IS_REGISTERED_SENIOR
    FROM identification T1 
    LEFT JOIN demographics T2 ON T1.ID = T2.MEMBER_ID
    $whereClause
    ORDER BY T1.ID ASC
";

$result = $conn->query($sql);
function getCategories($row) {
    $categories = [];
    if (isset($row['IS_REGISTERED_SENIOR']) && $row['IS_REGISTERED_SENIOR'] == 1) {
        $categories[] = 'Senior';
    }
    if (isset($row['IS_DISABLED']) && $row['IS_DISABLED'] == 1) {
        $categories[] = 'PWD';
    }
    if (isset($row['RESIDENT_TYPE']) && $row['RESIDENT_TYPE'] == 'Solo Parent') {
        $categories[] = 'Solo Parent';
    }
    return empty($categories) ? 'Regular' : implode(', ', $categories);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Residents Directory</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        .filter-dropdowns {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
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
            <form method="GET" action="residents.php" id="filterForm"> 
                <div class="search-bar-group">
                    <input 
                        type="text" 
                        placeholder="Search by name, ID, address, or location..." 
                        class="search-input" 
                        id="searchInput"
                        name="search"
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                    />
                </div>

                <div class="filter-dropdowns">
                    <div class="input-group">
                        <label for="category-filter">Category</label>
                        <select id="category-filter" name="category">
                            <option value="">-- Select Category --</option>
                            <option value="senior" <?php echo ($selectedCategory == 'senior') ? 'selected' : ''; ?>>Senior Citizen</option>
                            <option value="solo" <?php echo ($selectedCategory == 'solo') ? 'selected' : ''; ?>>Solo Parent</option>
                            <option value="pwd" <?php echo ($selectedCategory == 'pwd') ? 'selected' : ''; ?>>PWD</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="purok-filter">Purok</label>
                        <select id="purok-filter" name="purok">
                            <option value="">-- Select Purok --</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($selectedPurok == $i) ? 'selected' : ''; ?>>Purok <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="period-filter">Period of Residence</label>
                        <select id="period-filter" name="period" disabled>
                            <option value="">-- Select Period of Residence --</option>
                            <option value="0-1">1-12 Months</option>
                            <option value="1-5">1-5 Years</option>
                            <option value="5-10">5-10 Years</option>
                            <option value="10+">10+ Years</option>
                        </select>
                    </div>
                </div>
            </form>
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
                  <th>Purok</th> 
                  <th>Categories</th> 
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="residentsTableBody">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $residentId = htmlspecialchars($row['ID']);
                        echo "<tr>";
                        echo "<td>{$residentId}</td>";
                        echo "<td>{$row['PROVINCE']}</td>";
                        echo "<td>{$row['MUNICIPALITY']}</td>";
                        echo "<td>{$row['BARANGAY']}</td>";
                        echo "<td>{$row['ADDRESS']}</td>";
                        echo "<td>{$row['RESPONDENT_NAME']}</td>";
                        echo "<td>{$row['GENDER']}</td>";
                        echo "<td>" . htmlspecialchars($row['PUROK'] ?? 'N/A') . "</td>";
                        echo "<td>" . getCategories($row) . "</td>"; 
                        echo "<td>
                                <button class='btn small-btn edit-btn' onclick='editResident(\"{$residentId}\")'>Edit</button>
                                <button class='btn small-btn delete-btn' onclick='deleteResident(\"{$residentId}\")'>Delete</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No residents found matching the filters or search term: **" . htmlspecialchars($searchTerm) . "**</td></tr>";
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
let searchTimeout = null;
const filterForm = document.getElementById("filterForm");

function editResident(id) {
    window.location.href = `edit_resident.php?id=${id}`;
}

function deleteResident(id) {
    if (confirm(`Are you sure you want to delete resident with ID: ${id}? This action cannot be undone.`)) {
        window.location.href = `delete_resident.php?id=${id}`;
    }
}
function setupLiveSearch() {
    const searchInput = document.getElementById("searchInput");

    searchInput.addEventListener("input", function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterForm.submit();
        }, 500);
    });
}

function setupFilterSubmit() {
    const categoryFilter = document.getElementById("category-filter");
    const purokFilter = document.getElementById("purok-filter");
    categoryFilter.addEventListener("change", function() {
        filterForm.submit();
    });
    purokFilter.addEventListener("change", function() {
        filterForm.submit();
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
    setupFilterSubmit();
};
</script>
</body>
</html>