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
$total_deaths = 0;
$result_total = $conn->query("SELECT COUNT(*) AS total FROM deaths");
if ($result_total && $row_total = $result_total->fetch_assoc()) {
    $total_deaths = $row_total['total'];
}

$next_record_number = getNextDeathRecordNumber($conn);
$death_records = [];
$sql_records = "SELECT id, name, age, cause_of_death, date_of_death, record_number FROM deaths ORDER BY date_of_death DESC";
$result_records = $conn->query($sql_records);

if ($result_records) {
    while ($row = $result_records->fetch_assoc()) {
        $death_records[] = $row;
    }
}
$status_success = $_SESSION['status_success'] ?? null;
unset($_SESSION['status_success']);

$status_error = $_SESSION['status_error'] ?? null;
unset($_SESSION['status_error']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Deaths and Records - Happy Hallow Barangay System</title>
    <link rel="stylesheet" href="css/style.css" />
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

          <div class="card filter-section">
            <h3>Death Statistics and Filter</h3>
            <div class="dashboard-grid">
              <div class="stat-card report-stat">
                <p class="stat-label">Total Death Records</p>
                <p class="stat-value"><span><?php echo htmlspecialchars($total_deaths); ?></span></p> 
              </div>

              <div
                class="stat-card report-stat"
                style="border-left-color: #dc3545"
              >
                <p class="stat-label">Cause: Kidney Failure</p>
                <p class="stat-value"><span>[Dynamic Count]</span></p>
              </div>

              <div
                class="stat-card report-stat"
                style="border-left-color: #ffc107"
              >
                <p class="stat-label">Cause: Old Age</p>
                <p class="stat-value"><span>[Dynamic Count]</span></p>
              </div>
            </div>

            <div class="filter-dropdowns">
              <div class="input-group">
                <label for="filterName">Name</label>
                <input
                  type="text"
                  id="filterName"
                  placeholder="Search by Name"
                />
              </div>
              <div class="input-group">
                <label for="filterYear">Year of Death</label>
                <input
                  type="number"
                  id="filterYear"
                  min="1900"
                  max="2100"
                  placeholder="e.g., 2024"
                />
              </div>
              <div class="input-group">
                <label>&nbsp;</label>
                <button class="btn primary-btn" style="width: 100%">
                  Filter
                </button>
              </div>
            </div>
          </div>

          <div class="card data-table-card">
            <h3>Death Record Details</h3>
            <div class="search-results-info">
              Displaying 1-<?php echo min(count($death_records), 10); ?> of <?php echo count($death_records); ?> records.
            </div>
            <div class="table-responsive">
              <table>
                <thead>
                  <tr>
                    <th>Rec. No.</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Cause of Death</th>
                    <th>Date of Death</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="deathRecordTable">
                <?php if (empty($death_records)): ?>
                    <tr>
                        <td colspan="6">No death records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($death_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['record_number'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($record['name']); ?></td>
                            <td><?php echo htmlspecialchars($record['age']); ?></td>
                            <td><?php echo htmlspecialchars($record['cause_of_death']); ?></td>
                            <td><?php echo htmlspecialchars($record['date_of_death']); ?></td>
                            <td class="actions-cell">
                                <a
                                  href="#"
                                  class="action-link view"
                                  onclick="viewDetails(<?php echo $record['id']; ?>)"
                                  >View Details</a
                                >
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
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
                  <label for="recordNumber"
                    >Record Number (Auto-Generated)</label
                  >
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
      function toggleIdFields() {
        const isPwd = document.getElementById("isPwd").value === "yes";
        const isSeniorCitizen =
          document.getElementById("isSeniorCitizen").value === "yes";
        document.getElementById("pwdIdField").style.display = isPwd
          ? "flex"
          : "none";
        document.getElementById("seniorCitizenIdFields").style.display =
          isSeniorCitizen ? "grid" : "none";
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