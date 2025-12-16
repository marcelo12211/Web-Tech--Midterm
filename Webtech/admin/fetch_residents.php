<?php
session_start();
include '../db_connect.php'; 
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die('Session expired or user not logged in.');
}

$search_term = $_GET['search'] ?? '';
$purok_filter = $_GET['purok'] ?? '';
$category_filter = $_GET['category'] ?? '';
$sql = "SELECT * FROM residents WHERE 1";
$params = [];
$types = "";
if (!empty($search_term)) {
    $sql .= " AND (first_name LIKE ? OR surname LIKE ? OR person_id = ?)";
    $search_like = "%" . $search_term . "%";
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = is_numeric($search_term) ? (int)$search_term : 0; 
    $types .= "ssi"; 
}
if (!empty($purok_filter)) {
    $sql .= " AND purok = ?";
    $params[] = $purok_filter;
    $types .= "i";
}
if (!empty($category_filter)) {
    switch ($category_filter) {
        case 'senior':
            $sql .= " AND is_senior = 1";
            break;
        case 'pwd':
            $sql .= " AND is_disabled = 1";
            break;
        case 'pregnant':
            $sql .= " AND is_pregnant = 1 AND sex = 'Female'";
            break;
    }
}

$sql .= " ORDER BY surname ASC";
if (!isset($conn) || $conn === null) {
    http_response_code(500);
    die('Database connection is not available.');
}

$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    http_response_code(500);
    die('Database Query Error (Prepare): ' . mysqli_error($conn));
}

if (!empty($params)) {
    $bind_args = array_merge([$types], $params);
    $ref = [];
    foreach($bind_args as $key => $value) {
        $ref[$key] = &$bind_args[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $ref);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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

$output = '';

if (mysqli_num_rows($result) > 0) {
    while ($res = mysqli_fetch_assoc($result)) {
        $full_name = getFullName($res);
        $status_bar = getStatusBar($res);
        $resident_id = htmlspecialchars($res['person_id']);
        $age = date_diff(date_create($res['birthdate']), date_create('today'))->y;
        $output .= '<tr class="resident-row" data-resident-id="' . $resident_id . '">';
        $output .= '<td>' . $full_name . '</td>';
        $output .= '<td>' . htmlspecialchars($res['sex']) . '</td>';
        $output .= '<td>' . htmlspecialchars($res['birthdate']) . '</td>';
        $output .= '<td>' . htmlspecialchars($res['civil_status']) . '</td>';
        $output .= '<td>Purok ' . htmlspecialchars($res['purok']) . '</td>';
        $output .= '<td>' . htmlspecialchars($res['address']) . '</td>';
        $output .= '<td>';
        $output .= '<a href="editresident.php?id=' . $resident_id . '" class="action-btn" title="Edit"><i class="fas fa-edit"></i></a>';
        $output .= '<a href="deleteresident.php?id=' . $resident_id . '" class="action-btn delete-btn" title="Delete" onclick="return confirm(\'Are you sure you want to delete this resident?\');"><i class="fas fa-trash-alt"></i></a>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '<tr class="detail-row" data-detail-id="' . $resident_id . '">';
        $output .= '<td colspan="7">';
        $output .= '<div class="detail-container">';
        $output .= '<div class="detail-tabs">';
        $output .= '<div class="detail-tab" data-tab="basic-' . $resident_id . '">Basic Info</div>';
        $output .= '<div class="detail-tab" data-tab="health-' . $resident_id . '">Health & Status</div>';
        $output .= '</div>';
        $output .= '<div id="basic-' . $resident_id . '" class="tab-content">';
        $output .= '<div class="detail-grid">';
        $output .= '<div class="detail-box">';
        $output .= '<h4>Personal Information</h4>';
        $output .= '<p><strong>Resident ID:</strong> ' . $resident_id . '</p>';
        $output .= '<p><strong>Full Name:</strong> ' . $full_name . '</p>';
        $output .= '<p><strong>Birthdate:</strong> ' . htmlspecialchars($res['birthdate']) . '</p>';
        $output .= '<p><strong>Age:</strong> ' . $age . '</p>';
        $output .= '<p><strong>Sex:</strong> ' . htmlspecialchars($res['sex']) . '</p>';
        $output .= '</div>';
        $output .= '<div class="detail-box">';
        $output .= '<h4>Contact & Location</h4>';
        $output .= '<p><strong>Purok:</strong> Purok ' . htmlspecialchars($res['purok']) . '</p>';
        $output .= '<p><strong>Address:</strong> ' . htmlspecialchars($res['address']) . '</p>';
        $output .= '<p><strong>Residency Start:</strong> ' . htmlspecialchars($res['residency_start_date']) . '</p>';
        $output .= '</div>';
        $output .= '<div class="detail-box">';
        $output .= '<h4>Background</h4>';
        $output .= '<p><strong>Civil Status:</strong> ' . htmlspecialchars($res['civil_status']) . '</p>';
        $output .= '<p><strong>Nationality:</strong> ' . htmlspecialchars($res['nationality']) . '</p>';
        $output .= '<p><strong>Religion:</strong> ' . htmlspecialchars($res['religion']) . '</p>';
        $output .= '<p><strong>Education:</strong> ' . htmlspecialchars($res['education_level']) . '</p>';
        $output .= '<p><strong>Occupation:</strong> ' . htmlspecialchars($res['occupation']) . '</p>';
        $output .= '</div>';
        $output .= '</div>'; 
        $output .= '</div>'; 
        $output .= '<div id="health-' . $resident_id . '" class="tab-content">';
        $output .= '<div class="' . $status_bar['class'] . ' special-status-bar">' . $status_bar['text'] . '</div>';
        $output .= '<div class="detail-grid">';
        $output .= '<div class="detail-box">';
        $output .= '<h4>Special Status</h4>';
        $output .= '<p><strong>Senior Citizen:</strong> ' . ($res['is_senior'] == 1 ? 'Yes' : 'No') . '</p>';
        $output .= '<p><strong>PWD:</strong> ' . ($res['is_disabled'] == 1 ? 'Yes' : 'No') . '</p>';
        $output .= '<p><strong>Pregnant:</strong> ' . ($res['is_pregnant'] == 1 ? 'Yes' : 'No') . '</p>';
        $output .= '</div>';
        
        $output .= '<div class="detail-box">';
        $output .= '<h4>Health Records</h4>';
        $output .= '<p><strong>Health Insurance:</strong> ' . htmlspecialchars($res['health_insurance']) . '</p>';
        $output .= '<p><strong>Vaccination Status:</strong> ' . htmlspecialchars($res['vaccination']) . '</p>';
        $output .= '<p><strong>No. of Children:</strong> ' . htmlspecialchars($res['children_count']) . '</p>';
        $output .= '</div>';

        $output .= '</div>'; 
        $output .= '</div>'; 
        
        $output .= '</div>'; 
        $output .= '</td>';
        $output .= '</tr>';
    }
} else {
    $output = '<tr><td colspan="7" style="text-align: center;">No residents found matching the criteria.</td></tr>';
}

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

echo $output;
?>