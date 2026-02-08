<?php
session_start();
include '../db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$total_res_query = "SELECT COUNT(*) as total FROM residents";
$total_res_result = mysqli_query($conn, $total_res_query);
$total_residents = mysqli_fetch_assoc($total_res_result)['total'] ?? 0;
$senior_query = "SELECT COUNT(*) as total FROM residents WHERE is_senior = 1";
$senior_result = mysqli_query($conn, $senior_query);
$total_seniors = mysqli_fetch_assoc($senior_result)['total'] ?? 0;
$pwd_query = "SELECT COUNT(*) as total FROM residents WHERE is_disabled = 1";
$pwd_result = mysqli_query($conn, $pwd_query);
$total_pwd = mysqli_fetch_assoc($pwd_result)['total'] ?? 0;
$health_check = mysqli_query($conn, "SHOW TABLES LIKE 'health_tracking'");
$health_cases = 0;
if ($health_check && mysqli_num_rows($health_check) > 0) {
    $health_query = "SELECT COUNT(*) as total FROM health_tracking";
    $health_result = mysqli_query($conn, $health_query);
    $health_cases = mysqli_fetch_assoc($health_result)['total'] ?? 0;
}
$age_sql = "SELECT 
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 0 AND 12 THEN 1 ELSE 0 END) as kids,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 13 AND 19 THEN 1 ELSE 0 END) as teens,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) BETWEEN 20 AND 59 THEN 1 ELSE 0 END) as adults,
    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) >= 60 THEN 1 ELSE 0 END) as seniors
    FROM residents";
$age_res = mysqli_query($conn, $age_sql);
$age_data = mysqli_fetch_assoc($age_res);
$purok_sql = "SELECT purok, COUNT(*) as count FROM residents GROUP BY purok ORDER BY purok ASC";
$purok_res = mysqli_query($conn, $purok_sql);
$purok_labels = [];
$purok_counts = [];
while($row = mysqli_fetch_assoc($purok_res)) {
    $purok_labels[] = "Purok " . $row['purok'];
    $purok_counts[] = (int)$row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Happy Hallow System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #226b8dff;
            --warning-color: #fbbc04;
            --danger-color: #ea4335;
            --background-color: #f8f9fa;
            --sidebar-bg: #212121;
            --text-color: #202124;
            --border-color: #dadce0;
            --radius: 10px;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        body { margin: 0; font-family: "Roboto", Arial, sans-serif; background: var(--background-color); color: var(--text-color); }
        .app-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--sidebar-bg); color: white; }
        .logo { padding: 25px; text-align: center; font-weight: 700; font-size: 1.15rem; line-height: 1.3; border-bottom: 1px solid #333; }
        .main-nav ul { list-style: none; padding: 0; margin: 0; }
        .main-nav a { display: block; padding: 15px 20px; color: #bdc1c6; text-decoration: none; transition: 0.2s; }
        .main-nav a:hover, .main-nav a.active { background: var(--primary-color); color: white; }
        .main-content { flex: 1; display: flex; flex-direction: column; }
        .topbar { background: white; padding: 15px 30px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: flex-end; align-items: center; }
        .logout-btn { padding: 8px 15px; border: 1px solid var(--border-color); background: transparent; border-radius: 6px; text-decoration: none; color: var(--text-color); font-size: 0.85rem; }
        .page-content { padding: 30px; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 20px; border-left: 5px solid var(--border-color); }
        .stat-label { font-size: 0.7rem; font-weight: 700; color: #5f6368; text-transform: uppercase; }
        .stat-value { font-size: 2.2rem; font-weight: 700; margin-top: 5px; }
        .stat-card.total { border-left-color: var(--primary-color); }
        .stat-card.senior { border-left-color: var(--warning-color); }
        .stat-card.pwd { border-left-color: var(--danger-color); }
        .stat-card.health { border-left-color: #26c6da; }
        .charts-main-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; }
        .chart-box { background: white; padding: 25px; border-radius: var(--radius); box-shadow: var(--shadow); }
        .chart-title { font-weight: 700; color: #5f6368; margin-bottom: 20px; text-transform: uppercase; font-size: 0.8rem; text-align: center; }
        @media (max-width: 900px) { .charts-main-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="logo">Happy Hallow<br />Barangay System</div>
        <nav class="main-nav">
            <ul>
                <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
                <li><a href="residents.php">Manage Residents</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="documents.php">Documents</a></li>
                <li><a href="health_tracking.php">Health Tracking</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span style="margin-right: 15px;">Welcome, <strong><?php echo $logged_in_username; ?></strong></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="page-content">
            <h2 style="margin-bottom: 25px;">Barangay Dashboard</h2>
            
            <div class="dashboard-grid">
                <div class="stat-card total">
                    <div class="stat-label">Total Residents</div>
                    <div class="stat-value"><?php echo number_format($total_residents); ?></div>
                </div>
                <div class="stat-card senior">
                    <div class="stat-label">Seniors</div>
                    <div class="stat-value"><?php echo number_format($total_seniors); ?></div>
                </div>
                <div class="stat-card pwd">
                    <div class="stat-label">PWD</div>
                    <div class="stat-value"><?php echo number_format($total_pwd); ?></div>
                </div>
                <div class="stat-card health">
                    <div class="stat-label">Health Tracking</div>
                    <div class="stat-value"><?php echo number_format($health_cases); ?></div>
                </div>
            </div>

            <div class="charts-main-grid">
                <div class="chart-box">
                    <div class="chart-title">Population Age Distribution</div>
                    <canvas id="ageChart"></canvas>
                </div>
                <div class="chart-box">
                    <div class="chart-title">Population per Purok</div>
                    <canvas id="purokChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('ageChart'), {
        type: 'bar',
        data: { 
            labels: ['Kids (0-12)', 'Teens (13-19)', 'Adults (20-59)', 'Seniors (60+)'], 
            datasets: [{ 
                label: 'Resident Count', 
                data: [
                    <?php echo (int)($age_data['kids'] ?? 0); ?>, 
                    <?php echo (int)($age_data['teens'] ?? 0); ?>, 
                    <?php echo (int)($age_data['adults'] ?? 0); ?>, 
                    <?php echo (int)($age_data['seniors'] ?? 0); ?>
                ], 
                backgroundColor: '#226b8dff' 
            }] 
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    new Chart(document.getElementById('purokChart'), {
        type: 'doughnut',
        data: { 
            labels: <?php echo json_encode($purok_labels); ?>, 
            datasets: [{ 
                data: <?php echo json_encode($purok_counts); ?>,
                backgroundColor: ['#ea4335', '#fbbc04', '#226b8dff', '#34a853', '#673ab7'] 
            }] 
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
</body>
</html>