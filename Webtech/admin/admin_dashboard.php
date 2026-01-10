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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #226b8dff;
            --primary-dark: #1a526d;
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
        .main-content { flex: 1; display: flex; flex-direction: column; }
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
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-color);
        }
        .logout-btn:hover { background: #f1f1f1; }

        .page-content { padding: 30px; }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
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
        .stat-card.pregnant { border-left-color: #ff8fa3; }
        .stat-card.children { border-left-color: #26c6da; }
        .chart-section {
            background: white;
            padding: 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            max-width: 500px; 
            margin: 0 auto; 
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        h3.chart-title {
            text-align: center;
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 20px;
        }
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
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="documents.php">Documents</a></li>
                </ul>
            </nav>
        </div>

    <div class="main-content">
        <div class="topbar">
            <span class="user-info">Welcome, <strong><?php echo $logged_in_username; ?></strong></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="page-content">
            <h2 class="mb-4">Dashboard Overview</h2>

            <div class="dashboard-grid">
                <div class="stat-card total">
                    <div class="stat-label">Total Residents</div>
                    <div class="stat-value" id="totalResidents">—</div>
                </div>
                <div class="stat-card senior">
                    <div class="stat-label">Senior Citizens</div>
                    <div class="stat-value" id="seniorCount">—</div>
                </div>
                <div class="stat-card pwd">
                    <div class="stat-label">PWD</div>
                    <div class="stat-value" id="pwdCount">—</div>
                </div>
                <div class="stat-card pregnant">
                    <div class="stat-label">Pregnant</div>
                    <div class="stat-value" id="pregnantCount">—</div>
                </div>
                <div class="stat-card children">
                    <div class="stat-label">Total Children</div>
                    <div class="stat-value" id="childrenCount">—</div>
                </div>
            </div>

            <div class="chart-section">
                <h3 class="chart-title">Resident Demographics</h3>
                <div class="chart-container">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    fetch("http://127.0.0.1:5000/admin/dashboard/stats")
        .then(res => res.json())
        .then(data => {
            document.getElementById("totalResidents").textContent = data.total_residents;
            document.getElementById("seniorCount").textContent = data.senior;
            document.getElementById("pwdCount").textContent = data.pwd;
            document.getElementById("pregnantCount").textContent = data.pregnant;
            document.getElementById("childrenCount").textContent = data.total_children;
            const ctx = document.getElementById("categoriesChart").getContext('2d');
            new Chart(ctx, {
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
                        backgroundColor: ["#76d7d2", "#ffd97d", "#ff8fa3", "#64b5f6"],
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: { size: 12 }
                            }
                        }
                    }
                }
            });
        })
        .catch(err => {
            console.error("Dashboard API error:", err);
        });
</script>

</body>
</html>