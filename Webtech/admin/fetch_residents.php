<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die("Unauthorized access.");
}

$search_term = $_GET['search'] ?? '';
$purok_filter = $_GET['purok'] ?? '';
$category_filter = $_GET['category'] ?? '';
$where_clauses = [];
$bind_params = [];
$bind_types = '';
$error = ''; 
$residents = [];
function getFullName($res) {
    $name = htmlspecialchars($res['first_name']) . ' ';
    if (!empty($res['middle_name'])) {
        $name .= htmlspecialchars(substr($res['middle_name'], 0, 1)) . '. '; 
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
$sql = "SELECT person_id, household_id, first_name, middle_name, surname, suffix, sex, 
              birthdate, civil_status, nationality, religion, purok, address, 
              residency_start_date, education_level, occupation, 
              is_senior, is_disabled, health_insurance, vaccination, is_pregnant, 
              children_count
        FROM residents";

if (!empty($search_term)) {
    $search_like = "%" . $search_term . "%";
    $where_clauses[] = "(surname LIKE ? OR first_name LIKE ? OR person_id = ?)";
    $bind_types .= 'ssi';
    $bind_params[] = &$search_like;
    $bind_params[] = &$search_like;
    $temp_id = is_numeric($search_term) ? (int)$search_term : 0;
    $bind_params[] = &$temp_id; 
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
    echo "<tr><td colspan='7' class='alert-error' style='text-align: center;'>SQL Prepare Error: " . htmlspecialchars($conn->error) . "</td></tr>";
    $conn->close();
    exit();
}

if (!empty($bind_params)) {
    array_unshift($bind_params, $bind_types);
    $bind_references = [];
    foreach ($bind_params as $key => $value) {
        $bind_references[$key] = &$bind_params[$key];
    }
    call_user_func_array(array($stmt, 'bind_param'), $bind_references);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
        $residents[] = $row;
    }
    $stmt->close();
} else {
    echo "<tr><td colspan='7' class='alert-error' style='text-align: center;'>Error executing query: " . htmlspecialchars($stmt->error) . "</td></tr>";
    $conn->close();
    exit();
}

if (isset($conn) && $conn->ping()) {
    $conn->close();
}

if (empty($residents)): ?>
    <tr>
        <td colspan="7" style="text-align: center;">No residents found matching the criteria.</td>
    </tr>
<?php else: ?>
    <?php foreach ($residents as $res): 
        $full_name = getFullName($res);
        $status_bar = getStatusBar($res);
        $person_id = htmlspecialchars($res['person_id']); 
    ?>
        <tr class="resident-row" data-resident-id="<?php echo $person_id; ?>">
            <td><?php echo $full_name; ?></td>
            <td><?php echo htmlspecialchars($res['sex']); ?></td>
            <td><?php echo date('M d, Y', strtotime($res['birthdate'])); ?></td>
            <td><?php echo htmlspecialchars($res['civil_status']); ?></td>
            <td>Purok <?php echo htmlspecialchars($res['purok']); ?></td>
            <td><?php echo htmlspecialchars($res['address']); ?></td>
            <td>
                <a href="edit_resident.php?id=<?php echo $person_id; ?>" class="action-btn edit-btn" title="Edit Record">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="delete_resident.php?id=<?php echo $person_id; ?>" class="action-btn delete-btn" title="Delete Record" onclick="return confirm('Are you sure you want to delete this resident record (ID: <?php echo $person_id; ?>)?');">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>

        <tr class="detail-row" data-detail-id="<?php echo $person_id; ?>">
            <td colspan="7">
                <div class="detail-container">
                    
                    <div class="special-status-bar <?php echo $status_bar['class']; ?>">
                        <?php echo $status_bar['text']; ?>
                    </div>

                    <div class="detail-tabs">
                        <span class="detail-tab active" data-tab="personal-<?php echo $person_id; ?>">Personal Info</span>
                        <span class="detail-tab" data-tab="health-<?php echo $person_id; ?>">Health & Status</span>
                        <span class="detail-tab" data-tab="education-<?php echo $person_id; ?>">Education & Work</span>
                        <span class="detail-tab" data-tab="family-<?php echo $person_id; ?>">Family</span>
                    </div>

                    <div class="tab-content active" id="personal-<?php echo $person_id; ?>">
                        <div class="detail-grid">
                            <div class="detail-box">
                                <h4>IDENTIFICATION</h4>
                                <p><strong>Person ID:</strong> <?php echo $person_id; ?></p>
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

                    <div class="tab-content" id="health-<?php echo $person_id; ?>">
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
                    
                    <div class="tab-content" id="education-<?php echo $person_id; ?>">
                        <div class="detail-box">
                            <h4>EDUCATION & OCCUPATION</h4>
                            <p><strong>Highest Attainment:</strong> <?php echo htmlspecialchars($res['education_level'] ?? 'N/A'); ?></p>
                            <p><strong>Occupation:</strong> <?php echo htmlspecialchars($res['occupation'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    
                    <div class="tab-content" id="family-<?php echo $person_id; ?>">
                        <div class="detail-box">
                            <h4>FAMILY DETAILS</h4>
                            <p><strong>Civil Status:</strong> <?php echo htmlspecialchars($res['civil_status']); ?></p>
                            <p><strong>No. of Children:</strong> <?php echo htmlspecialchars($res['children_count'] ?? 0); ?></p>
                            <p><strong>Household ID:</strong> <a href="view_household.php?id=<?php echo $res['household_id']; ?>" title="View all members of this household"><?php echo $res['household_id']; ?> (View Family Members)</a></p>
                        </div>
                    </div>

                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>