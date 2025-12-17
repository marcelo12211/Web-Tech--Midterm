<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_username = htmlspecialchars($_SESSION['user_name'] ?? 'Guest');
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

* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: "Roboto", Arial, sans-serif;
    background: var(--background-color);
    color: var(--text-color);
}
a { text-decoration: none; }
.app-container { display: flex; min-height: 100vh; }
.sidebar { width: 250px; background: var(--sidebar-bg); color: white; }
.logo { padding: 25px; text-align: center; font-weight: 700; font-size: 1.15rem; line-height: 1.3; }
.main-nav ul { list-style: none; padding: 0; margin: 0; }
.main-nav a { display: block; padding: 14px 20px; color: #bdc1c6; }
.main-nav a:hover,
.main-nav a.active { background: var(--primary-dark); color: white; }

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
    cursor: pointer;
    border-radius: 6px;
}
.page-content { padding: 30px; }

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
}

.stat-card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 20px;
    border-left: 5px solid var(--border-color);
}

.stat-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-light);
}

.stat-value {
    font-size: 2.2rem;
    font-weight: 700;
}

.dashboard-grid .stat-card:nth-child(1) { border-left-color: var(--primary-color); }
.dashboard-grid .stat-card:nth-child(2) { border-left-color: var(--warning-color); }
.dashboard-grid .stat-card:nth-child(3) { border-left-color: var(--danger-color); }
.dashboard-grid .stat-card:nth-child(4) { border-left-color: #64b5f6; }
.dashboard-grid .stat-card:nth-child(5) { border-left-color: #26c6da; }
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
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span class="user-info">Welcome, <?php echo $logged_in_username; ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="page-content">
            <h2>Dashboard Overview</h2>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-label">TOTAL RESIDENTS</div>
                    <div class="stat-value" id="totalResidents">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">SENIOR CITIZEN</div>
                    <div class="stat-value" id="seniorCount">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">PWD</div>
                    <div class="stat-value" id="pwdCount">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">PREGNANT RESIDENTS</div>
                    <div class="stat-value" id="pregnantCount">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">TOTAL CHILDREN</div>
                    <div class="stat-value" id="childrenCount">—</div>
                </div>
            </div>

            <canvas id="categoriesChart" width="200" height="200" style="display: block; box-sizing: border-box; "></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
fetch("http://127.0.0.1:5000/admin/dashboard/stats")
    .then(res => res.json())
    .then(data => {
        document.getElementById("totalResidents").textContent = data.total_residents;
        document.getElementById("seniorCount").textContent = data.senior;
        document.getElementById("pwdCount").textContent = data.pwd;
        document.getElementById("pregnantCount").textContent = data.pregnant;
        document.getElementById("childrenCount").textContent = data.total_children;

        new Chart(document.getElementById("categoriesChart"), {
            type: "doughnut",
            data: {
                labels: ["Senior", "PWD", "Pregnant", "Regular"],
                datasets: [{
                    data: [
                        data.senior,
                        data.pwd,
                        data.pregnant,
                        data.regular
                    ],
                    backgroundColor: ["#76d7d2", "#ffd97d", "#ff8fa3", "#64b5f6"]
                }]
            }
        });
    })
    .catch(err => {
        console.error("Dashboard API error:", err);
    });
</script>

</body>
</html>
