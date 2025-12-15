<?php
session_start();
include '../db_connect.php'; 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$users = [];
$search_term = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';
$where_clauses = [];
$bind_params = [];
$bind_types = '';
$error = ''; 

$sql = "SELECT user_id, fullname, email, password, role
        FROM users";

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
        array_unshift($bind_params, $bind_types);
        call_user_func_array(array($stmt, 'bind_param'), $bind_params);
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

if ($conn && $conn->ping()) {
    $conn->close();
}

function getRoleBadge($role) {
    switch(strtolower($role)) {
        case 'admin':
            return ['text' => 'Administrator', 'class' => 'badge-admin'];
        case 'staff':
            return ['text' => 'Staff Member', 'class' => 'badge-staff'];
        case 'clerk':
            return ['text' => 'Clerk', 'class' => 'badge-clerk'];
        default:
            return ['text' => ucfirst($role), 'class' => 'badge-default'];
    }
}

function getStatusBadge($role) {
    // Since there's no status column, we'll use role for status display
    switch(strtolower($role)) {
        case 'admin':
            return ['text' => 'Active', 'class' => 'status-active'];
        case 'staff':
            return ['text' => 'Active', 'class' => 'status-active'];
        default:
            return ['text' => 'Active', 'class' => 'status-active'];
    }
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
    <link rel="stylesheet" href="css/style.css" />
    <style>
        .search-and-filter-wrapper {
            display: flex;
            gap: 20px;
            width: 100%;
            margin-bottom: 20px;
            align-items: flex-end;
        }
        .search-input {
            flex-grow: 1;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
        }
        .filter-group {
            display: flex;
            gap: 10px;
        }
        .filter-select {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
        }
        .data-control-panel {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 20px;
        }
        .add-btn {
            white-space: nowrap;
        }
        .user-row {
            cursor: pointer;
        }
        .detail-row {
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }
        .detail-row.collapsed-details {
            max-height: 0 !important;
            opacity: 0 !important;
        }
        .user-row.expanded + .detail-row {
            max-height: 500px;
            opacity: 1;
        }
        .detail-container {
            padding: 20px;
            background-color: var(--secondary-bg);
            border-top: 1px solid var(--border-color);
        }
        .detail-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .detail-tab {
            cursor: pointer;
            padding: 8px 15px;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: var(--text-color);
        }
        .detail-tab.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .role-badge-bar {
            padding: 10px 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: bold;
            color: white;
            text-align: center;
        }
        .badge-admin { background-color: #e74c3c; }
        .badge-staff { background-color: #3498db; } 
        .badge-clerk { background-color: #9b59b6; }
        .badge-default { background-color: var(--text-color-light); color: var(--text-color-dark); }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            display: inline-block;
        }
        .status-active { background-color: #27ae60; color: white; }
        .status-inactive { background-color: #95a5a6; color: white; }
        .status-suspended { background-color: #e67e22; color: white; }
        .status-default { background-color: var(--text-color-light); color: var(--text-color-dark); }

        @media (max-width: 992px) {
            .data-control-panel {
                flex-direction: column;
                align-items: stretch;
            }
            .search-and-filter-wrapper {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-group {
                flex-direction: column;
                gap: 15px;
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
                <h2>User Management (<?php echo count($users); ?> Found)</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert-error" style="background-color: var(--danger-color); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" style="background-color: #27ae60; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error" style="background-color: var(--danger-color); color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
                
                <form method="GET" action="users.php" class="data-control-panel">
                    <div class="search-and-filter-wrapper">
                        <input
                            type="text"
                            placeholder="Search by username or email..."
                            class="search-input"
                            name="search"
                            value="<?php echo htmlspecialchars($search_term); ?>"
                        />
                        <div class="filter-group">
                            <select class="filter-select role-filter" name="role">
                                <option value="">-- Select Role --</option>
                                <option value="admin" <?php echo ($role_filter == 'admin' ? 'selected' : ''); ?>>Administrator</option>
                                <option value="staff" <?php echo ($role_filter == 'staff' ? 'selected' : ''); ?>>Staff</option>
                                <option value="clerk" <?php echo ($role_filter == 'clerk' ? 'selected' : ''); ?>>Clerk</option>
                            </select>
                            <select class="filter-select status-filter" name="status">
                                <option value="">-- All Users --</option>
                                <option value="active" <?php echo ($status_filter == 'active' ? 'selected' : ''); ?>>All Active</option>
                            </select>
                            <button type="submit" class="btn primary-btn" style="padding: 10px 15px;">Filter</button>
                            <a href="users.php" class="btn">Reset</a>
                        </div>
                    </div>
                    <a href="add_user.php" class="btn primary-btn add-btn">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                </form>
                
                <div class="card data-table-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>User ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No users found matching the criteria.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): 
                                        $role_badge = getRoleBadge($user['role']);
                                        $status_badge = getStatusBadge($user['role']);
                                    ?>
                                        <tr class="user-row" data-user-id="<?php echo $user['user_id']; ?>">
                                            <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                            <td>
                                                <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="action-btn edit-btn" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" class="action-btn delete-btn" title="Delete User" onclick="return confirm('Are you sure you want to delete this user?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr class="detail-row collapsed-details" data-detail-id="<?php echo $user['user_id']; ?>">
                                            <td colspan="5">
                                                <div class="detail-container">
                                                    <div class="role-badge-bar <?php echo $role_badge['class']; ?>">
                                                        <?php echo $role_badge['text']; ?>
                                                    </div>
                                                    <div class="detail-tabs">
                                                         <span class="detail-tab active" data-tab="account-<?php echo $user['user_id']; ?>">Account Info</span>
                                                         <span class="detail-tab" data-tab="contact-<?php echo $user['user_id']; ?>">Role & Permissions</span>
                                                    </div>
                                                    
                                                    <div class="tab-content active" id="account-<?php echo $user['user_id']; ?>">
                                                        <div class="detail-grid">
                                                            <div class="detail-box">
                                                                <h4>ACCOUNT DETAILS</h4>
                                                                <p><strong>User ID:</strong> <?php echo $user['user_id']; ?></p>
                                                                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
                                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                                            </div>
                                                            <div class="detail-box">
                                                                <h4>ROLE & STATUS</h4>
                                                                <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($user['role'])); ?></p>
                                                                <p><strong>Status:</strong> <span class="status-badge <?php echo $status_badge['class']; ?>"><?php echo $status_badge['text']; ?></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="tab-content" id="contact-<?php echo $user['user_id']; ?>">
                                                        <div class="detail-box">
                                                            <h4>PERMISSIONS & ACCESS</h4>
                                                            <p><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($user['role'])); ?></p>
                                                            <p><strong>User ID:</strong> <?php echo $user['user_id']; ?></p>
                                                            <?php if ($user['role'] == 'admin'): ?>
                                                                <p><strong>Access Level:</strong> Full System Access</p>
                                                                <p><em>Can manage residents, users, and documents</em></p>
                                                            <?php else: ?>
                                                                <p><strong>Access Level:</strong> Limited Access</p>
                                                                <p><em>Can view and manage residents and documents</em></p>
                                                            <?php endif; ?>
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
            const detailRows = document.querySelectorAll(".detail-row");
            detailRows.forEach(row => row.classList.add('collapsed-details'));
            
            function collapseAllDetails() {
                document.querySelectorAll('.detail-row').forEach(otherDetail => {
                    otherDetail.classList.add('collapsed-details');
                });
                document.querySelectorAll('.user-row').forEach(otherRow => {
                    otherRow.classList.remove('expanded');
                });
            }
            
            document.querySelectorAll(".user-row").forEach((row) => {
                row.addEventListener("click", (e) => {
                    if (e.target.closest(".action-btn")) return;

                    const userId = row.dataset.userId;
                    const detailRow = document.querySelector(
                        `.detail-row[data-detail-id="${userId}"]`
                    );
                    
                    if (detailRow) {
                        const isExpanded = row.classList.contains("expanded");
                        collapseAllDetails();
                        
                        if (!isExpanded) {
                            detailRow.classList.remove("collapsed-details");
                            row.classList.add("expanded");
                            
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
                    const parent = e.target.closest(".detail-row");
                    const tabName = e.target.dataset.tab;
                    
                    if (parent) {
                        parent.querySelectorAll(".detail-tab").forEach((t) => t.classList.remove("active"));
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