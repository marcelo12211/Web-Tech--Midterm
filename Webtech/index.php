<?php
include __DIR__ . '/db_connect.php';

$sql = "SELECT
    COUNT(*) AS total_residents,
    SUM(is_senior = 1) AS senior_count,
    SUM(is_disabled = 1) AS pwd_count,
    SUM(is_pregnant = 1) AS pregnant_count,  -- ADDED: Count of pregnant residents
    SUM(children_count) AS total_children,  -- ADDED: Sum of children from all residents
    SUM(PUROK = 1) AS purok1_count,
    SUM(PUROK = 2) AS purok2_count,
    SUM(PUROK = 3) AS purok3_count,
    SUM(PUROK = 4) AS purok4_count,
    SUM(PUROK = 5) AS purok5_count
FROM residents";

$result = $conn->query($sql);
$stats = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - Facilities Database</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="logo">Happy Hallow Barangay System</div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="residents.php">Residents</a></li>
                <li><a href="addnewresidents.php">Add Resident</a></li>
                <li><a href="deaths.php">Deaths</a></li>
                <li><a href="documents.php">Documents</a></li>
                <li class="nav-divider"></li>
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
                    <p class="stat-label">Total Residents</p>
                    <p class="stat-value"><span><?php echo $stats['total_residents']; ?></span></p>
                </div>
                
                <div class="stat-card">
                    <p class="stat-label">Senior Citizen</p>
                    <p class="stat-value"><span><?php echo $stats['senior_count']; ?></span></p>
                </div>
                
                <div class="stat-card">
                    <p class="stat-label">PWD</p>
                    <p class="stat-value"><span><?php echo $stats['pwd_count']; ?></span></p>
                </div>
                
                <div class="stat-card">
                    <p class="stat-label">Pregnant Residents</p>
                    <p class="stat-value"><span><?php echo $stats['pregnant_count']; ?></span></p>
                </div>

                <div class="stat-card">
                    <p class="stat-label">Total Children</p>
                    <p class="stat-value"><span><?php echo $stats['total_children']; ?></span></p>
                </div>
                
                <div class="card chart-card">
                    <h3>Population by Purok</h3>
                    <div class="purok-chart">
                        <?php
                        // The loop now uses $stats['total_residents'] for the base count
                        for ($i = 1; $i <= 5; $i++) {
                            $count = $stats["purok{$i}_count"];
                            $width = $stats['total_residents'] > 0 ? ($count / $stats['total_residents']) * 100 : 0;
                            echo '<div class="purok-item">';
                            echo "<span class='purok-label'>Purok $i</span>";
                            echo "<div class='purok-bar-container'>";
                            echo "<div class='purok-bar' style='width: {$width}%;'></div>";
                            echo "<span class='purok-count'>{$count}</span>";
                            echo "</div></div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    logoutBtn.addEventListener("click", () => {
        localStorage.removeItem("rms_user");
        window.location.href = "login.html";
    });
}
function showUser() {
    const user = JSON.parse(localStorage.getItem("rms_user"));
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