<?php
session_start();
include '../db_connect.php'; 
function fetchUserData($conn) {
    $users = [];
    $search_term = $_GET['search'] ?? '';
    $role_filter = $_GET['role'] ?? '';
    $status_filter = $_GET['status'] ?? '';

    $where_clauses = [];
    $bind_params = [];
    $bind_types = '';
    $error = ''; 
    $sql = "SELECT user_id, fullname, email, role, status FROM users";
    if (!empty($search_term)) {
        $search_like = "%" . $search_term . "%";
        $where_clauses[] = "(fullname LIKE ? OR email LIKE ?)";
        $bind_types .= 'ss';
        $bind_params[] = &$search_like;
        $bind_params[] = &$search_like;
    }

    if (!empty($role_filter)) {
        $where_clauses[] = "role = ?";
        $bind_types .= 's'; 
        $bind_params[] = &$role_filter;
    }

    if (!empty($status_filter)) {
        $where_clauses[] = "status = ?";
        $bind_types .= 's';
        $bind_params[] = &$status_filter;
    }
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    $sql .= " ORDER BY fullname ASC";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $error = "SQL Prepare Error: " . $conn->error;
    } else {
        if (!empty($bind_params)) {
            $bind_references = array_merge([$bind_types], $bind_params);
            foreach ($bind_references as $key => &$value) {
            }
            $call_array = [$stmt, 'bind_param'];
            $call_array = array_merge($call_array, [$bind_types], $bind_params);
            $ref_params = [];
            $ref_params[] = $bind_types;
            foreach ($bind_params as $key => $value) {
                $ref_params[] = &$bind_params[$key];
            }
            call_user_func_array(array($stmt, 'bind_param'), $ref_params);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            $stmt->close();
        } else {
            $error = "Error executing query: " . $stmt->error;
        }
    }
    
    return ['users' => $users, 'error' => $error, 'count' => count($users), 'search' => $search_term, 'role' => $role_filter, 'status' => $status_filter];
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
function getRoleBadge($role) {
    switch(strtolower($role)) {
        case 'admin': return ['text' => 'Administrator', 'class' => 'badge-admin'];
        case 'staff': return ['text' => 'Staff Member', 'class' => 'badge-staff'];
        case 'clerk': return ['text' => 'Clerk', 'class' => 'badge-clerk'];
        default: return ['text' => ucfirst($role), 'class' => 'badge-none'];
    }
}

function getStatusBadge($status) {
    $status = strtolower($status);
    if ($status == 'active') {
        return ['text' => 'Active', 'class' => 'badge-active'];
    }
    if ($status == 'inactive') {
        return ['text' => 'Inactive', 'class' => 'badge-inactive'];
    }
    return ['text' => ucfirst($status), 'class' => 'badge-none']; 
}

function renderUserTable($data) {
    $users = $data['users'];
    $html = '
    <div class="card data-table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>User ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>';
    
    if (empty($users)) {
        $html .= '
            <tr>
                <td colspan="6" style="text-align: center;">No users found matching the criteria.</td>
            </tr>';
    } else {
        foreach ($users as $user) { 
            $role_badge = getRoleBadge($user['role']);
            $status_badge = getStatusBadge($user['status'] ?? 'inactive'); 
            $user_id = htmlspecialchars($user['user_id']);
            
            $html .= '
                <tr class="user-row" data-user-id="' . $user_id . '">
                    <td>' . htmlspecialchars($user['fullname']) . '</td>
                    <td>' . htmlspecialchars($user['email']) . '</td>
                    <td><span class="special-status-bar ' . $role_badge['class'] . '" style="margin-bottom: 0;">' . htmlspecialchars(ucfirst($user['role'])) . '</span></td>
                    <td>' . $user_id . '</td>
                    <td><span class="special-status-bar ' . $status_badge['class'] . '" style="margin-bottom: 0;">' . $status_badge['text'] . '</span></td>
                    <td>
                        <div class="action-icon-group">
                            <a href="edit_user.php?id=' . $user_id . '" class="action-btn edit-btn" title="Edit User">
                                <i class="fas fa-edit"></i> </a>
                            <a href="delete_user.php?id=' . $user_id . '" class="action-btn delete-btn" title="Delete User" onclick="return confirm(\'Are you sure you want to delete this user (ID: ' . $user_id . ')?\');">
                                <i class="fas fa-trash"></i> </a>
                        </div>
                    </td>
                </tr>
                <tr class="detail-row" data-detail-id="' . $user_id . '">
                    <td colspan="6">
                        <div class="detail-container">
                            <div class="special-status-bar ' . $role_badge['class'] . '">
                                Role: ' . $role_badge['text'] . '
                            </div>
                            <div class="detail-tabs">
                                <span class="detail-tab active" data-tab="account-' . $user_id . '">Account Info</span>
                                <span class="detail-tab" data-tab="permissions-' . $user_id . '">Role & Permissions</span>
                            </div>
                            <div class="tab-content active" id="account-' . $user_id . '">
                                <div class="detail-grid">
                                    <div class="detail-box">
                                        <h4>IDENTIFICATION</h4>
                                        <p><strong>User ID:</strong> ' . $user_id . '</p>
                                        <p><strong>Full Name:</strong> ' . htmlspecialchars($user['fullname']) . '</p>
                                        <p><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</p>
                                    </div>
                                    <div class="detail-box">
                                        <h4>STATUS & DATE</h4>
                                        <p><strong>Current Status:</strong> <span class="special-status-bar ' . $status_badge['class'] . '" style="padding: 3px 8px; margin: 0;">' . $status_badge['text'] . '</span></p>
                                        <p><strong>Account Created:</strong> N/A (Placeholder)</p> 
                                    </div>
                                </div>
                            </div>
                            <div class="tab-content" id="permissions-' . $user_id . '">
                                <div class="detail-box">
                                    <h4>USER ROLE & ACCESS LEVEL</h4>
                                    <p><strong>Role:</strong> <span class="special-status-bar ' . $role_badge['class'] . '" style="padding: 3px 8px; margin: 0;">' . $role_badge['text'] . '</span></p>';
            
            $role_info = [
                'admin' => 'Full control over all system data, including residents, users, and documents.',
                'staff' => 'Can manage (add/edit/view) residents and documents, but has limited user management access.',
                'clerk' => 'Primarily for data entry and document retrieval. View and limited edit access to resident data.',
                'default' => 'Limited or no specific permissions defined.'
            ];
            $role_key = strtolower($user['role']);
            
            $html .= '
                                    <p><strong>Permissions:</strong></p>
                                    <p><em>' . ($role_info[$role_key] ?? $role_info['default']) . '</em></p>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>';
        }
    }
    
    $html .= '
                </tbody>
            </table>
        </div>
    </div>';
    
    return $html;
}

if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $data = fetchUserData($conn);
    if (isset($conn) && $conn->ping()) {
        $conn->close();
    }
    
    $response = [
        'html' => renderUserTable($data),
        'count' => $data['count'],
        'error' => $data['error']
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$data = fetchUserData($conn);
$users = $data['users'];
$error = $data['error'];
$search_term = $data['search'];
$role_filter = $data['role'];
$status_filter = $data['status'];

if (isset($conn) && $conn->ping()) {
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Management</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <style>

:root {
    --primary-color: #226b8dff;
    --primary-dark: #226b8dff;
    --secondary-color: #5f6368;
    --warning-color: #fbbc04;
    --danger-color: #ea4335;
    --background-color: #f8f9fa;
    --card-background: #ffffff;
    --sidebar-bg: #212121;
    --text-color: #202124;
    --text-light: #5f6368;
    --border-color: #dadce0;
    --radius: 10px;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: "Roboto", Arial, sans-serif;
    background: var(--background-color);
    color: var(--text-color);
}
a { text-decoration: none; }
.app-container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background: var(--sidebar-bg);
    color: white;
}
.logo {
    padding: 25px;
    text-align: center;
    font-weight: 700;
    font-size: 1.15rem;
    line-height: 1.3;
}
.main-nav ul { list-style: none; padding: 0; margin: 0; }
.main-nav a {
    display: block;
    padding: 14px 20px;
    color: #bdc1c6;
}
.main-nav a:hover,
.main-nav a.active {
    background: var(--primary-dark);
    color: white;
}

.main-content { flex: 1; }
.topbar {
    background: white;
    padding: 15px 30px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
.topbar-right { display: flex; align-items: center; }
.user-info { margin-right: 15px; color: var(--text-light); }
.logout-btn {
    padding: 8px 15px;
    border: 1px solid var(--border-color);
    background: transparent;
    color: var(--text-color);
    font-size: 0.9rem;
    cursor: pointer;
    border-radius: 6px;
    transition: background-color 0.2s;
    font-weight: 500;
}
.logout-btn:hover { background: var(--background-color); }

.page-content { padding: 30px; }

.card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 25px;
    margin-bottom: 30px;
}

.btn {
    padding: 10px 18px;
    border-radius: 6px;
    font-weight: 500;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
}
.primary-btn {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}
.primary-btn:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
}

table {
    width: 100%;
    border-collapse: collapse;
}
thead {
    background: #eef2f5;
    color: var(--text-light);
    font-weight: 600;
    font-size: 0.9rem;
    text-align: left;
}
th, td {
    padding: 14px;
    border-bottom: 1px solid var(--border-color);
}

.user-row {
    cursor: pointer;
    transition: background-color 0.2s;
}

.user-row.expanded {
    background-color: #eef2f5;
    border-bottom: none; 
}
.user-row:hover:not(.expanded) {
    background: #f0f0f0; 
}

.data-control-panel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.search-and-filter-wrapper {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap; 
}
.search-input {
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    width: 250px;
    font-size: 1rem;
    transition: border-color 0.2s;
}
.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 1px var(--primary-color);
}
.filter-group {
    display: flex;
    gap: 10px;
}
.filter-select {
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 1rem;
    background-color: white;
    cursor: pointer;
}
.add-btn {
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-row {
    display: none; 
}
.detail-row.expanded {
    display: table-row;
    background: #fcfcfc; 
}
.detail-row td {
    padding: 0 !important; 
    border-top: 1px solid #e0e0e0;
}
.detail-container {
    padding: 20px 15px;
}

.detail-tabs {
    border-bottom: 2px solid var(--border-color);
    margin-bottom: 15px;
    display: flex;
}
.detail-tab {
    padding: 10px 15px;
    cursor: pointer;
    font-weight: 500;
    color: var(--text-light);
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    margin-bottom: -2px; 
}
.detail-tab:hover {
    color: var(--primary-color);
}
.detail-tab.active {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
}

.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
    padding-top: 10px;
}
.detail-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.detail-box {
    flex: 1 1 300px;
    padding: 10px;
    border-left: 3px solid #ccc;
}
.detail-box h4 {
    margin: 0 0 10px 0;
    font-size: 0.9rem;
    color: var(--secondary-color);
    text-transform: uppercase;
    font-weight: 700;
}
.detail-box p {
    margin: 5px 0;
    font-size: 0.95rem;
}
.detail-box strong {
    color: var(--text-color);
    font-weight: 600;
    display: inline-block;
    width: 150px; 
}

.special-status-bar {
    padding: 8px 15px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.85rem;
    margin-bottom: 15px;
    display: inline-block;
}
.badge-admin {
    background-color: #fce4e4;
    color: var(--danger-color);
}
.badge-staff {
    background-color: #fff4e5;
    color: #cc9900;
}
.badge-clerk {
    background-color: #e6f7ff;
    color: #1890ff;
}
.badge-active { 
    background-color: #e6f4ea;
    color: var(--primary-dark);
}
.badge-inactive {
    background-color: #f0f0f0;
    color: var(--text-light);
}
.badge-none {
    background-color: #f0f0f0;
    color: var(--text-light);
}

.action-btn {
    color: var(--secondary-color);
    font-size: 1rem;
    margin: 0 5px;
    transition: color 0.2s;
}
.action-btn:hover {
    color: var(--primary-color);
}
.delete-btn:hover {
    color: var(--danger-color);
}

.alert-success, .alert-error {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
}
.alert-success {
    background-color: #e6f4ea;
    color: var(--primary-dark);
    border: 1px solid var(--primary-dark);
}
.alert-error {
    background-color: #fce4e4;
    color: var(--danger-color);
    border: 1px solid var(--danger-color);
}

@media (max-width: 900px) {
    .search-and-filter-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }
    .search-input, .filter-select {
        width: 100%;
    }
    .filter-group {
        width: 100%;
        justify-content: space-between;
    }
    .data-control-panel {
        flex-direction: column;
        align-items: flex-start;
    }
    .add-btn {
        width: 100%;
        margin-top: 15px;
        justify-content: center;
    }
    .detail-grid {
        flex-direction: column;
    }
    .detail-box {
        border-left: none;
        border-bottom: 1px solid #eee;
    }
}
    </style>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="logo">Happy Hallow<br />Barangay System</div>
        <nav class="main-nav">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="residents.php">Manage Residents</a></li>
                <li><a href="users.php" class="active">Manage Users</a></li>
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
            <h2 id="user-count-header">**User Directory** (<?php echo $data['count']; ?> Found)</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert-error" id="server-error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert-success"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert-error"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <form id="filter-form" method="GET" action="users.php" class="data-control-panel" onsubmit="event.preventDefault(); return false;">
                <div class="search-and-filter-wrapper">
                    <input
                        type="text"
                        placeholder="Search by name or email..."
                        class="search-input"
                        name="search"
                        id="search-input"
                        value="<?php echo htmlspecialchars($search_term); ?>"
                    />
                    <div class="filter-group">
                        <select class="filter-select" name="role" id="role-filter">
                            <option value="">-- Select Role --</option>
                            <option value="admin" <?php echo $role_filter == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="staff" <?php echo $role_filter == 'staff' ? 'selected' : ''; ?>>Staff</option>
                            <option value="clerk" <?php echo $role_filter == 'clerk' ? 'selected' : ''; ?>>Clerk</option>
                        </select>
                        <select class="filter-select" name="status" id="status-filter">
                            <option value="">-- All Status --</option>
                            <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <button type="submit" class="btn primary-btn" style="display: none;">Filter</button> 
                        <a href="users.php" class="btn">Reset</a>
                    </div>
                </div>
                <a href="add_user.php" class="btn primary-btn add-btn">
                    <i class="fas fa-plus"></i> Add New User
                </a>
            </form>            
            <div id="user-table-container">
                <?php echo renderUserTable($data); ?>
            </div>
            </div>
    </div>
</div>
<script>
function setupDetailRowHandlers() {
    document.querySelectorAll(".user-row").forEach((row) => {
        row.removeEventListener("click", toggleDetailView); 
        row.addEventListener("click", toggleDetailView);
    });
    document.querySelectorAll(".detail-tab").forEach((tab) => {
        tab.removeEventListener("click", switchDetailTab);
        tab.addEventListener("click", switchDetailTab);
    });
}
function collapseAllDetails() {
    document.querySelectorAll('.detail-row').forEach(otherDetail => {
        otherDetail.classList.remove('expanded'); 
    });
    document.querySelectorAll('.user-row').forEach(otherRow => {
        otherRow.classList.remove('expanded');
    });
}
function toggleDetailView(e) {
    if (e.target.closest(".action-btn, a")) {
        return;
    }
    const row = e.currentTarget;
    const userId = row.dataset.userId;
    const detailRow = document.querySelector(
        `.detail-row[data-detail-id="${userId}"]`
    );
    if (detailRow) {
        const isExpanded = row.classList.contains("expanded");
        collapseAllDetails();
        if (!isExpanded) {
            row.classList.add("expanded");
            detailRow.classList.add('expanded');
            const allTabs = detailRow.querySelectorAll(".detail-tab");
            const allContents = detailRow.querySelectorAll(".tab-content");
            const firstTab = detailRow.querySelector(".detail-tab");
            
            allTabs.forEach((tab) => tab.classList.remove("active"));
            allContents.forEach((content) => content.classList.remove("active"));
            
            if (firstTab) {
                firstTab.classList.add("active");
                const firstContentId = firstTab.dataset.tab;
                document.getElementById(firstContentId).classList.add("active");
            }
        }
    }
}

function switchDetailTab(e) {
    const detailContainer = e.target.closest(".detail-container");
    const tabName = e.target.dataset.tab;
    if (detailContainer) {
        detailContainer.querySelectorAll(".detail-tabs .detail-tab").forEach((t) => t.classList.remove("active"));
        detailContainer.querySelectorAll(".tab-content").forEach((c) => c.classList.remove("active"));
        e.target.classList.add("active");
        detailContainer.querySelector(`#${tabName}`).classList.add("active");
    }
}

function fetchUsers(search, role, status) {
    const container = document.getElementById('user-table-container');
    const header = document.getElementById('user-count-header');
    const errorAlert = document.getElementById('server-error-message');
    container.style.opacity = '0.5'; 

    const params = new URLSearchParams({
        ajax: 'true',
        search: search,
        role: role,
        status: status
    }).toString();
    fetch(`users.php?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            container.innerHTML = data.html;
            header.innerHTML = `**User Directory** (${data.count} Found)`;
    
            if (data.error) {
                 let newErrorAlert = document.getElementById('ajax-error-alert');
                 if (!newErrorAlert) {
                     newErrorAlert = document.createElement('div');
                     newErrorAlert.id = 'ajax-error-alert';
                     newErrorAlert.classList.add('alert-error');
                     container.parentNode.insertBefore(newErrorAlert, container);
                 }
                 newErrorAlert.innerHTML = `**AJAX Error:** ${data.error}`;
                 newErrorAlert.style.display = 'block';
            } else if (errorAlert) {
                 errorAlert.style.display = 'none';
            }
            setupDetailRowHandlers(); 
            
            container.style.opacity = '1'; 
        })
        .catch(error => {
            console.error('Fetch error:', error);
            let newErrorAlert = document.getElementById('ajax-error-alert');
            if (!newErrorAlert) {
                newErrorAlert = document.createElement('div');
                newErrorAlert.id = 'ajax-error-alert';
                newErrorAlert.classList.add('alert-error');
                container.parentNode.insertBefore(newErrorAlert, container);
            }
            newErrorAlert.innerHTML = `**AJAX Failed:** Could not retrieve data. Please check network connection.`;
            newErrorAlert.style.display = 'block';

            container.style.opacity = '1';
        });
}

document.addEventListener("DOMContentLoaded", () => {
    setupDetailRowHandlers(); 

    const searchInput = document.getElementById('search-input');
    const roleFilter = document.getElementById('role-filter');
    const statusFilter = document.getElementById('status-filter');
    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            fetchUsers(searchInput.value, roleFilter.value, statusFilter.value);
        }, 300); 
    });
    roleFilter.addEventListener('change', () => {
        fetchUsers(searchInput.value, roleFilter.value, statusFilter.value);
    });

    statusFilter.addEventListener('change', () => {
        fetchUsers(searchInput.value, roleFilter.value, statusFilter.value);
    });
});
</script>
</body>
</html>