<?php
session_start();
include '../db_connect.php'; 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$residents = [];
$search_term = $_GET['search'] ?? '';
$purok_filter = $_GET['purok'] ?? '';
$category_filter = $_GET['category'] ?? '';
$where_clauses = [];
$bind_params = [];
$bind_types = '';
$error = ''; 
$sql = "SELECT person_id, household_id, first_name, middle_name, surname, suffix, sex, 
              birthdate, civil_status, nationality, religion, purok, address, 
              residency_start_date, education_level, occupation, 
              is_senior, is_disabled, health_insurance, vaccination, is_pregnant, 
              children_count
        FROM residents";
if (!empty($search_term)) {
    $search_like = "%" . $search_term . "%";
    $where_clauses[] = "(surname LIKE ? OR first_name LIKE ?)";
    $bind_types .= 'ss';
    $bind_params[] = &$search_like;
    $bind_params[] = &$search_like;
}
if (!empty($purok_filter)) {
    $where_clauses[] = "purok = ?";
    $bind_types .= 's'; 
    $bind_params[] = &$purok_filter;
}
if (!empty($category_filter)) {
    switch ($category_filter) {
        case 'senior':
            $where_clauses[] = "is_senior = 1";
            break;
        case 'pwd':
            $where_clauses[] = "is_disabled = 1";
            break;
        case 'pregnant':
            $where_clauses[] = "is_pregnant = 1 AND sex = 'Female'";
            break;
    }
}
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY surname ASC";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $error = "SQL Prepare Error: " . $conn->error;
} else {
    if (!empty($bind_params)) {
        array_unshift($bind_params, $bind_types);
        call_user_func_array(array($stmt, 'bind_param'), $bind_params);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $residents[] = $row;
        }
        $stmt->close();
    } else {
        $error = "Error executing query: " . $stmt->error;
    }
}
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
function getFullName($res) {
    $name = htmlspecialchars($res['first_name']) . ' ';
    if (!empty($res['middle_name'])) {
        $name .= htmlspecialchars($res['middle_name'][0]) . '. ';
    }
    $name .= htmlspecialchars($res['surname']);
    if (!empty($res['suffix'])) {
        $name .= ' ' . htmlspecialchars($res['suffix']);
    }
    return $name;
}

function getStatusBar($res) {
    if ($res['is_senior'] == 1) return ['text' => 'Senior Citizen', 'class' => 'badge-senior'];
    if ($res['is_disabled'] == 1) return ['text' => 'Person with Disability (PWD)', 'class' => 'badge-pwd'];
    if ($res['is_pregnant'] == 1 && $res['sex'] == 'Female') return ['text' => 'Pregnant Resident', 'class' => 'badge-pregnant'];
    return ['text' => 'General Resident', 'class' => 'badge-none'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resident Directory</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <div class="app-container">
        <div class="sidebar">
            <div class="logo">Happy Hallow<br />Barangay System</div>
            <nav class="main-nav">
                <ul>
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="residents.php" class="active">Manage Residents</a></li>
                    <li><a href="users.php">Manage Users</a></li>
                    <li><a href="documents.php">Documents</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <div class="topbar">
                <div class="topbar-right">
                    <span class="user-info">Welcome, <?php echo $logged_in_username; ?></span>
                    <a href="logout.php" class="btn logout-btn">Logout</a>
                </div>
            </div>
            
            <div class="page-content">
                <h2>**Resident Directory** (<?php echo count($residents); ?> Found)</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert-success">
                        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert-error">
                        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <form method="GET" action="residents.php" class="data-control-panel">
                    <div class="search-and-filter-wrapper">
                        <input
                            type="text"
                            placeholder="Search by name or ID..."
                            class="search-input"
                            name="search"
                            value="<?php echo htmlspecialchars($search_term); ?>"
                        />
                        <div class="filter-group">
                            <select class="filter-select" name="category">
                                <option value="">-- Select Category --</option>
                                <option value="senior" <?php echo ($category_filter == 'senior' ? 'selected' : ''); ?>>Senior Citizen</option>
                                <option value="pwd" <?php echo ($category_filter == 'pwd' ? 'selected' : ''); ?>>PWD</option>
                                <option value="pregnant" <?php echo ($category_filter == 'pregnant' ? 'selected' : ''); ?>>Pregnant</option>
                            </select>
                            <select class="filter-select" name="purok">
                                <option value="">-- Select Purok --</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($purok_filter == $i ? 'selected' : ''); ?>>Purok <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <button type="submit" class="btn primary-btn">Filter</button>
                            <a href="residents.php" class="btn">Reset</a>
                        </div>
                    </div>
                    <a href="addnewresidents.php" class="btn primary-btn add-btn">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </form>

                <div class="card data-table-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Sex</th>
                                    <th>Birthdate</th>
                                    <th>Civil Status</th>
                                    <th>Purok</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($residents)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">No residents found matching the criteria.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($residents as $res): 
                                        $full_name = getFullName($res);
                                        $status_bar = getStatusBar($res);
                                    ?>
                                        <tr class="resident-row" data-resident-id="<?php echo $res['person_id']; ?>">
                                            <td><?php echo $full_name; ?></td>
                                            <td><?php echo htmlspecialchars($res['sex']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($res['birthdate'])); ?></td>
                                            <td><?php echo htmlspecialchars($res['civil_status']); ?></td>
                                            <td>Purok <?php echo htmlspecialchars($res['purok']); ?></td>
                                            <td><?php echo htmlspecialchars($res['address']); ?></td>
                                            <td>
                                                <a href="edit_resident.php?id=<?php echo $res['person_id']; ?>" class="action-btn edit-btn" title="Edit Record">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_resident.php?id=<?php echo $res['person_id']; ?>" class="action-btn delete-btn" title="Delete Record" onclick="return confirm('Are you sure you want to delete this resident record (ID: <?php echo $res['person_id']; ?>)?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <tr class="detail-row" data-detail-id="<?php echo $res['person_id']; ?>">
                                            <td colspan="7">
                                                <div class="detail-container">
                                                    
                                                    <div class="special-status-bar <?php echo $status_bar['class']; ?>">
                                                        <?php echo $status_bar['text']; ?>
                                                    </div>

                                                    <div class="detail-tabs">
                                                        <span class="detail-tab active" data-tab="personal-<?php echo $res['person_id']; ?>">Personal Info</span>
                                                        <span class="detail-tab" data-tab="health-<?php echo $res['person_id']; ?>">Health & Status</span>
                                                        <span class="detail-tab" data-tab="education-<?php echo $res['person_id']; ?>">Education & Work</span>
                                                        <span class="detail-tab" data-tab="family-<?php echo $res['person_id']; ?>">Family</span>
                                                    </div>

                                                    <div class="tab-content active" id="personal-<?php echo $res['person_id']; ?>">
                                                        <div class="detail-grid">
                                                            <div class="detail-box">
                                                                <h4>IDENTIFICATION</h4>
                                                                <p><strong>Person ID:</strong> <?php echo $res['person_id']; ?></p>
                                                                <p><strong>Household ID:</strong> <?php echo htmlspecialchars($res['household_id'] ?? 'N/A'); ?></p>
                                                            </div>
                                                            <div class="detail-box">
                                                                <h4>LOCATION & BACKGROUND</h4>
                                                                <p><strong>Purok:</strong> <?php echo htmlspecialchars($res['purok']); ?></p>
                                                                <p><strong>Residency Start:</strong> <?php echo date('M d, Y', strtotime($res['residency_start_date'])); ?></p>
                                                            </div>
                                                            <div class="detail-box">
                                                                <h4>OTHER DETAILS</h4>
                                                                <p><strong>Religion:</strong> <?php echo htmlspecialchars($res['religion'] ?? 'N/A'); ?></p>
                                                                <p><strong>Nationality:</strong> <?php echo htmlspecialchars($res['nationality'] ?? 'N/A'); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="tab-content" id="health-<?php echo $res['person_id']; ?>">
                                                        <div class="detail-grid">
                                                            <div class="detail-box">
                                                                <h4>HEALTH STATUS</h4>
                                                                <p><strong>Health Insurance:</strong> <?php echo htmlspecialchars($res['health_insurance'] ?? 'N/A'); ?></p>
                                                                <p><strong>Vaccination:</strong> <?php echo htmlspecialchars($res['vaccination'] ?? 'N/A'); ?></p>
                                                            </div>
                                                            <div class="detail-box">
                                                                <h4>SPECIAL STATUS</h4>
                                                                <p><strong>Senior Citizen:</strong> <?php echo ($res['is_senior'] == 1 ? 'Yes' : 'No'); ?></p>
                                                                <p><strong>PWD Registered:</strong> <?php echo ($res['is_disabled'] == 1 ? 'Yes' : 'No'); ?></p>
                                                                <p><strong>Pregnant:</strong> <?php echo ($res['is_pregnant'] == 1 ? 'Yes' : 'No'); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="tab-content" id="education-<?php echo $res['person_id']; ?>">
                                                        <div class="detail-box">
                                                            <h4>EDUCATION & OCCUPATION</h4>
                                                            <p><strong>Highest Attainment:</strong> <?php echo htmlspecialchars($res['education_level'] ?? 'N/A'); ?></p>
                                                            <p><strong>Occupation:</strong> <?php echo htmlspecialchars($res['occupation'] ?? 'N/A'); ?></p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="tab-content" id="family-<?php echo $res['person_id']; ?>">
                                                        <div class="detail-box">
                                                            <h4>FAMILY DETAILS</h4>
                                                            <p><strong>Civil Status:</strong> <?php echo htmlspecialchars($res['civil_status']); ?></p>
                                                            <p><strong>No. of Children:</strong> <?php echo htmlspecialchars($res['children_count'] ?? 0); ?></p>
                                                            <p><strong>Household ID:</strong> <?php echo $res['household_id']; ?> (View Family Members)</p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            function collapseAllDetails() {
                document.querySelectorAll('.detail-row').forEach(otherDetail => {
                    otherDetail.classList.remove('expanded'); 
                });
                document.querySelectorAll('.resident-row').forEach(otherRow => {
                    otherRow.classList.remove('expanded');
                });
            }

            document.querySelectorAll(".resident-row").forEach((row) => {
                row.addEventListener("click", (e) => {
                    if (e.target.closest(".action-btn")) return;

                    const residentId = row.dataset.residentId;
                    const detailRow = document.querySelector(
                        `.detail-row[data-detail-id="${residentId}"]`
                    );

                    if (detailRow) {
                        const isExpanded = row.classList.contains("expanded");
                        collapseAllDetails();
                        
                        if (!isExpanded) {
                            row.classList.add("expanded");
                            
                            const nextDetailRow = row.nextElementSibling;
                            if (nextDetailRow && nextDetailRow.classList.contains('detail-row')) {
                                nextDetailRow.classList.add('expanded'); 
                            }
                            const firstTab = detailRow.querySelector(".detail-tab");
                            const allTabs = detailRow.querySelectorAll(".detail-tab");
                            const allContents = detailRow.querySelectorAll(".tab-content");
                            
                            allTabs.forEach((tab) => tab.classList.remove("active"));
                            allContents.forEach((content) => content.classList.remove("active"));
                            
                            if (firstTab) {
                                firstTab.classList.add("active");
                                const firstContentId = firstTab.dataset.tab;
                                document.getElementById(firstContentId).classList.add("active");
                            }
                        }
                    }
                });
            });

            document.querySelectorAll(".detail-tab").forEach((tab) => {
                tab.addEventListener("click", (e) => {
                    const parent = e.target.closest("td");
                    const tabName = e.target.dataset.tab;
                    if (parent) {
                        parent.querySelectorAll(".detail-tabs .detail-tab").forEach((t) => t.classList.remove("active"));
                        parent.querySelectorAll(".tab-content").forEach((c) => c.classList.remove("active"));
                        e.target.classList.add("active");
                        parent.querySelector(`#${tabName}`).classList.add("active");
                    }
                });
            });
        });
    </script>
</body>
</html>