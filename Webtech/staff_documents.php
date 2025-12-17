<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Staff';

// Sample documents data (no database needed)
$all_documents = [
    [
        'document_id' => 'BC-25-00123',
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
        'document_id' => 'CR-25-00045',
        'type' => 'Residency',
        'issued_to' => 'Santos, Maria F.',
        'resident_id' => 'R-0002',
        'date_issued' => '2025-12-05',
        'status' => 'Released',
        'purpose' => 'School Enrollment',
        'is_paid' => true,
        'released_by' => 'E. Fajardo (Staff)',
        'note' => 'No notes.',
    ],
    [
        'document_id' => 'CR-25-00088',
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
        'document_id' => 'CI-25-00001',
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
        case 'clearance': return ['text' => 'Clearance', 'class' => 'badge-clearance'];
        case 'residency': return ['text' => 'Residency', 'class' => 'badge-residency'];
        case 'indigency': return ['text' => 'Indigency', 'class' => 'badge-indigency'];
        default: return ['text' => ucfirst($type), 'class' => 'badge-default'];
    }
}

function getDocStatusBadge($status) {
    $status = strtolower($status);
    if ($status == 'released') {
        return ['text' => 'Released', 'class' => 'status-released'];
    }
    if ($status == 'pending pickup') {
        return ['text' => 'Pending Pickup', 'class' => 'status-pending']; 
    }
    return ['text' => ucfirst($status), 'class' => 'status-default']; 
}

$docTypes = array_unique(array_column($all_documents, 'type'));
sort($docTypes);
$docStatuses = array_unique(array_column($all_documents, 'status'));
sort($docStatuses);

$search_term = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';
$status_filter = $_GET['status'] ?? '';

$filteredDocuments = array_filter($all_documents, function ($doc) use ($search_term, $type_filter, $status_filter) {
    $matchesSearch = 
        empty($search_term) ||
        str_contains(strtolower($doc['document_id']), strtolower($search_term)) ||
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
    <title>Generate Certificates</title>
    <link rel="stylesheet" href="css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <style>
.doc-row {
    cursor: pointer;
    transition: background-color 0.2s;
}
.doc-row.expanded {
    background-color: #eef2f5; 
}
.doc-row:hover:not(.expanded) {
    background: #f0f0f0; 
}

.data-control-panel {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
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
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
}
.filter-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
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
    color: var(--secondary-color);
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
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}
.detail-box h4 {
    margin: 0 0 15px 0;
    font-size: 0.85rem;
    color: var(--secondary-color);
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.detail-box p {
    margin: 8px 0;
    font-size: 0.95rem;
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #e9ecef;
}
.detail-box p:last-child {
    border-bottom: none;
}
.detail-box strong {
    color: var(--secondary-color);
    font-weight: 600;
    font-size: 0.9rem;
}
.detail-box p span:last-child {
    color: var(--text-color);
    font-weight: 500;
}

.special-status-bar {
    padding: 6px 12px;
    border-radius: 14px;
    font-weight: 600;
    font-size: 0.75rem;
    margin-bottom: 15px;
    display: inline-block;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-clearance { 
    background-color: #e3f2fd; 
    color: #1565c0; 
} 
.badge-residency { 
    background-color: #e8f5e9; 
    color: #2e7d32; 
} 
.badge-indigency { 
    background-color: #fff3e0; 
    color: #e65100; 
} 
.badge-default {
    background-color: #f0f0f0;
    color: var(--secondary-color);
}

.status-released { 
    background-color: #e8f5e9;
    color: #2e7d32;
}
.status-pending { 
    background-color: #fff8e1; 
    color: #f57c00;
}
.status-default {
    background-color: #f0f0f0;
    color: var(--secondary-color);
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
    color: #dc3545;
}

.template-grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 15px;
}
.template-item {
    padding: 25px;
    border: 1px solid var(--border-color);
    border-radius: 10px;
    text-align: center;
    background: white;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.template-item:hover {
    box-shadow: var(--shadow-medium);
    transform: translateY(-3px);
}
.template-item h4 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 1.2rem;
    color: var(--primary-color);
    font-weight: 600;
}
.template-item p {
    font-size: 0.9rem;
    color: var(--secondary-color);
    margin-bottom: 20px;
    line-height: 1.5;
}

@media (max-width: 900px) {
    .search-and-filter-wrapper {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
    }
    .search-input, .filter-select {
        width: 100%;
    }
    .filter-group {
        width: 100%;
    }
    .data-control-panel {
        flex-direction: column;
        align-items: flex-start;
    }
    .export-btn {
        width: 100%;
        justify-content: center;
    }
    .detail-grid {
        flex-direction: column;
    }
}
    </style>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="logo">Happy Hallow Barangay System</div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="household.php">Households</a></li>
                <li><a href="residents.php">Residents</a></li>
                <li><a href="addnewresidents.php">Add Resident</a></li>
                <li><a href="deaths.php">Deaths</a></li>
                <li><a href="documents.php">Documents</a></li>
                <li><a href="staff_documents.php">Generate Certificates</a></li>
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
                        <a href="staff_generate_clearance.php" class="btn primary-btn" style="padding: 8px 15px;">
                            <i class="fas fa-plus"></i> Generate
                        </a>
                    </div>
                    <div class="template-item">
                        <h4>Certificate of Residency</h4>
                        <p>Official proof of barangay residency.</p>
                        <a href="staff_generate_residency.php" class="btn primary-btn" style="padding: 8px 15px;">
                            <i class="fas fa-plus"></i> Generate
                        </a>
                    </div>
                    <div class="template-item">
                        <h4>Certificate of Indigency</h4>
                        <p>Low-income status assistance certificate.</p>
                        <a href="staff_generate_indigency.php" class="btn primary-btn" style="padding: 8px 15px;">
                            <i class="fas fa-plus"></i> Generate
                        </a>
                    </div>
                </div>
            </div>

            <h2>Generated Documents</h2>
            
            <form method="GET" action="staff_documents.php" class="data-control-panel">
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
                        <a href="staff_documents.php" class="btn">Reset</a>
                    </div>
                </div>
                <a href="staff_export_documents.php" class="btn primary-btn export-btn" style="background: #226b8dff; border-color: #226b8dff;">Export Report
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
                                    $doc_id = htmlspecialchars($doc['document_id']);
                                ?>
                                    <tr class="doc-row" data-doc-id="<?php echo $doc_id; ?>">
                                        <td><?php echo $doc_id; ?></td>
                                        <td><?php echo htmlspecialchars($doc['issued_to']); ?></td>
                                        <td><span class="special-status-bar <?php echo $type_badge['class']; ?>" style="margin-bottom: 0;"><?php echo $type_badge['text']; ?></span></td>
                                        <td><?php echo htmlspecialchars($doc['date_issued']); ?></td>
                                        <td><span class="special-status-bar <?php echo $status_badge['class']; ?>" style="margin-bottom: 0;"><?php echo $status_badge['text']; ?></span></td>
                                        <td>
                                            <div class="action-icon-group">
                                                <a href="staff_view_document.php?id=<?php echo $doc_id; ?>" class="action-btn" title="View/Edit Details">
                                                    <i class="fas fa-edit"></i> 
                                                </a>
                                                <a href="staff_archive_document.php?id=<?php echo $doc_id; ?>" class="action-btn delete-btn" title="Archive Document" onclick="return confirm('Archive document ID: <?php echo $doc_id; ?>?');">
                                                    <i class="fas fa-archive"></i> 
                                                </a>
                                                <?php if (strtolower($doc['status']) === 'pending pickup'): ?>
                                                    <a href="staff_release_document.php?id=<?php echo $doc_id; ?>" class="action-btn" style="color: var(--primary-color);" title="Mark as Released" onclick="return confirm('Mark document ID: <?php echo $doc_id; ?> as RELEASED?');">
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
                                                            <p><strong>Doc ID:</strong> <span><?php echo $doc_id; ?></span></p>
                                                            <p><strong>Issued To:</strong> <span><?php echo htmlspecialchars($doc['issued_to']); ?></span></p>
                                                            <p><strong>Resident ID:</strong> <span><?php echo htmlspecialchars($doc['resident_id']); ?></span></p>
                                                            <p><strong>Document Type:</strong> <span class="special-status-bar <?php echo $type_badge['class']; ?>" style="padding: 3px 8px; margin: 0;"><?php echo $type_badge['text']; ?></span></p>
                                                        </div>
                                                        <div class="detail-box">
                                                            <h4>ISSUANCE INFO</h4>
                                                            <p><strong>Purpose:</strong> <span><?php echo htmlspecialchars($doc['purpose']); ?></span></p>
                                                            <p><strong>Date Issued:</strong> <span><?php echo htmlspecialchars($doc['date_issued']); ?></span></p>
                                                            <p><strong>Payment Status:</strong> <span><?php echo $doc['is_paid'] ? 'Paid (&#x20B1;50)' : 'Pending Payment'; ?></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="tab-content" id="history-<?php echo $doc_id; ?>">
                                                    <div class="detail-box">
                                                        <h4>NOTES AND RELEASE</h4>
                                                        <p><strong>Issued By:</strong> <span><?php echo htmlspecialchars($logged_in_username); ?> (Staff)</span></p> 
                                                        <p><strong>Released By:</strong> <span><?php echo htmlspecialchars($doc['released_by']); ?></span></p>
                                                        <p><strong>Notes:</strong> <span><em><?php echo htmlspecialchars($doc['note']); ?></em></span></p>
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
