<?php
session_start();                      
include '../db_connect.php'; 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$error = '';                    
function getCount($conn, $table) {
    $safe_table = mysqli_real_escape_string($conn, $table); 
    $sql = "SELECT COUNT(*) AS count FROM $safe_table";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return number_format($row['count']);
    }
    return 0;
}
$total_residents = getCount($conn, 'residents');
$total_documents = getCount($conn, 'documents');
$total_users = getCount($conn, 'users'); 
$total_deaths = getCount($conn, 'deaths');
$activity_logs = [];
$activity_logs = [];
$log_result = $conn->query("SELECT * FROM user_log ORDER BY log_id DESC LIMIT 5");


// HARDCODED ITO ISSUE
if ($log_result && $log_result->num_rows > 0) {
    while($row = $log_result->fetch_assoc()) {
        $activity_logs[] = $row;
    }
} else {
    $activity_logs = [
        ['log_time' => 'N/A', 'user_name' => 'System', 'action' => 'No Data', 'details' => 'Please implement logging in login.php, residents.php, etc.'],
        ['log_time' => '2025-12-11 10:00 PM', 'user_name' => 'Admin', 'action' => 'Login', 'details' => 'Successful login to the system.'], 
        ['log_time' => '2025-12-11 09:30 PM', 'user_name' => 'Staff User 1', 'action' => 'Create', 'details' => 'Added new resident: **Dela Cruz, Juan** (Purok 1)'],
        ['log_time' => '2025-12-11 09:15 PM', 'user_name' => 'Admin', 'action' => 'Delete', 'details' => 'Removed temporary user account: **TempUser01**'],
        ['log_time' => '2025-12-11 08:00 PM', 'user_name' => 'Staff User 2', 'action' => 'Update', 'details' => 'Updated contact number for **Santos, Maria**.'],
    ];
}
function getActionTag($action) {
    switch (strtolower($action)) {
        case 'create':
        case 'add':
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
<link rel="stylesheet" href="css/style.css" />
<script>
window.onload = function () {
    if (window.history.length > 0) {
        window.history.forward();
    }
};
document.addEventListener("DOMContentLoaded", function () {
    window.history.pushState(null, document.title, window.location.href);
    window.addEventListener("popstate", function () {
        window.history.pushState(null, document.title, window.location.href);
    });
});
</script>

</head>

<body>
<div class="app-container">

    <div class="sidebar">
        <div class="logo">Happy Hallow<br>Barangay System</div>
        <nav class="main-nav">
            <ul>
                <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="residents.php">Manage Residents</a></li>
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
            <h2>Dashboard Overview</h2>

            <div class="dashboard-grid">
                
                <div class="card stat-card">
                    <div class="stat-label">Registered Residents</div>
                    <div class="stat-value">
                        <span><?php echo $total_residents; ?></span>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-label">Total Records (Certificates/Etc)</div>
                    <div class="stat-value">
                        <span><?php echo $total_documents; ?></span>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-label">Active Barangay Staff</div>
                    <div class="stat-value">
                        <span><?php echo $total_users; ?></span>
                    </div>
                </div>

                <div class="card stat-card">
                    <div class="stat-label">Total Death Records</div>
                    <div class="stat-value">
                        <span><?php echo $total_deaths; ?></span>
                    </div>
                </div>
                
                </div>
            
            <div class="card activity-log-card">
                <h3>Recent Activity Logs</h3>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($activity_logs as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['log_time'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($log['encoded_by'] ?? $log['user_name'] ?? 'Unknown'); ?></td>
                                <td>
                                    <span class="category-tag <?php echo getActionTag($log['action'] ?? $log['remarks'] ?? 'Info'); ?>">
                                        <?php echo htmlspecialchars($log['action'] ?? $log['remarks'] ?? 'Log'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($log['details'] ?? 'No detail provided.'); ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($activity_logs) && $log_result->num_rows == 0): ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No recent activity found in the log table.</td>
                            </tr>
                        <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>
</body>
</html>