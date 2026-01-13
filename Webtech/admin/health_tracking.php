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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Health Tracking - Happy Hallow System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
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
            text-decoration: none;
        }

        .page-content { padding: 30px; }
        .detail-tabs {
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 25px;
            display: flex;
        }
        .detail-tab {
            padding: 12px 25px;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-light);
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            margin-bottom: -2px;
        }
        .detail-tab:hover { color: var(--primary-color); }
        .detail-tab.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }

        .card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 25px;
        }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #eef2f5; color: var(--text-light); font-size: 0.9rem; text-align: left; }
        th, td { padding: 14px; border-bottom: 1px solid var(--border-color); }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .special-status-bar {
            padding: 5px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
        }
        .badge-success { background-color: #e6f4ea; color: #1e7e34; }
        .badge-info { background-color: #eaf6fa; color: #008cba; }
        .badge-warning { background-color: #fff4e5; color: #cc9900; }
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
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="documents.php">Documents</a></li>
                <li><a href="health_tracking.php" class="active">Health Tracking</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span class="user-info">Welcome, <?php echo $logged_in_username; ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="page-content">
            <h2 style="margin-bottom: 20px;">Health & Vaccination Tracking</h2>

            <div class="detail-tabs">
                <div class="detail-tab active" data-tab="maintenance-section">Maintenance Monitoring</div>
                <div class="detail-tab" data-tab="vaccination-section">Vaccination Program</div>
            </div>

            <div id="maintenance-section" class="tab-content active">
                <div class="card">
                    <h4 style="margin-top:0; color:var(--primary-color);">Maintenance Medicine Profiles</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Resident Name</th>
                                <th>Medical Condition</th>
                                <th>Maintenance Meds</th>
                                <th>Last Checkup</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Dela Cruz, Juan C.</td>
                                <td>Hypertension</td>
                                <td>Amlodipine 5mg</td>
                                <td>2025-12-10</td>
                                <td><span class="special-status-bar badge-success">Active Intake</span></td>
                            </tr>
                            <tr>
                                <td>Santos, Maria F.</td>
                                <td>Diabetes</td>
                                <td>Metformin 500mg</td>
                                <td>2025-11-28</td>
                                <td><span class="special-status-bar badge-info">For Refill</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="vaccination-section" class="tab-content">
                <div class="card">
                    <h4 style="margin-top:0; color:var(--primary-color);">Child Vaccination Records</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Child Name</th>
                                <th>Vaccine Type</th>
                                <th>Dose</th>
                                <th>Date Administered</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Baby Ramos, Liam S.</td>
                                <td>Anti-Polio (OPV)</td>
                                <td>2nd Dose</td>
                                <td>2025-12-05</td>
                                <td><span class="special-status-bar badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>Baby Cruz, Anna D.</td>
                                <td>Measles (MMR)</td>
                                <td>1st Dose</td>
                                <td>2025-11-20</td>
                                <td><span class="special-status-bar badge-warning">Next Dose: Jan 2026</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.detail-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-tab');
            document.querySelectorAll('.detail-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });
</script>
</body>
</html>