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
$selectedOwnership = isset($_GET['ownership']) ? $conn->real_escape_string($_GET['ownership']) : '';

$whereClauses = [];

if (!empty($searchTerm)) {
    $whereClauses[] = "
        (h.household_id LIKE '%$searchTerm%' OR
        h.household_head LIKE '%$searchTerm%')
    ";
}

if (!empty($selectedOwnership)) {
    $whereClauses[] = "h.housing_ownership = '$selectedOwnership'";
}

$whereClause = '';
if (!empty($whereClauses)) {
    $whereClause = " WHERE " . implode(" AND ", $whereClauses);
}

$sql = "
    SELECT 
        h.household_id,
        h.household_head,
        h.housing_ownership,
        h.water_source,
        h.toilet_facility,
        h.electricity_source,
        h.waste_disposal,
        h.building_type,
        COUNT(DISTINCT r.person_id) as member_count
    FROM household h
    LEFT JOIN residents r ON h.household_id = r.household_id
    $whereClause
    GROUP BY h.household_id, h.household_head, h.housing_ownership, 
             h.water_source, h.toilet_facility, h.electricity_source,
             h.waste_disposal, h.building_type
    ORDER BY h.household_id ASC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Household Directory</title>
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
            <li><a href="household.php" class="active">Households</a></li>
            <li><a href="residents.php">Residents</a></li>
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
            <h2>Household Directory</h2>
            <a href="add_household.php" class="btn primary-btn">+ Add New Household</a>
          </div>

          <div class="filter-section">
            <form method="GET" action="household.php" id="filterForm"> 
                <div class="search-bar-group">
                    <input 
                        type="text" 
                        placeholder="Search by Household ID or Head..." 
                        class="search-input" 
                        id="searchInput"
                        name="search"
                        value="<?php echo htmlspecialchars($searchTerm); ?>"
                    />
                </div>

                <div class="filter-dropdowns">
                    <div class="input-group">
                        <label for="ownership-filter">Ownership</label>
                        <select id="ownership-filter" name="ownership" onchange="document.getElementById('filterForm').submit()">
                            <option value="">-- Select Ownership --</option>
                            <option value="Owned" <?php echo ($selectedOwnership == 'Owned') ? 'selected' : ''; ?>>Owned</option>
                            <option value="Rented" <?php echo ($selectedOwnership == 'Rented') ? 'selected' : ''; ?>>Rented</option>
                        </select>
                    </div>
                </div>
            </form>
          </div>

          <div class="data-table-card">
            <table>
              <thead>
                <tr>
                  <th>Household ID</th>
                  <th>Household Head</th>
                  <th>Members</th>
                  <th>Ownership</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="residentsTableBody">
                <?php
                if ($result->num_rows > 0) {
                    $counter = 0;
                    while($row = $result->fetch_assoc()) {
                        $householdId = htmlspecialchars($row['household_id']); 
                        $rowId = "row-" . $counter;
                        $detailsId = "details-" . $counter;
                        
                        echo "<tr class='expandable-row' onclick='toggleDetails(\"$detailsId\", \"$rowId\")'>";
                        echo "<td><div class='name-cell'><span class='expand-icon' id='icon-$rowId'>▶</span><span>" . htmlspecialchars($row['household_id'] ?: 'N/A') . "</span></div></td>";
                        echo "<td>" . htmlspecialchars($row['household_head'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['member_count'] ?? 0) . "</td>";
                        echo "<td>" . htmlspecialchars($row['housing_ownership'] ?? 'N/A') . "</td>";
                        echo "<td>
                                <button class='btn small-btn edit-btn' onclick='event.stopPropagation(); editHousehold(\"{$householdId}\")'>Edit</button>
                                <button class='btn small-btn delete-btn' onclick='event.stopPropagation(); deleteHousehold(\"{$householdId}\")'>Delete</button>
                              </td>";
                        echo "</tr>";
                        
                        echo "<tr class='details-row' id='$detailsId'>";
                        echo "<td colspan='5' class='details-cell'>";
                        echo "<div class='details-content'>";
                        
                        echo "<div class='status-summary'>";
                        echo "<div class='status-item'><span class='status-icon'></span> " . htmlspecialchars($row['member_count'] ?? 0) . " Family Members</div>";
                        echo "</div>";
                        
                        echo "<div class='tab-navigation'>";
                        echo "<button class='tab-button active' onclick='switchTab(event, \"property-$counter\")'>Property Details</button>";
                        echo "<button class='tab-button' onclick='switchTab(event, \"utilities-$counter\")'>Utilities & Services</button>";
                        echo "</div>";
                        
                        echo "<div id='property-$counter' class='tab-content active'>";
                        echo "<div class='info-grid'>";
                        echo "<div class='info-card'>";
                        echo "<h4>Property Information</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Household ID:</span><span class='detail-value'>" . htmlspecialchars($row['household_id']) . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Household Head:</span><span class='detail-value'>" . htmlspecialchars($row['household_head'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Total Members:</span><span class='detail-value'>" . htmlspecialchars($row['member_count'] ?? 0) . "</span></div>";
                        echo "</div>";
                        
                        echo "<div class='info-card'>";
                        echo "<h4>Housing Details</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Housing Ownership:</span><span class='detail-value'>" . htmlspecialchars($row['housing_ownership'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Building Type:</span><span class='detail-value'>" . htmlspecialchars($row['building_type'] ?? 'N/A') . "</span></div>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                        
                        echo "<div id='utilities-$counter' class='tab-content'>";
                        echo "<div class='info-grid'>";
                        echo "<div class='info-card'>";
                        echo "<h4>Utilities</h4>";
                        echo "<div class='detail-item'><span class='detail-label'>Water Source:</span><span class='detail-value'>" . htmlspecialchars($row['water_source'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Electricity Source:</span><span class='detail-value'>" . htmlspecialchars($row['electricity_source'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Toilet Facility:</span><span class='detail-value'>" . htmlspecialchars($row['toilet_facility'] ?? 'N/A') . "</span></div>";
                        echo "<div class='detail-item'><span class='detail-label'>Waste Disposal:</span><span class='detail-value'>" . htmlspecialchars($row['waste_disposal'] ?? 'N/A') . "</span></div>";
                        echo "</div>";
                        
                        echo "</div>"; 
                        echo "</td>";
                        echo "</tr>";
                        
                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No households found matching the filters or search term.</td></tr>";
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

function editHousehold(id) {
    window.location.href = `edit_household.php?id=${id}`;
}

function deleteHousehold(id) {
    if (confirm(`Are you sure you want to delete household ${id}? This action cannot be undone.`)) {
        window.location.href = `delete_household.php?id=${id}`;
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