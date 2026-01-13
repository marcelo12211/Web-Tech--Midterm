<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
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
            --primary-dark: #226b8dff;
            --secondary-color: #5f6368;
            --warning-color: #fbbc04;
            --danger-color: #ea4335;
            --background-color: #f8f9fa;
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
        .app-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--sidebar-bg); color: white; }
        .logo { padding: 25px; text-align: center; font-weight: 700; font-size: 1.15rem; line-height: 1.3; }
        .main-nav ul { list-style: none; padding: 0; margin: 0; }
        .main-nav a { display: block; padding: 14px 20px; color: #bdc1c6; text-decoration: none; }
        .main-nav a:hover, .main-nav a.active { background: var(--primary-dark); color: white; }
        
        .main-content { flex: 1; }
        .topbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .user-info { margin-right: 15px; color: var(--text-light); }
        .logout-btn {
            padding: 8px 15px;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-color);
            font-size: 0.9rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .page-content { padding: 30px; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            border-left: 5px solid var(--border-color);
        }
        .stat-label { font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; }
        .stat-value { font-size: 2rem; font-weight: 700; margin-top: 5px; }
        .stat-card.total { border-left-color: var(--primary-color); }
        .stat-card.senior { border-left-color: var(--warning-color); }
        .stat-card.pwd { border-left-color: var(--danger-color); }
        .charts-main-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        .chart-box {
            background: white;
            padding: 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .chart-title { font-weight: 700; color: var(--text-light); margin-bottom: 20px; text-transform: uppercase; font-size: 0.9rem; text-align: center; }

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
            <span class="user-info">Welcome, <?php echo $logged_in_username; ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="page-content">
            <h2 style="margin-bottom: 25px;">Dashboard Overview</h2>
            <div class="dashboard-grid">
                <div class="stat-card total"><div class="stat-label">Total Residents</div><div class="stat-value">1,240</div></div>
                <div class="stat-card senior"><div class="stat-label">Seniors</div><div class="stat-value">158</div></div>
                <div class="stat-card pwd"><div class="stat-label">PWD</div><div class="stat-value">42</div></div>
                <div class="stat-card" style="border-left-color: #26c6da;"><div class="stat-label">Health Cases</div><div class="stat-value">28</div></div>
            </div>

            <div class="charts-main-grid">
                <div class="chart-box">
                    <div class="chart-title">Population Age Brackets</div>
                    <canvas id="ageChart"></canvas>
                </div>
                <div class="chart-box">
                    <div class="chart-title">Health Conditions</div>
                    <canvas id="healthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('ageChart'), {
        type: 'bar',
        data: { labels: ['0-12', '13-19', '20-59', '60+'], datasets: [{ label: 'Residents', data: [200, 150, 600, 158], backgroundColor: '#226b8dff' }] }
    });
    new Chart(document.getElementById('healthChart'), {
        type: 'doughnut',
        data: { labels: ['Hypertension', 'Diabetes', 'Asthma'], datasets: [{ data: [15, 10, 5], backgroundColor: ['#ea4335', '#fbbc04', '#226b8dff'] }] }
    });
</script>
</body>
</html>