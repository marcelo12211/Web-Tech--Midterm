<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');   
header('Expires: 0');         
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include __DIR__ . '/db_connect.php';

// fetch data from db
$sql = "SELECT
    COUNT(*) AS total_residents,
    SUM(is_senior = 1) AS senior_count,
    SUM(is_disabled = 1) AS pwd_count,
    SUM(is_pregnant = 1) AS pregnant_count,
    SUM(PUROK = 0) AS purok0_count,
    SUM(PUROK = 1) AS purok1_count,
    SUM(PUROK = 2) AS purok2_count,
    SUM(PUROK = 3) AS purok3_count,
    SUM(PUROK = 4) AS purok4_count,
    SUM(PUROK = 5) AS purok5_count
FROM residents";
//execute the queryy
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$stats = $result->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Happy Hallow Barangay System</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chart-card h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 18px;
        }
        .chart-wrapper {
            position: relative;
            height: 300px;
        }
    </style>
</head>
<body>
    <!-- //side bar nav -->
<div class="app-container">
    <aside class="sidebar">
        <div class="logo">Happy Hallow Barangay System</div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="household.php">Households</a></li>
                <li><a href="residents.php">Residents</a></li>
                <li><a href="addnewresidents.php">Add Resident</a></li>
                <li><a href="deaths.php">Deaths</a></li>
                <li><a href="documents.php">Documents</a></li>
            </ul>
        </nav>
    </aside>
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-right">
                <span id="userName" class="user-info">Welcome, User</span>
                <button id="logoutBtn" class="btn logout-btn">Logout</button>
            </div>
        </header>
        <main class="page-content">
            <h2>Dashboard Overview</h2>
            <div class="dashboard-grid">
                
                <div class="stat-card">
                    <p class="stat-label" >Total Residents</p>
                    <p class="stat-value"><span><?php echo $stats['total_residents']; ?></span></p>
                </div>
                
                <div class="stat-card" >
                    <p class="stat-label">Senior Citizen</p>
                    <p class="stat-value" ><span><?php echo $stats['senior_count']; ?></span></p>
                </div>
                
                <div class="stat-card">
                    <p class="stat-label">PWD</p>
                    <p class="stat-value"><span><?php echo $stats['pwd_count']; ?></span></p>
                </div>
                
                <div class="stat-card">
                    <p class="stat-label">Pregnant Residents</p>
                    <p class="stat-value"><span><?php echo $stats['pregnant_count']; ?></span></p>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-card">
                    <h3>Residents Distribution</h3>
                    <div class="chart-wrapper">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <h3>Residents by Purok</h3>
                    <div class="chart-wrapper">
                        <canvas id="purokChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    //resident chart
const categoryData = {
    senior: <?php echo $stats['senior_count']; ?>,
    pwd: <?php echo $stats['pwd_count']; ?>,
    pregnant: <?php echo $stats['pregnant_count']; ?>,
    regular: <?php echo $stats['total_residents'] - $stats['senior_count'] - $stats['pwd_count'] - $stats['pregnant_count']; ?>
};

const colors = {
    blue: 'rgba(54, 162, 235, 0.8)',
    green: 'rgba(75, 192, 192, 0.8)',
    yellow: 'rgba(255, 206, 86, 0.8)',
    red: 'rgba(255, 99, 132, 0.8)'
};

const doughnutCtx = document.getElementById('categoriesChart').getContext('2d');
new Chart(doughnutCtx, {
    type: 'doughnut',
    data: {
        labels: ['Senior Citizens', 'PWD', 'Pregnant', 'Regular Residents'],
        datasets: [{
            data: [
                categoryData.senior,
                categoryData.pwd,
                categoryData.pregnant,
                categoryData.regular
            ],
            backgroundColor: [
                colors.green,
                colors.yellow,
                colors.red,
                colors.blue
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true, 
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Purok Chart data from sa db
const purokData = {
    purok0: <?php echo isset($stats['purok0_count']) ? $stats['purok0_count'] : 0; ?>,
    purok1: <?php echo $stats['purok1_count']; ?>,
    purok2: <?php echo $stats['purok2_count']; ?>,
    purok3: <?php echo $stats['purok3_count']; ?>,
    purok4: <?php echo $stats['purok4_count']; ?>,
    purok5: <?php echo $stats['purok5_count']; ?>
};

// Purok Bar Chart
const purokCtx = document.getElementById('purokChart').getContext('2d');
new Chart(purokCtx, {
    type: 'bar',
    data: {
        labels: ['No Purok', 'Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'],
        datasets: [{
            label: 'Number of Residents',
            data: [
                purokData.purok0,
                purokData.purok1,
                purokData.purok2,
                purokData.purok3,
                purokData.purok4,
                purokData.purok5
            ],
            backgroundColor: [
                'rgba(156, 163, 175, 0.8)',  // Gray for No Purok
                'rgba(59, 130, 246, 0.8)',   // Blue
                'rgba(16, 185, 129, 0.8)',   // Green
                'rgba(245, 158, 11, 0.8)',   // Yellow
                'rgba(239, 68, 68, 0.8)',    // Red
                'rgba(139, 92, 246, 0.8)'    // Purple
            ],
            borderColor: [
                'rgba(156, 163, 175, 1)',
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(245, 158, 11, 1)',
                'rgba(239, 68, 68, 1)',
                'rgba(139, 92, 246, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    logoutBtn.addEventListener("click", () => {
        window.location.href = "logout.php"; 
    });
}
function showUser() {
    const user = JSON.parse(localStorage.getItem("rms_user") || "{}");
    const userNameSpan = document.getElementById("userName");
    if (user && user.name) {
        userNameSpan.textContent = `Welcome, ${user.name}`;
    } else {
        userNameSpan.textContent = `Welcome, Guest`;
    }
}
window.onload = function () {
    showUser();
    setupLogout();
};
</script>
</body>
</html>
