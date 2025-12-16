<?php
session_start();

if (!isset($_SESSION['user_id'])) {
}

$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$all_documents = [
    [
        'id' => 'BC-25-00123',
        'type' => 'Clearance',
        'issued_to' => 'Dela Cruz, Juan C.',
        'resident_id' => 'R-0001',
        'date_issued' => '2025-12-09',
        'status' => 'Released',
        'purpose' => 'Job Application',
        'is_paid' => true,
        'released_by' => 'J. Mason (Staff)',
        'note' => 'Valid for 6 months.',
    ],
    [
        'id' => 'CR-25-00045',
        'type' => 'Residency',
        'issued_to' => 'Santos, Maria F.',
        'resident_id' => 'R-0002',
        'date_issued' => '2025-12-05',
        'status' => 'Released',
        'purpose' => 'School Enrollment',
        'is_paid' => true,
        'released_by' => 'E. Fajardo (Admin)',
        'note' => 'No notes.',
    ],
    [
        'id' => 'CR-25-00088',
        'type' => 'Residency',
        'issued_to' => 'Ramos, Lita S.',
        'resident_id' => 'R-0003',
        'date_issued' => '2025-11-20',
        'status' => 'Pending Pickup',
        'purpose' => 'Loan Application',
        'is_paid' => false,
        'released_by' => 'N/A',
        'note' => 'Payment still pending.',
    ],
    [
        'id' => 'CI-25-00001',
        'type' => 'Indigency',
        'issued_to' => 'Cruz, Anna D.',
        'resident_id' => 'R-0004',
        'date_issued' => '2025-11-25',
        'status' => 'Pending Pickup',
        'purpose' => 'Medical Assistance',
        'is_paid' => false,
        'released_by' => 'N/A',
        'note' => 'For signature of the Punong Barangay.',
    ],
];

function getDocTypeBadge($type) {
    switch(strtolower($type)) {
        case 'clearance': return ['text' => 'Clearance', 'class' => 'badge-info'];
        case 'residency': return ['text' => 'Residency', 'class' => 'badge-primary'];
        case 'indigency': return ['text' => 'Indigency', 'class' => 'badge-success'];
        default: return ['text' => ucfirst($type), 'class' => 'badge-none'];
    }
}

function getDocStatusBadge($status) {
    $status = strtolower($status);
    if ($status == 'released') {
        return ['text' => 'Released', 'class' => 'badge-active'];
    }
    if ($status == 'pending pickup') {
        return ['text' => 'Pending Pickup', 'class' => 'badge-staff']; 
    }
    return ['text' => ucfirst($status), 'class' => 'badge-none']; 
}

$docTypes = array_unique(array_column($all_documents, 'type'));
sort($docTypes);
$docStatuses = array_unique(array_column($all_documents, 'status'));
sort($docStatuses);
$search_term = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$error = ''; 
$filteredDocuments = array_filter($all_documents, function ($doc) use ($search_term, $type_filter, $status_filter) {
    $matchesSearch = 
        empty($search_term) ||
        str_contains(strtolower($doc['id']), strtolower($search_term)) ||
        str_contains(strtolower($doc['issued_to']), strtolower($search_term));
    $matchesType = 
        empty($type_filter) ||
        $doc['type'] === $type_filter;
    $matchesStatus =
        empty($status_filter) ||
        $doc['status'] === $status_filter;

    return $matchesSearch && $matchesType && $matchesStatus;
});

$document_count = count($filteredDocuments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay Documents</title>
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

.doc-row {
    cursor: pointer;
    transition: background-color 0.2s;
}
.doc-row.expanded {
    background-color: #eef2f5; 
    border-bottom: none; 
}
.doc-row:hover:not(.expanded) {
    background: #f0f0f0; 
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
.export-btn { 
    display: flex;
    align-items: center;
    gap: 8px;
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

.badge-admin { 
    background-color: #fce4e4; 
    color: var(--danger-color);
}
.badge-staff { 
    background-color: #fff4e5; 
    color: #cc9900;
}
.badge-clerk { 
    background-color: #e6f7ff; 
    color: #1890ff;
}
.badge-active { 
    background-color: #e6f4ea;
    color: var(--primary-dark);
}
.badge-inactive { 
    background-color: #f0f0f0;
    color: var(--text-light);
}

.badge-info { 
    background-color: #eaf6fa; 
    color: #008cba; 
} 
.badge-primary { 
    background-color: #e6e9ff; 
    color: #0040ff; 
} 
.badge-success { 
    background-color: #e6f4ea; 
    color: var(--primary-dark); 
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

.template-grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 15px;
}
.template-item {
    padding: 20px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    text-align: center;
    background: #fcfcfc;
}
.template-item h4 {
    margin-top: 0;
    font-size: 1.1rem;
    color: var(--primary-color);
}
.template-item p {
    font-size: 0.9rem;
    color: var(--text-light);
    margin-bottom: 15px;
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
    .export-btn {
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
                <li><a href="residents.php">Manage Residents</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="documents.php" class="active">Documents</a></li>
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
            <h2 style="margin-bottom: 15px;">Document Templates</h2>
            
            <div class="card template-card">
                <div class="template-grid-container">
                    <div class="template-item">
                        <h4>Barangay Clearance</h4>
                        <p>Generate a general or specific clearance certificate.</p>
                        <a href="generate_clearance.php" class="btn primary-btn" style="padding: 8px 15px;">
                            <i class="fas fa-plus"></i> Generate
                        </a>
                    </div>
                    <div class="template-item">
                        <h4>Certificate of Residency</h4>
                        <p>Official proof of barangay residency.</p>
                        <a href="generate_residency.php" class="btn primary-btn" style="padding: 8px 15px;">
                            <i class="fas fa-plus"></i> Generate
                        </a>
                    </div>
                    <div class="template-item">
                        <h4>Certificate of Indigency</h4>
                        <p>Low-income status assistance certificate.</p>
                        <a href="generate_indigency.php" class="btn primary-btn" style="padding: 8px 15px;">
                            <i class="fas fa-plus"></i> Generate
                        </a>
                    </div>
                </div>
            </div>

            <h2>**Generated Documents** (<?php echo $document_count; ?> Found)</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="GET" action="documents.php" class="data-control-panel">
                <div class="search-and-filter-wrapper">
                    <input
                        type="text"
                        placeholder="Search by ID or Issued Name..."
                        class="search-input"
                        name="search"
                        value="<?php echo htmlspecialchars($search_term); ?>"
                    />
                    <div class="filter-group">
                        <select class="filter-select" name="type">
                            <option value="">-- All Types --</option>
                            <?php foreach ($docTypes as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $type_filter == $type ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select class="filter-select" name="status">
                            <option value="">-- All Status --</option>
                            <?php foreach ($docStatuses as $status): ?>
                                <option value="<?php echo htmlspecialchars($status); ?>" <?php echo $status_filter == $status ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($status); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn primary-btn">Filter</button>
                        <a href="documents.php" class="btn">Reset</a>
                    </div>
                </div>
                <a href="export_documents.php" class="btn primary-btn export-btn" style="background: #226b8dff; border-color: #226b8dff;">Export Report
                </a>
            </form>
            
            <div class="card data-table-card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Doc ID</th>
                                <th>Issued To</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($filteredDocuments)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No documents found matching the criteria.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($filteredDocuments as $doc): 
                                    $type_badge = getDocTypeBadge($doc['type']);
                                    $status_badge = getDocStatusBadge($doc['status']); 
                                    $doc_id = htmlspecialchars($doc['id']);
                                ?>
                                    <tr class="doc-row" data-doc-id="<?php echo $doc_id; ?>">
                                        <td><?php echo $doc_id; ?></td>
                                        <td><?php echo htmlspecialchars($doc['issued_to']); ?></td>
                                        <td><span class="special-status-bar <?php echo $type_badge['class']; ?>" style="margin-bottom: 0;"><?php echo $type_badge['text']; ?></span></td>
                                        <td><?php echo htmlspecialchars($doc['date_issued']); ?></td>
                                        <td><span class="special-status-bar <?php echo $status_badge['class']; ?>" style="margin-bottom: 0;"><?php echo $status_badge['text']; ?></span></td>
                                        <td>
                                            <div class="action-icon-group">
                                                <a href="view_document.php?id=<?php echo $doc_id; ?>" class="action-btn" title="View/Edit Details">
                                                    <i class="fas fa-edit"></i> 
                                                </a>
                                                <a href="archive_document.php?id=<?php echo $doc_id; ?>" class="action-btn delete-btn" title="Archive Document" onclick="return confirm('Archive document ID: <?php echo $doc_id; ?>?');">
                                                    <i class="fas fa-archive"></i> 
                                                </a>
                                                <?php if (strtolower($doc['status']) === 'pending pickup'): ?>
                                                    <a href="release_document.php?id=<?php echo $doc_id; ?>" class="action-btn" style="color: var(--primary-color);" title="Mark as Released" onclick="return confirm('Mark document ID: <?php echo $doc_id; ?> as RELEASED?');">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <tr class="detail-row" data-detail-id="<?php echo $doc_id; ?>">
                                        <td colspan="6">
                                            <div class="detail-container">
                                                
                                                <div class="special-status-bar <?php echo $status_badge['class']; ?>">
                                                     Status: <?php echo $status_badge['text']; ?>
                                                </div>

                                                <div class="detail-tabs">
                                                    <span class="detail-tab active" data-tab="details-<?php echo $doc_id; ?>">Document Details</span>
                                                    <span class="detail-tab" data-tab="history-<?php echo $doc_id; ?>">Issuance History</span>
                                                </div>
                                                
                                                <div class="tab-content active" id="details-<?php echo $doc_id; ?>">
                                                    <div class="detail-grid">
                                                        <div class="detail-box">
                                                            <h4>IDENTIFICATION</h4>
                                                            <p><strong>Doc ID:</strong> <?php echo $doc_id; ?></p>
                                                            <p><strong>Issued To:</strong> <?php echo htmlspecialchars($doc['issued_to']); ?></p>
                                                            <p><strong>Resident ID:</strong> <?php echo htmlspecialchars($doc['resident_id']); ?></p>
                                                            <p><strong>Document Type:</strong> <span class="special-status-bar <?php echo $type_badge['class']; ?>" style="padding: 3px 8px; margin: 0;"><?php echo $type_badge['text']; ?></span></p>
                                                        </div>
                                                        <div class="detail-box">
                                                            <h4>ISSUANCE INFO</h4>
                                                            <p><strong>Purpose:</strong> <?php echo htmlspecialchars($doc['purpose']); ?></p>
                                                            <p><strong>Date Issued:</strong> <?php echo htmlspecialchars($doc['date_issued']); ?></p>
                                                            <p><strong>Payment Status:</strong> <?php echo $doc['is_paid'] ? 'Paid (&#x20B1;50)' : 'Pending Payment'; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="tab-content" id="history-<?php echo $doc_id; ?>">
                                                    <div class="detail-box">
                                                        <h4>NOTES AND RELEASE</h4>
                                                        <p><strong>Issued By:</strong> E. Fajardo (Admin)</p> 
                                                        <p><strong>Released By:</strong> <?php echo htmlspecialchars($doc['released_by']); ?></p>
                                                        <p><strong>Notes:</strong> <em><?php echo htmlspecialchars($doc['note']); ?></em></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    function collapseAllDetails() {
        document.querySelectorAll('.detail-row').forEach(otherDetail => {
            otherDetail.classList.remove('expanded'); 
        });
        document.querySelectorAll('.doc-row').forEach(otherRow => {
            otherRow.classList.remove('expanded');
        });
    }
    document.querySelectorAll(".doc-row").forEach((row) => {
        row.addEventListener("click", (e) => {
            if (e.target.closest(".action-btn, a")) {
                return;
            }

            const docId = row.dataset.docId;
            const detailRow = document.querySelector(
                `.detail-row[data-detail-id="${docId}"]`
            );

            if (detailRow) {
                const isExpanded = row.classList.contains("expanded");
                collapseAllDetails();
                
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
                        document.getElementById(firstContentId).classList.add("active");
                    }
                }
            }
        });
    });
    document.querySelectorAll(".detail-tab").forEach((tab) => {
        tab.addEventListener("click", (e) => {
            const detailContainer = e.target.closest(".detail-container");
            const tabName = e.target.dataset.tab;
            
            if (detailContainer) {
                detailContainer.querySelectorAll(".detail-tabs .detail-tab").forEach((t) => t.classList.remove("active"));
                detailContainer.querySelectorAll(".tab-content").forEach((c) => c.classList.remove("active"));
                e.target.classList.add("active");
                detailContainer.querySelector(`#${tabName}`).classList.add("active");
            }
        });
    });
});
</script>
</body>
</html>