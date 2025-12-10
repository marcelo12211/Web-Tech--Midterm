<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); 
header('Pragma: no-cache');   
header('Expires: 0');         
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

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
    <link rel="stylesheet" href="css/residents-details.css" /> 
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
          <span id="userName" class="user-info">Welcome, <?php echo htmlspecialchars($logged_in_username); ?></span>
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
                        <select id="category-filter" name="category" onchange="document.getElementById('filterForm').submit()">
                            <option value="">-- Select Category --</option>
                            <option value="senior" <?php echo ($selectedCategory == 'senior') ? 'selected' : ''; ?>>Senior Citizen</option>
                            <option value="pwd" <?php echo ($selectedCategory == 'pwd') ? 'selected' : ''; ?>>PWD</option>
                            <option value="pregnant" <?php echo ($selectedCategory == 'pregnant') ? 'selected' : ''; ?>>Pregnant</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="purok-filter">Purok</label>
                        <select id="purok-filter" name="purok" onchange="document.getElementById('filterForm').submit()">
                            <option value="">-- Select Purok --</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($selectedPurok == $i) ? 'selected' : ''; ?>>Purok <?php echo $i; ?></option>
                            <?php endfor; ?>
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
                        if (!$isSenior && !$isPWD && !$isPregnant) {
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
                        echo "</div>";
                        
                        echo "<div id='health-$counter' class='tab-content'>";
                        echo "<div class='info-grid'>";
                        echo "<div class='info-card'>";
                        echo "<h4>Health Status</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Vaccination:</span><span class='detail-value'><span class='badge " . ($isVaccinated ? "badge-yes" : "badge-no") . "'>" . ($isVaccinated ? "Vaccinated" : "Not Vaccinated") . "</span></span></div>";
                        
                        $healthInsurance = !empty($row['health_insurance']) && $row['health_insurance'] != 'None' ? $row['health_insurance'] : 'None';
                        echo "<div class='detail-item'><span class='detail-label'>Health Insurance:</span><span class='detail-value'>" . htmlspecialchars($healthInsurance) . "</span></div>";
                        echo "</div>";
                        
                        echo "<div class='info-card'>";
                        echo "<h4>Special Categories</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Senior Citizen:</span><span class='detail-value'><span class='badge " . ($isSenior ? "badge-yes" : "badge-no") . "'>" . ($isSenior ? "Yes" : "No") . "</span></span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>PWD Status:</span><span class='detail-value'><span class='badge " . ($isPWD ? "badge-yes" : "badge-no") . "'>" . ($isPWD ? "Yes" : "No") . "</span></span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Pregnant:</span><span class='detail-value'><span class='badge " . ($isPregnant ? "badge-yes" : "badge-no") . "'>" . ($isPregnant ? "Yes" : "No") . "</span></span></div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div id='education-$counter' class='tab-content'>";
                        echo "<div class='info-grid'>";
                        echo "<div class='info-card'>";
                        echo "<h4>Education</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Education Level:</span><span class='detail-value'>" . htmlspecialchars($row['education_level'] ?? 'N/A') . "</span></div>";
                        echo "</div>";
                        
                        echo "<div class='info-card'>";
                        echo "<h4>Employment</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Occupation:</span><span class='detail-value'>" . htmlspecialchars($row['occupation'] ?? 'N/A') . "</span></div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div id='family-$counter' class='tab-content'>";
                        echo "<div class='info-grid'>";
                        echo "<div class='info-card'>";
                        echo "<h4>Family Information</h4>";
                        $childrenCount = isset($row['children_count']) ? $row['children_count'] : 0;
                        echo "<div class='detail-item'><span class='detail-label'>Number of Children:</span><span class='detail-value'>" . htmlspecialchars($childrenCount) . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Civil Status:</span><span class='detail-value'>" . htmlspecialchars($row['civil_status'] ?? 'N/A') . "</span></div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "</div>"; 
                        echo "</td>";
                        echo "</tr>";
                        
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
    return; 
}

function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    logoutBtn.addEventListener("click", () => {
        window.location.href = "logout.php"; 
    });
}

function showUser() {
    return;
}

window.onload = function () {
    setupLogout();
    setupLiveSearch();
};
</script>
</body>
</html>