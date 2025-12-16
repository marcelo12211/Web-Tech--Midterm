<?php
session_start();
include '../db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$search_term = $_GET['search'] ?? '';
$purok_filter = $_GET['purok'] ?? '';
$category_filter = $_GET['category'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resident Directory</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
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
.main-nav ul { list-style: none; padding: 0; margin: 0; }
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
    color: var(--text-color);
    font-size: 0.9rem;
    cursor: pointer;
    border-radius: 6px;
    transition: background-color 0.2s;
    font-weight: 500;
}
.logout-btn:hover { background: var(--background-color); }

.page-content { padding: 30px; }

.card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 25px;
    margin-bottom: 30px;
}
.btn {
    padding: 10px 18px;
    border-radius: 6px;
    font-weight: 500;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
}
.primary-btn {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}
.primary-btn:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
}

table {
    width: 100%;
    border-collapse: collapse;
}
thead {
    background: #eef2f5;
    color: var(--text-light);
    font-weight: 600;
    font-size: 0.9rem;
    text-align: left;
}
th, td {
    padding: 14px;
    border-bottom: 1px solid var(--border-color);
}

.data-control-panel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.search-and-filter-wrapper {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap; 
}
.search-input {
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    width: 250px;
    font-size: 1rem;
    transition: border-color 0.2s;
}
.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 1px var(--primary-color);
}
.filter-group {
    display: flex;
    gap: 10px;
}
.filter-select {
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 1rem;
    background-color: white;
    cursor: pointer;
}

.add-btn {
    display: flex;
    align-items: center;
    gap: 8px;
}

.resident-row {
    cursor: pointer;
    transition: background-color 0.2s;
}
.resident-row.expanded {
    background-color: #eef2f5; 
    border-bottom: none; 
}
.resident-row:hover:not(.expanded) {
    background: #f0f0f0; 
}

.detail-row {
    display: none; 
}
.detail-row.expanded {
    display: table-row; 
    background: #fcfcfc; 
}
.detail-row td {
    padding: 0 !important; 
    border-top: 1px solid #e0e0e0;
}
.detail-container {
    padding: 20px 15px;
}

.detail-tabs {
    border-bottom: 2px solid var(--border-color);
    margin-bottom: 15px;
    display: flex;
}
.detail-tab {
    padding: 10px 15px;
    cursor: pointer;
    font-weight: 500;
    color: var(--text-light);
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    margin-bottom: -2px; 
}
.detail-tab:hover {
    color: var(--primary-color);
}
.detail-tab.active {
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-color);
}

.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
    padding-top: 10px;
}
.detail-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.detail-box {
    flex: 1 1 300px;
    padding: 10px;
    border-left: 3px solid #ccc;
}
.detail-box h4 {
    margin: 0 0 10px 0;
    font-size: 0.9rem;
    color: var(--secondary-color);
    text-transform: uppercase;
    font-weight: 700;
}
.detail-box p {
    margin: 5px 0;
    font-size: 0.95rem;
}
.detail-box strong {
    color: var(--text-color);
    font-weight: 600;
    display: inline-block;
    width: 150px; 
}

.special-status-bar {
    padding: 8px 15px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.85rem;
    margin-bottom: 15px;
    display: inline-block;
}
.badge-senior {
    background-color: #fff4e5;
    color: #cc9900;
}
.badge-pwd {
    background-color: #fce4e4;
    color: var(--danger-color);
}
.badge-pregnant {
    background-color: #e6f7ff;
    color: #1890ff;
}
.badge-none {
    background-color: #f0f0f0;
    color: var(--text-light);
}

.action-btn {
    color: var(--secondary-color);
    font-size: 1rem;
    margin: 0 5px;
    transition: color 0.2s;
}
.action-btn:hover {
    color: var(--primary-color);
}
.delete-btn:hover {
    color: var(--danger-color);
}

/* Alerts */
.alert-success, .alert-error {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
}
.alert-success {
    background-color: #e6f4ea;
    color: var(--primary-dark);
    border: 1px solid var(--primary-dark);
}
.alert-error {
    background-color: #fce4e4;
    color: var(--danger-color);
    border: 1px solid var(--danger-color);
}
@media (max-width: 900px) {
    .search-and-filter-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }
    .search-input, .filter-select {
        width: 100%;
    }
    .filter-group {
        width: 100%;
        justify-content: space-between;
    }
    .data-control-panel {
        flex-direction: column;
        align-items: flex-start;
    }
    .add-btn {
        width: 100%;
        margin-top: 15px;
        justify-content: center;
    }
    .detail-grid {
        flex-direction: column;
    }
    .detail-box {
        border-left: none;
        border-bottom: 1px solid #eee;
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
                <h2>Resident Directory (<span id="resident-count">Loading...</span> Found)</h2>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert-success">
                        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert-error">
                        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>

                <form id="filter-form" action="residents.php" class="data-control-panel" onsubmit="return false;">
                    <div class="search-and-filter-wrapper">
                        <input
                            type="text"
                            placeholder="Search by name or ID..."
                            class="search-input"
                            id="search-input"
                            name="search"
                            value="<?php echo htmlspecialchars($search_term); ?>"
                        />
                        <div class="filter-group">
                            <select class="filter-select" name="category" id="category-select">
                                <option value="">-- Select Category --</option>
                                <option value="senior" <?php echo ($category_filter == 'senior' ? 'selected' : ''); ?>>Senior Citizen</option>
                                <option value="pwd" <?php echo ($category_filter == 'pwd' ? 'selected' : ''); ?>>PWD</option>
                                <option value="pregnant" <?php echo ($category_filter == 'pregnant' ? 'selected' : ''); ?>>Pregnant</option>
                            </select>
                            <select class="filter-select" name="purok" id="purok-select">
                                <option value="">-- Select Purok --</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($purok_filter == $i ? 'selected' : ''); ?>>Purok <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <a href="residents.php" class="btn">Reset</a>
                        </div>
                    </div>
                    <a href="addnewresidents.php" class="btn primary-btn add-btn">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </form>

                <div class="card data-table-card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Sex</th>
                                    <th>Birthdate</th>
                                    <th>Civil Status</th>
                                    <th>Purok</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="resident-table-body">
                                <tr><td colspan="7" style="text-align: center;">Loading resident data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            
            const tableBody = document.getElementById("resident-table-body");
            const searchInput = document.getElementById("search-input");
            const categorySelect = document.getElementById("category-select");
            const purokSelect = document.getElementById("purok-select");
            const residentCountSpan = document.getElementById("resident-count");
            let searchTimeout;
            
            function loadResidents() {
                collapseAllDetails();
                tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Filtering data...</td></tr>';
                
                const searchTerm = searchInput.value;
                const category = categorySelect.value;
                const purok = purokSelect.value;
                const url = `fetch_residents.php?search=${encodeURIComponent(searchTerm)}&category=${encodeURIComponent(category)}&purok=${encodeURIComponent(purok)}`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => { throw new Error(text) });
                        }
                        return response.text();
                    })
                    .then(html => {
                        tableBody.innerHTML = html; 
                        const residentRows = tableBody.querySelectorAll('.resident-row');
                        residentCountSpan.textContent = residentRows.length;
                        attachRowClickListeners();
                        attachDetailTabListeners();
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        tableBody.innerHTML = `<tr><td colspan="7" class="alert-error" style="text-align: center;">${error.message || 'Failed to load data. Please check network and server logs.'}</td></tr>`;
                        residentCountSpan.textContent = '0';
                    });
            }
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(loadResidents, 300);
            });
            categorySelect.addEventListener('change', loadResidents);
            purokSelect.addEventListener('change', loadResidents);
            function collapseAllDetails() {
                document.querySelectorAll('.detail-row').forEach(otherDetail => {
                    otherDetail.classList.remove('expanded'); 
                });
                document.querySelectorAll('.resident-row').forEach(otherRow => {
                    otherRow.classList.remove('expanded');
                });
            }
            
            function handleRowClick(e) {
                if (e.target.closest(".action-btn, a")) {
                    return;
                }

                const row = e.currentTarget;
                const residentId = row.dataset.residentId;
                const detailRow = document.querySelector(
                    `.detail-row[data-detail-id="${residentId}"]`
                );

                if (detailRow) {
                    const isExpanded = row.classList.contains("expanded");
                    document.querySelectorAll('.resident-row.expanded').forEach(r => {
                        if (r !== row) r.classList.remove('expanded');
                    });
                    document.querySelectorAll('.detail-row.expanded').forEach(dr => {
                        if (dr !== detailRow) dr.classList.remove('expanded');
                    });
                    if (!isExpanded) {
                        row.classList.add("expanded");
                        detailRow.classList.add('expanded');
                        const allTabs = detailRow.querySelectorAll(".detail-tab");
                        const allContents = detailRow.querySelectorAll(".tab-content");
                        const firstTab = detailRow.querySelector(".detail-tab");
                        
                        allTabs.forEach((tab) => tab.classList.remove("active"));
                        allContents.forEach((content) => content.classList.remove("active"));
                        
                        if (firstTab) {
                            firstTab.classList.add("active");
                            const firstContentId = firstTab.dataset.tab;
                            const firstContent = document.getElementById(firstContentId);
                            if (firstContent) {
                                firstContent.classList.add("active");
                            }
                        }
                    } else {
                         row.classList.remove("expanded");
                         detailRow.classList.remove('expanded');
                    }
                }
            }

            function attachRowClickListeners() {
                document.querySelectorAll(".resident-row").forEach((row) => {
                    row.removeEventListener("click", handleRowClick);
                    row.addEventListener("click", handleRowClick);
                });
            }
            
            function handleTabClick(e) {
                const detailContainer = e.target.closest(".detail-container");
                const tabName = e.target.dataset.tab;
                
                if (detailContainer) {
                    detailContainer.querySelectorAll(".detail-tabs .detail-tab").forEach((t) => t.classList.remove("active"));
                    detailContainer.querySelectorAll(".tab-content").forEach((c) => c.classList.remove("active"));
                    e.target.classList.add("active");
                    const contentElement = detailContainer.querySelector(`#${tabName}`);
                    if (contentElement) {
                        contentElement.classList.add("active");
                    }
                }
            }
            
            function attachDetailTabListeners() {
                document.querySelectorAll(".detail-tab").forEach((tab) => {
                    tab.removeEventListener("click", handleTabClick);
                    tab.addEventListener("click", handleTabClick);
                });
            }
            loadResidents();
        });
    </script>
</body>
</html>