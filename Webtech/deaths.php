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

// --- DATA PROCESSING START ---

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

// Function for Record Number
function getNextDeathRecordNumber($conn) {
    if (!$conn || $conn->connect_error) return 'D-ERR';
    $result = $conn->query("SELECT record_number FROM deaths ORDER BY id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $last_number = intval(substr($row['record_number'], 2)); 
        return 'D-' . str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'D-001';
}

$next_record_number = getNextDeathRecordNumber($conn);

function formatDate($date) {
    if (empty($date) || $date == '0000-00-00') return 'N/A';
    return date('M d, Y', strtotime($date));
}

// Fetch Death Records
$death_records = [];
$sql_records = "SELECT * FROM deaths" . $where_sql . " ORDER BY date_of_death DESC";
$stmt = $conn->prepare($sql_records);

if ($stmt) {
    if (count($bind_params) > 0) {
        $stmt->bind_param($bind_types, ...$bind_params);
    }
    $stmt->execute();
    $result_records = $stmt->get_result();
    while ($row = $result_records->fetch_assoc()) {
        $death_records[] = $row;
    }
    $stmt->close();
}

// --- AJAX HANDLER ---
// Ito ang sasagot sa JavaScript search nang hindi nire-refresh ang buong page
if (isset($_GET['ajax'])) {
    if (!empty($death_records)) {
        foreach ($death_records as $record) {
            $deathId = htmlspecialchars($record['id']);
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
        }
    } else {
        echo "<tr><td colspan='5' style='text-align:center;'>No death records found.</td></tr>";
    }
    exit; // Itigil ang script dito kapag AJAX request
}

// Statistics (Para sa Dashboard Cards)
$total_deaths = 0;
$result_total = $conn->query("SELECT COUNT(*) AS total FROM deaths");
if ($result_total && $row_total = $result_total->fetch_assoc()) {
    $total_deaths = $row_total['total'];
}

$top_causes = [];
$sql_top_causes = "SELECT cause_of_death, COUNT(id) AS count FROM deaths GROUP BY cause_of_death ORDER BY count DESC LIMIT 2";
$result_top_causes = $conn->query($sql_top_causes);
if ($result_top_causes) {
    while ($row = $result_top_causes->fetch_assoc()) { $top_causes[] = $row; }
}
while (count($top_causes) < 2) { $top_causes[] = ['cause_of_death' => 'N/A', 'count' => 0]; }

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
                    <li><a href="deaths.php" class="active">Deaths</a></li>
                    <li><a href="documents.php">Documents</a></li>
                    <li><a href="staff_documents.php">Generate Certificates</a></li>
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
                        <button class="btn primary-btn" onclick="openDeathRecordModal()">+ Add New Record</button>
                    </div>
                    
                    <?php if ($status_success): ?><div class="alert success"><?php echo htmlspecialchars($status_success); ?></div><?php endif; ?>
                    <?php if ($status_error): ?><div class="alert error"><?php echo htmlspecialchars($status_error); ?></div><?php endif; ?>

                    <div class="filter-section">
                        <h3>Death Statistics</h3>
                        <div class="dashboard-grid">
                            <div class="stat-card report-stat">
                                <p class="stat-label">Total Death Records</p>
                                <p class="stat-value"><span><?php echo $total_deaths; ?></span></p> 
                            </div>
                            <div class="stat-card report-stat" style="border-left-color: #dc3545">
                                <p class="stat-label">Cause: <?php echo htmlspecialchars($top_causes[0]['cause_of_death']); ?></p>
                                <p class="stat-value"><span><?php echo $top_causes[0]['count']; ?></span></p>
                            </div>
                            <div class="stat-card report-stat" style="border-left-color: #ffc107">
                                <p class="stat-label">Cause: <?php echo htmlspecialchars($top_causes[1]['cause_of_death']); ?></p>
                                <p class="stat-value"><span><?php echo $top_causes[1]['count']; ?></span></p>
                            </div>
                        </div>

                        <form method="GET" action="deaths.php" id="filterForm">
                            <div class="filter-dropdowns">
                                <div class="input-group">
                                    <label for="filterName">Name</label>
                                    <input type="text" id="filterName" name="filterName" placeholder="Search by Name..." value="<?php echo $current_filter_name; ?>" autocomplete="off" />
                                </div>
                                <div class="input-group">
                                    <label for="filterYear">Year of Death</label>
                                    <input type="number" id="filterYear" name="filterYear" placeholder="e.g., 2024" value="<?php echo $current_filter_year; ?>" />
                                </div>
                                <div class="input-group">
                                    <label>&nbsp;</label>
                                    <button type="button" id="clearBtn" class="btn secondary" style="width: 100%">Clear Search</button>
                                </div>
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
                                <?php if (!empty($death_records)): ?>
                                    <?php foreach ($death_records as $record): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($record['name']); ?></td>
                                            <td><?php echo htmlspecialchars($record['age']); ?></td>
                                            <td><?php echo htmlspecialchars($record['cause_of_death']); ?></td>
                                            <td><?php echo formatDate($record['date_of_death']); ?></td>
                                            <td>
                                                <button class='btn small-btn edit-btn' onclick='editDeath("<?php echo $record['id']; ?>")'>Edit</button>
                                                <button class='btn small-btn delete-btn' onclick='deleteDeath("<?php echo $record['id']; ?>")'>Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan='5' style="text-align:center;">No death records found.</td></tr>
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
                <a class="close-btn" onclick="closeModal('deathRecordModal')">&times;</a>
                <h2 class="form-title">Register Death Record</h2>
                <form action="process_death.php" method="POST">
                    <div class="form-section">
                        <h3>Basic Information</h3>
                        <div class="form-grid">
                            <div class="input-group">
                                <label>Record Number</label>
                                <input type="text" value="<?php echo $next_record_number; ?>" disabled />
                                <input type="hidden" name="record_number" value="<?php echo $next_record_number; ?>">
                            </div>
                            <div class="input-group">
                                <label for="residentName">Full Name</label>
                                <input type="text" id="residentName" name="resident_name" required />
                            </div>
                            <div class="input-group">
                                <label for="residentAge">Age</label>
                                <input type="number" id="residentAge" name="resident_age" required />
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
                    <div class="form-actions">
                        <button type="button" class="btn secondary" onclick="closeModal('deathRecordModal')">Cancel</button>
                        <button type="submit" class="btn primary-btn create">Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const filterName = document.getElementById('filterName');
        const filterYear = document.getElementById('filterYear');
        const tableBody = document.getElementById('deathRecordTable');
        const clearBtn = document.getElementById('clearBtn');

        // Function para sa AJAX Search
        async function performSearch() {
            const nameValue = filterName.value;
            const yearValue = filterYear.value;

            // Bubuo ng URL para sa AJAX request
            const params = new URLSearchParams({
                filterName: nameValue,
                filterYear: yearValue,
                ajax: 1
            });

            try {
                const response = await fetch(`deaths.php?${params.toString()}`);
                const html = await response.text();
                tableBody.innerHTML = html;
            } catch (error) {
                console.error('Search Error:', error);
            }
        }

        // Event Listeners para sa typing (Live Search)
        filterName.addEventListener('input', performSearch);
        filterYear.addEventListener('input', performSearch);

        // Clear button logic
        clearBtn.addEventListener('click', () => {
            filterName.value = '';
            filterYear.value = '';
            performSearch();
        });

        // Functions for Modal & Actions
        function openDeathRecordModal() { document.getElementById("deathRecordModal").classList.add("show"); }
        function closeModal(id) { document.getElementById(id).classList.remove("show"); }
        function editDeath(id) { window.location.href = `edit_deaths.php?id=${id}`; }
        function deleteDeath(id) {
            if (confirm(`Are you sure you want to delete this record?`)) {
                window.location.href = `delete_deaths.php?id=${id}`;
            }
        }

        document.getElementById("logoutBtn").addEventListener("click", () => {
            window.location.href = "logout.php";
        });
    </script>
</body>
</html>