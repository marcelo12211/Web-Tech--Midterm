<?php
session_start();
include '../db_connect.php'; 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_username = htmlspecialchars($_SESSION['user_name'] ?? 'Guest');
function getCount($conn, $table) {
    $table = mysqli_real_escape_string($conn, $table); 
    $sql = "SELECT COUNT(*) AS total FROM $table";
    $res = $conn->query($sql);
    return ($res && $row = $res->fetch_assoc()) ? (int)$row['total'] : 0;
}
$total_residents = getCount($conn, 'residents');
$senior = getCount($conn, "residents WHERE is_senior = 1");
$pwd = getCount($conn, "residents WHERE is_disabled = 1");
$pregnant = getCount($conn, "residents WHERE is_pregnant = 1");
$total_children = 5; 
$regular = $total_residents - ($senior + $pwd + $pregnant);
if ($regular < 0) $regular = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Happy Hallow Barangay System - Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: "Roboto", Arial, sans-serif;
  background: var(--background-color);
  color: var(--text-color);
}

a {
  text-decoration: none;
}

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

.main-nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.main-nav li {
    margin: 0;
}

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

.main-content {
  flex: 1;
}

.topbar {
  background: white;
  padding: 15px 30px;
  border-bottom: 1px solid var(--border-color);
  display: flex; 
  justify-content: flex-end;
  align-items: center;
}

.topbar-right {
    display: flex;
    align-items: center;
}

.user-info {
    margin-right: 15px;
    color: var(--text-light);
}

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

.logout-btn:hover {
    background: var(--background-color);
}

.page-content {
  padding: 30px;
}

.page-content h2 {
    margin-top: 0;
    margin-bottom: 30px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color); 
    width: fit-content; 
    font-size: 1.5rem;
    font-weight: 500;
}

.card {
  background: white;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 25px;
  margin-bottom: 30px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 100px;
    margin-bottom: 0 !important;
    border-left: 5px solid var(--border-color);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); 
}

.stat-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
}

.stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--text-color);
    line-height: 1; 
}

.dashboard-grid .stat-card:nth-child(1) {
    border-left-color: var(--primary-color); 
}
.dashboard-grid .stat-card:nth-child(2) {
    border-left-color: var(--warning-color); 
}
.dashboard-grid .stat-card:nth-child(3) {
    border-left-color: var(--danger-color); 
}
.dashboard-grid .stat-card:nth-child(4) {
    border-left-color: #64b5f6; 
}
.dashboard-grid .stat-card:nth-child(5) {
    border-left-color: #26c6da;
}

.charts-container {
    margin-top: 30px;
}

.chart-card {
    padding: 25px;
    margin-bottom: 0;
}

.chart-card h3 {
    margin-top: 0;
    font-weight: 500;
    color: var(--text-color);
}

.chart-wrapper {
    max-width: 450px;
    margin: 0 auto;
    padding-top: 20px;
}
.template-card .btn {
  background-color: #1a73e8; 
  border-color: #1a73e8;
  color: #ffffff;
}

.template-card .btn:hover {
  background-color: #1558c0;
  border-color: #1558c0;
  box-shadow: 0 4px 10px rgba(26, 115, 232, 0.4);
}
@media (max-width: 768px) {
  .sidebar {
    display: none;
  }
  .page-content {
    padding: 15px;
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
            <h2>Dashboard Overview</h2>
            
            <div class="dashboard-grid">
                <div class="card stat-card">
                    <div class="stat-label">TOTAL RESIDENTS</div>
                    <div class="stat-value"><?php echo $total_residents; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">SENIOR CITIZEN</div>
                    <div class="stat-value"><?php echo $senior; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">PWD</div>
                    <div class="stat-value"><?php echo $pwd; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">PREGNANT RESIDENTS</div>
                    <div class="stat-value"><?php echo $pregnant; ?></div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">TOTAL CHILDREN</div>
                    <div class="stat-value"><?php echo $total_children; ?></div>
                </div>
            </div>
            
            <div class="charts-container">
                <div class="card chart-card">
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
                position: 'bottom',
                labels: {
                    padding: 20
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed !== null) {
                            label += context.parsed;
                        }
                        return label;
                    }
                }
            }
        },
        maintainAspectRatio: false,
        aspectRatio: 1
    }
});
</script>
</body>
</html>