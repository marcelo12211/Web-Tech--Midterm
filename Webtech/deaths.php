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

include 'db_connect.php';

$filter_name = $_GET['filterName'] ?? '';
$filter_year = $_GET['filterYear'] ?? '';

$current_filter_name = htmlspecialchars($filter_name);
$current_filter_year = htmlspecialchars($filter_year);

$where_clauses = [];
$bind_params = [];
$bind_types = '';

if (!empty($filter_name)) {
    $where_clauses[] = "name LIKE ?";
    $bind_types .= 's';
    $bind_params[] = '%' . $filter_name . '%';
}

if (!empty($filter_year) && is_numeric($filter_year)) {
    $where_clauses[] = "YEAR(date_of_death) = ?";
    $bind_types .= 'i';
    $bind_params[] = (int)$filter_year;
}

$where_sql = count($where_clauses) > 0 ? " WHERE " . implode(" AND ", $where_clauses) : "";

function getNextDeathRecordNumber($conn) {
    if (!$conn || $conn->connect_error) {
        return 'D-ERR';
    }
    $result = $conn->query("SELECT record_number FROM deaths ORDER BY id DESC LIMIT 1");
    
    if ($result && $row = $result->fetch_assoc()) {
        $last_number = intval(substr($row['record_number'], 2)); 
        return 'D-' . str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
    } else {
        return 'D-001';
    }
}

$next_record_number = getNextDeathRecordNumber($conn);

function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}

$total_deaths = 0;
$result_total = $conn->query("SELECT COUNT(*) AS total FROM deaths");
if ($result_total && $row_total = $result_total->fetch_assoc()) {
    $total_deaths = $row_total['total'];
}

$top_causes = [];
$sql_top_causes = "
    SELECT 
        cause_of_death, 
        COUNT(id) AS count
    FROM 
        deaths
    GROUP BY 
        cause_of_death
    ORDER BY 
        count DESC, cause_of_death ASC
    LIMIT 2
";
$result_top_causes = $conn->query($sql_top_causes);

if ($result_top_causes) {
    while ($row = $result_top_causes->fetch_assoc()) {
        $top_causes[] = $row;
    }
}

while (count($top_causes) < 2) {
    $top_causes[] = ['cause_of_death' => 'N/A', 'count' => 0];
}

$death_records = [];
$sql_records = "SELECT * FROM deaths" . $where_sql . " ORDER BY date_of_death DESC";

$stmt = $conn->prepare($sql_records);

if ($stmt) {
    if (count($bind_params) > 0) {
        $stmt_params = array_merge([$bind_types], $bind_params);
        call_user_func_array([$stmt, 'bind_param'], refValues($stmt_params));
    }
    
    $stmt->execute();
    $result_records = $stmt->get_result();
    
    if ($result_records) {
        while ($row = $result_records->fetch_assoc()) {
            $death_records[] = $row;
        }
    }
    $stmt->close();
}

$status_success = $_SESSION['status_success'] ?? null;
unset($_SESSION['status_success']);

$status_error = $_SESSION['status_error'] ?? null;
unset($_SESSION['status_error']);

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
    <title>Deaths and Records - Happy Hallow Barangay System</title>
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
                <li><a href="residents.php">Residents</a></li>
                <li><a href="addnewresidents.php">Add Resident</a></li>
                <li><a href="deaths.php" class="active" >Deaths</a></li>
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
              <h2>Death Records</h2>
              <button class="btn primary-btn" onclick="openDeathRecordModal()">
                + Add New Record
              </button>
            </div>
            
            <?php if (isset($status_success)): ?>
              <div class="alert success"><?php echo htmlspecialchars($status_success); ?></div>
            <?php endif; ?>
            <?php if (isset($status_error)): ?>
              <div class="alert error"><?php echo htmlspecialchars($status_error); ?></div>
            <?php endif; ?>

            <div class="filter-section">
              <h3>Death Statistics</h3>
              <div class="dashboard-grid">
                <div class="stat-card report-stat">
                  <p class="stat-label">Total Death Records</p>
                  <p class="stat-value"><span><?php echo htmlspecialchars($total_deaths); ?></span></p> 
                </div>

                <div class="stat-card report-stat" style="border-left-color: #dc3545">
                  <p class="stat-label">Cause: <?php echo htmlspecialchars($top_causes[0]['cause_of_death']); ?></p>
                  <p class="stat-value"><span><?php echo htmlspecialchars($top_causes[0]['count']); ?></span></p>
                </div>

                <div class="stat-card report-stat" style="border-left-color: #ffc107">
                  <p class="stat-label">Cause: <?php echo htmlspecialchars($top_causes[1]['cause_of_death']); ?></p>
                  <p class="stat-value"><span><?php echo htmlspecialchars($top_causes[1]['count']); ?></span></p>
                </div>
              </div>

              <form method="GET" action="deaths.php" id="filterForm">
                  <div class="filter-dropdowns">
                    <div class="input-group">
                      <label for="filterName">Name</label>
                      <input
                        type="text"
                        id="filterName"
                        name="filterName"
                        placeholder="Search by Name"
                        value="<?php echo $current_filter_name; ?>"
                      />
                    </div>
                    <div class="input-group">
                      <label for="filterYear">Year of Death</label>
                      <input
                        type="number"
                        id="filterYear"
                        name="filterYear"
                        min="1900"
                        max="2100"
                        placeholder="e.g., 2024"
                        value="<?php echo $current_filter_year; ?>"
                      />
                    </div>
                    <div class="input-group">
                      <label>&nbsp;</label>
                      <button type="submit" class="btn primary-btn" style="width: 100%">
                        Filter
                      </button>
                    </div>
                    <?php if (!empty($filter_name) || !empty($filter_year)): ?>
                    <div class="input-group">
                      <label>&nbsp;</label>
                      <a href="deaths.php" class="btn secondary" style="width: 100%; text-align: center;">Clear Filters</a>
                    </div>
                    <?php endif; ?>
                  </div>
              </form>
            </div>

            <div class="data-table-card">
              <table>
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Cause of Death</th>
                    <th>Date of Death</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="deathRecordTable">
                <?php
                if (!empty($death_records)) {
                    $counter = 0;
                    foreach ($death_records as $record) {
                        $deathId = htmlspecialchars($record['id']); 
                        $rowId = "row-" . $counter;
                        $detailsId = "details-" . $counter;

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($record['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($record['age']) . "</td>";
                        echo "<td>" . htmlspecialchars($record['cause_of_death']) . "</td>";
                        echo "<td>" . formatDate($record['date_of_death']) . "</td>";
                        echo "<td>
                                <button class='btn small-btn edit-btn' onclick='editDeath(\"{$deathId}\")'>Edit</button>
                                <button class='btn small-btn delete-btn' onclick='deleteDeath(\"{$deathId}\")'>Delete</button>
                              </td>";
                        echo "</tr>";
                        
                        $counter++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No death records found.</td></tr>";
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </main>
      </div>
    </div>

    <div id="deathRecordModal" class="modal-backdrop">
      <div class="modal-content-wrapper">
        <div class="form-modal-card">
          <a class="close-btn" onclick="closeModal('deathRecordModal')"
            >&times;</a
          >
          <h2 class="form-title">Register Death Record</h2>

          <form action="process_death.php" method="POST">
            <div class="form-section">
              <h3>Basic Information</h3>
              <div class="form-grid">
                <div class="input-group">
                  <label for="recordNumber">Record Number (Auto-Generated)</label>
                  <input
                    type="text"
                    id="recordNumberDisplay"
                    value="<?php echo htmlspecialchars($next_record_number); ?>"
                    disabled
                  />
                  <input type="hidden" name="record_number" value="<?php echo htmlspecialchars($next_record_number); ?>">
                </div>
                <div class="input-group">
                  <label for="residentName">Full Name</label>
                  <input type="text" id="residentName" name="resident_name" required />
                </div>
                <div class="input-group">
                  <label for="residentAge">Age</label>
                  <input type="number" id="residentAge" name="resident_age" min="0" required />
                </div>
                <div class="input-group">
                  <label for="dateOfDeath">Date of Death</label>
                  <input type="date" id="dateOfDeath" name="date_of_death" required />
                </div>
                <div class="input-group">
                  <label for="causeOfDeath">Cause of Death</label>
                  <select id="causeOfDeath" name="cause_of_death" required>
                    <option value="">Select Cause</option>
                    <option value="Kidney Failure">Kidney Failure</option>
                    <option value="Old Age (Natural)">Old Age (Natural)</option>
                    <option value="Accident">Accident</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-section">
              <h3>Special Status (For De-listing/Records Update)</h3>
              <div class="form-grid">
                <div class="input-group">
                  <label for="isPwd">Person with Disability (PWD)?</label>
                  <select id="isPwd" name="is_pwd" onchange="toggleIdFields()">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                  </select>
                </div>
                <div class="input-group">
                  <label for="isSeniorCitizen">Senior Citizen?</label>
                  <select id="isSeniorCitizen" name="is_senior" onchange="toggleIdFields()">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                  </select>
                </div>
              </div>
              <div
                id="pwdIdField"
                class="input-group"
                style="display: none; margin-top: 20px"
              >
                <label for="pwdId">PWD ID Number</label>
                <input type="text" id="pwdId" name="pwd_id" />
              </div>
              <div
                id="seniorCitizenIdFields"
                class="form-grid"
                style="display: none; margin-top: 20px"
              >
                <div class="input-group">
                  <label for="ncscRrn">NCSC-RRN Number</label>
                  <input type="text" id="ncscRrn" name="ncsc_rrn" />
                </div>
                <div class="input-group">
                  <label for="oscaId">OSCA ID Number</label>
                  <input type="text" id="oscaId" name="osca_id" />
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button
                type="button"
                class="btn secondary"
                onclick="closeModal('deathRecordModal')"
              >
                Cancel
              </button>
              <button type="submit" class="btn primary-btn create">
                Save Record
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      function editDeath(id) {
          window.location.href = `edit_deaths.php?id=${id}`;
      }

      function deleteDeath(id) {
          if (confirm(`Are you sure you want to delete this death record? This action cannot be undone.`)) {
              window.location.href = `delete_deaths.php?id=${id}`;
          }
      }

      function toggleIdFields() {
        const isPwd = document.getElementById("isPwd").value === "yes";
        const isSeniorCitizen = document.getElementById("isSeniorCitizen").value === "yes";
        document.getElementById("pwdIdField").style.display = isPwd ? "flex" : "none";
        document.getElementById("seniorCitizenIdFields").style.display = isSeniorCitizen ? "grid" : "none";
      }

      function openDeathRecordModal() {
        document.getElementById("deathRecordModal").classList.add("show");
      }

      function closeModal(modalId) {
        document.getElementById(modalId).classList.remove("show");
      }

      function setupLogout() {
        const logoutBtn = document.getElementById("logoutBtn");
        logoutBtn.addEventListener("click", () => {
          window.location.href = "logout.php";
        });
      }

      window.onload = function () {
        setupLogout();
        toggleIdFields(); 
      };
    </script>
  </body>
</html>