<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_username = htmlspecialchars($_SESSION['user_name'] ?? 'Admin');

/* =========================
   DASHBOARD COUNTS
========================= */
function getCount($conn, $table) {
    $table = mysqli_real_escape_string($conn, $table);
    $sql = "SELECT COUNT(*) AS total FROM $table";
    $res = $conn->query($sql);
    return ($res && $row = $res->fetch_assoc()) ? (int)$row['total'] : 0;
}

$total_residents = getCount($conn, 'residents');
$total_documents = getCount($conn, 'documents');
$total_users     = getCount($conn, 'users');
$total_deaths    = getCount($conn, 'deaths');

/* =========================
   PIE CHART DATA
========================= */
$senior = getCount($conn, "residents WHERE is_senior = 1");
$pwd = getCount($conn, "residents WHERE is_disabled = 1");
$pregnant = getCount($conn, "residents WHERE is_pregnant = 1");

/* Regular Residents = Total - Special Categories */
$regular = $total_residents - ($senior + $pwd + $pregnant);
if ($regular < 0) $regular = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="css/style.css">
<style>
.charts-container {
    margin-top: 30px;
}
.chart-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.chart-wrapper {
    max-width: 450px;
    margin: 0 auto;
}
</style>
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
                    <div class="stat-value"><?php echo $total_residents; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">Total Records</div>
                    <div class="stat-value"><?php echo $total_documents; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">Active Barangay Staff</div>
                    <div class="stat-value"><?php echo $total_users; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">Death Records</div>
                    <div class="stat-value"><?php echo $total_deaths; ?></div>
                </div>
            </div>
            <div class="charts-container">
                <div class="chart-card">
                    <h3>Residents Distribution</h3>
                    <div class="chart-wrapper">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('categoriesChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: [
            'Senior Citizens',
            'PWD',
            'Pregnant',
            'Regular Residents'
        ],
        datasets: [{
            data: [
                <?php echo $senior; ?>,
                <?php echo $pwd; ?>,
                <?php echo $pregnant; ?>,
                <?php echo $regular; ?>
            ],
            backgroundColor: [
                '#76d7d2',
                '#ffd97d',
                '#ff8fa3',
                '#64b5f6'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
</body>
</html>
