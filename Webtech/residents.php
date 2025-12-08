<?php
include __DIR__ . '/db_connect.php'; 

$searchTerm = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$selectedPurok = isset($_GET['purok']) ? $conn->real_escape_string($_GET['purok']) : '';

$whereClauses = [];

if (!empty($searchTerm)) {
    $whereClauses[] = "
        (T1.person_id LIKE '%$searchTerm%' OR
         CONCAT(T1.first_name, ' ', T1.middle_name, ' ', T1.surname, ' ', IFNULL(T1.suffix,'')) LIKE '%$searchTerm%')
    ";
}

if (!empty($selectedPurok)) {
    $whereClauses[] = "T1.purok = '$selectedPurok'";
}

if (!empty($selectedCategory)) {
    switch ($selectedCategory) {
        case 'senior':
            $whereClauses[] = "T1.is_senior = 1"; 
            break;
        case 'pwd':
            $whereClauses[] = "T1.is_disabled = 1"; 
            break;
        case 'pregnant':
            $whereClauses[] = "T1.is_pregnant = 1"; 
            break;
    }
}

$whereClause = '';
if (!empty($whereClauses)) {
    $whereClause = " WHERE " . implode(" AND ", $whereClauses);
}

$sql = "
    SELECT 
        T1.person_id AS ID, 
        T1.household_id,
        T1.first_name,
        T1.middle_name,
        T1.surname,
        T1.suffix,
        T1.sex,
        T1.birthdate,
        T1.civil_status,
        T1.nationality,
        T1.religion,
        T1.purok,
        T1.address,
        T1.education_level,
        T1.occupation,
        T1.is_senior,
        T1.is_disabled,
        T1.health_insurance,
        T1.vaccination,
        T1.is_pregnant,
        T1.children_count
    FROM residents T1 
    $whereClause
    ORDER BY T1.person_id ASC
";

$result = $conn->query($sql);

function calculateAge($birthdate) {
    if (empty($birthdate) || $birthdate == '0000-00-00') return 'N/A';
    $birthDate = new DateTime($birthdate);
    $today = new DateTime();
    return $birthDate->diff($today)->y . ' years old';
}

function formatDate($date) {
    if (empty($date) || $date == '0000-00-00') return 'N/A';
    return date('M d, Y', strtotime($date));
}
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
            <h2>Resident Directory</h2>
            <a href="addnewresidents.php" class="btn primary-btn">+ Add New</a>
          </div>

          <div class="filter-section">
            <form method="GET" action="residents.php" id="filterForm"> 
                <div class="search-bar-group">
                    <input 
                        type="text" 
                        placeholder="Search by name or ID..." 
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
                            <option value="pwd" <?php echo ($selectedCategory == 'pwd') ? 'selected' : ''; ?>>PWD</option>
                            <option value="pregnant" <?php echo ($selectedCategory == 'pregnant') ? 'selected' : ''; ?>>Pregnant</option>
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
                  <th>Full Name</th>
                  <th>Sex</th>
                  <th>Birthdate</th>
                  <th>Civil Status</th>
                  <th>Nationality</th>
                  <th>Purok</th>
                  <th>Address</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="residentsTableBody">
                <?php
                if ($result->num_rows > 0) {
                    $counter = 0;
                    while($row = $result->fetch_assoc()) {
                        $residentId = htmlspecialchars($row['ID']); 
                        $rowId = "row-" . $counter;
                        $detailsId = "details-" . $counter;

                        // Concatenate full name
                        $full_name = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['surname'] . ' ' . ($row['suffix'] ?? ''));

                        echo "<tr class='expandable-row' onclick='toggleDetails(\"$detailsId\", \"$rowId\")'>";
                        echo "<td><div class='name-cell'><span class='expand-icon' id='icon-$rowId'>▶</span><span>" . htmlspecialchars($full_name ?: 'N/A') . "</span></div></td>";
                        echo "<td>" . htmlspecialchars($row['sex'] ?? 'N/A') . "</td>";
                        echo "<td>" . formatDate($row['birthdate']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['civil_status'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['nationality'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['purok'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['address'] ?? 'N/A') . "</td>";
                        echo "<td>
                                <button class='btn small-btn edit-btn' onclick='event.stopPropagation(); editResident(\"{$residentId}\")'>Edit</button>
                                <button class='btn small-btn delete-btn' onclick='event.stopPropagation(); deleteResident(\"{$residentId}\")'>Delete</button>
                              </td>";
                        echo "</tr>";

                        // Details row (you can also use $full_name here)
                        echo "<tr class='details-row' id='$detailsId'>";
                        echo "<td colspan='8' class='details-cell'>";
                        echo "<div class='details-content'>";

                        $isSenior = $row['is_senior'] == 1;
                        $isPWD = $row['is_disabled'] == 1;
                        $isPregnant = $row['is_pregnant'] == 1;
                        $isVaccinated = $row['vaccination'] == 1;

                        echo "<div class='status-summary'>";
                        if ($isSenior) echo "<div class='status-item'><span class='status-icon'></span> Senior Citizen</div>";
                        if ($isPWD) echo "<div class='status-item'><span class='status-icon'></span> PWD</div>";
                        if ($isPregnant) echo "<div class='status-item'><span class='status-icon'></span> Pregnant</div>";
                        if ($isVaccinated) echo "<div class='status-item'><span class='status-icon'></span> Vaccinated</div>";
                        if (!$isSenior && !$isPWD && !$isPregnant && !$isVaccinated) {
                            echo "<div class='status-item'><span class='status-icon'></span> No Special Status</div>";
                        }
                        echo "</div>";

                        echo "<div class='tab-navigation'>";
                        echo "<button class='tab-button active' onclick='switchTab(event, \"personal-$counter\")'>Personal Info</button>";
                        echo "<button class='tab-button' onclick='switchTab(event, \"health-$counter\")'>Health & Status</button>";
                        echo "<button class='tab-button' onclick='switchTab(event, \"education-$counter\")'>Education & Work</button>";
                        echo "<button class='tab-button' onclick='switchTab(event, \"family-$counter\")'>Family</button>";
                        echo "</div>";

                        echo "<div id='personal-$counter' class='tab-content active'>";
                        echo "<div class='info-grid'>";
                        echo "<div class='info-card'>";
                        echo "<h4>Identification</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Person ID:</span><span class='detail-value'>" . htmlspecialchars($row['ID']) . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Household ID:</span><span class='detail-value'>" . htmlspecialchars($row['household_id'] ?? 'N/A') . "</span></div>";
                        echo "</div>";

                        echo "<div class='info-card'>";
                        echo "<h4>Personal Details</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Full Name:</span><span class='detail-value'>" . htmlspecialchars($full_name ?: 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Sex:</span><span class='detail-value'>" . htmlspecialchars($row['sex'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Birthdate:</span><span class='detail-value'>" . formatDate($row['birthdate']) . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Age:</span><span class='detail-value'>" . calculateAge($row['birthdate']) . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Civil Status:</span><span class='detail-value'>" . htmlspecialchars($row['civil_status'] ?? 'N/A') . "</span></div>";
                        echo "</div>";

                        echo "<div class='info-card'>";
                        echo "<h4>Location & Background</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Purok:</span><span class='detail-value'>" . htmlspecialchars($row['purok'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Address:</span><span class='detail-value'>" . htmlspecialchars($row['address'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Nationality:</span><span class='detail-value'>" . htmlspecialchars($row['nationality'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Religion:</span><span class='detail-value'>" . htmlspecialchars($row['religion'] ?? 'N/A') . "</span></div>";
                        echo "</div>";
                        echo "</div>";

                        // Other tabs remain the same...
                        echo "</div></td></tr>";

                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='8'>No residents found matching the filters or search term.</td></tr>";
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

function toggleDetails(detailsId, rowId) {
    const detailsRow = document.getElementById(detailsId);
    const icon = document.getElementById('icon-' + rowId);
    
    if (detailsRow.classList.contains('show')) {
        detailsRow.classList.remove('show');
        icon.classList.remove('expanded');
    } else {
        detailsRow.classList.add('show');
        icon.classList.add('expanded');
    }
}

function switchTab(event, tabId) {
    event.stopPropagation();
    
    const tabButton = event.currentTarget;
    const detailsContent = tabButton.closest('.details-content');
    
    const tabButtons = detailsContent.querySelectorAll('.tab-button');
    const tabContents = detailsContent.querySelectorAll('.tab-content');
    
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));
    
    tabButton.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

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