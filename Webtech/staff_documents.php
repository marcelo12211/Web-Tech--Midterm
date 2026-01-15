<?php
session_start();

// 1. SESSION-BASED DATA SIMULATION
// Ginagawa natin ito para "gumana" ang delete kahit walang database.
if (!isset($_SESSION['all_documents'])) {
    $_SESSION['all_documents'] = [
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
}

// 2. DELETE/ARCHIVE LOGIC
if (isset($_GET['archive_id'])) {
    $id_to_remove = $_GET['archive_id'];
    foreach ($_SESSION['all_documents'] as $key => $doc) {
        if ($doc['document_id'] === $id_to_remove) {
            unset($_SESSION['all_documents'][$key]);
            $_SESSION['all_documents'] = array_values($_SESSION['all_documents']); // Re-index
            break;
        }
    }
    header("Location: staff_documents.php?msg=deleted");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    // header("Location: login.php"); // Naka-comment para sa demo
    // exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Staff';

// Helper Functions
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
    if ($status == 'released') return ['text' => 'Released', 'class' => 'status-released'];
    if ($status == 'pending pickup') return ['text' => 'Pending Pickup', 'class' => 'status-pending']; 
    return ['text' => ucfirst($status), 'class' => 'status-default']; 
}

// Filtering Logic
$docTypes = array_unique(array_column($_SESSION['all_documents'], 'type'));
sort($docTypes);
$docStatuses = array_unique(array_column($_SESSION['all_documents'], 'status'));
sort($docStatuses);

$search_term = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';
$status_filter = $_GET['status'] ?? '';

$filteredDocuments = array_filter($_SESSION['all_documents'], function ($doc) use ($search_term, $type_filter, $status_filter) {
    $matchesSearch = empty($search_term) || 
                     str_contains(strtolower($doc['document_id']), strtolower($search_term)) || 
                     str_contains(strtolower($doc['issued_to']), strtolower($search_term));
    $matchesType = empty($type_filter) || $doc['type'] === $type_filter;
    $matchesStatus = empty($status_filter) || $doc['status'] === $status_filter;
    return $matchesSearch && $matchesType && $matchesStatus;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Generate Certificates</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <style>
        /* Pinanatili ang iyong orihinal na CSS styles */
        :root { --primary-color: #007bff; --secondary-color: #6c757d; --text-color: #333; --border-color: #ddd; --shadow-medium: 0 4px 6px rgba(0,0,0,0.1); }
        .doc-row { cursor: pointer; transition: background-color 0.2s; }
        .doc-row.expanded { background-color: #eef2f5; }
        .doc-row:hover:not(.expanded) { background: #f0f0f0; }
        .data-control-panel { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
        .search-and-filter-wrapper { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .search-input { padding: 10px 15px; border: 1px solid var(--border-color); border-radius: 6px; width: 250px; }
        .filter-select { padding: 10px 15px; border: 1px solid var(--border-color); border-radius: 6px; }
        .detail-row { display: none; }
        .detail-row.expanded { display: table-row; background: #fcfcfc; }
        .detail-container { padding: 20px 15px; }
        .detail-tabs { border-bottom: 2px solid var(--border-color); margin-bottom: 15px; display: flex; }
        .detail-tab { padding: 10px 15px; cursor: pointer; font-weight: 500; border-bottom: 2px solid transparent; }
        .detail-tab.active { color: var(--primary-color); border-bottom: 2px solid var(--primary-color); }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .detail-grid { display: flex; flex-wrap: wrap; gap: 20px; }
        .detail-box { flex: 1 1 300px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-color); }
        .special-status-bar { padding: 6px 12px; border-radius: 14px; font-weight: 600; font-size: 0.75rem; display: inline-block; text-transform: uppercase; }
        .badge-clearance { background-color: #e3f2fd; color: #1565c0; }
        .badge-residency { background-color: #e8f5e9; color: #2e7d32; }
        .badge-indigency { background-color: #fff3e0; color: #e65100; }
        .status-released { background-color: #e8f5e9; color: #2e7d32; }
        .status-pending { background-color: #fff8e1; color: #f57c00; }
        .action-btn { color: var(--secondary-color); font-size: 1rem; margin: 0 5px; cursor: pointer; }
        .action-btn:hover { color: var(--primary-color); }
        .delete-btn:hover { color: #dc3545; }
        .template-grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .template-item { padding: 25px; border: 1px solid var(--border-color); border-radius: 10px; text-align: center; background: white; transition: 0.3s; }
        .template-item:hover { box-shadow: var(--shadow-medium); transform: translateY(-3px); }
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; cursor: pointer; border: none; }
        .primary-btn { background: var(--primary-color); color: white; }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="logo">Happy Hallow Barangay System</div>
        <nav class="main-nav">
          <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="household.php">Households</a></li>
            <li><a href="residents.php">Residents</a></li>
            <li><a href="addnewresidents.php">Add Resident</a></li>
            <li><a href="deaths.php">Deaths</a></li>
            <li><a href="documents.php">Documents</a></li>
            <li><a href="staff_documents.php" class="active">Generate Certificates</a></li>
            <li><a href="health_tracking.php">Health Tracking</a></li>
          </ul>
        </nav>
      </aside>
    
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-right">
                <span class="user-info">Welcome, <?php echo $logged_in_username; ?></span>
                <a href="logout.php" class="btn logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="page-content">
            <h2 style="margin-bottom: 15px;">Document Templates</h2>
            
            <div class="card template-card" style="margin-bottom: 30px;">
                <div class="template-grid-container">
                    <div class="template-item">
                        <h4>Barangay Clearance</h4>
                        <p>Generate a general or specific clearance certificate.</p>
                        <a href="staff_generate_clearance.php" class="btn primary-btn"><i class="fas fa-plus"></i> Generate</a>
                    </div>
                    <div class="template-item">
                        <h4>Certificate of Residency</h4>
                        <p>Official proof of barangay residency.</p>
                        <a href="staff_generate_residency.php" class="btn primary-btn"><i class="fas fa-plus"></i> Generate</a>
                    </div>
                    <div class="template-item">
                        <h4>Certificate of Indigency</h4>
                        <p>Low-income status assistance certificate.</p>
                        <a href="staff_generate_indigency.php" class="btn primary-btn"><i class="fas fa-plus"></i> Generate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Row Expand/Collapse Logic
    document.querySelectorAll(".doc-row").forEach((row) => {
        row.addEventListener("click", (e) => {
            if (e.target.closest(".action-btn, a, button")) return;
            const docId = row.dataset.docId;
            const detailRow = document.querySelector(`.detail-row[data-detail-id="${docId}"]`);
            
            const isExpanded = row.classList.contains("expanded");
            
            // Close others
            document.querySelectorAll('.detail-row').forEach(d => d.classList.remove('expanded'));
            document.querySelectorAll('.doc-row').forEach(r => r.classList.remove('expanded'));

            if (!isExpanded) {
                row.classList.add("expanded");
                detailRow.classList.add('expanded');
            }
        });
    });

    // Tab Logic
    document.querySelectorAll(".detail-tab").forEach((tab) => {
        tab.addEventListener("click", (e) => {
            const container = e.target.closest(".detail-container");
            const tabName = e.target.dataset.tab;
            container.querySelectorAll(".detail-tab").forEach(t => t.classList.remove("active"));
            container.querySelectorAll(".tab-content").forEach(c => c.classList.remove("active"));
            e.target.classList.add("active");
            document.getElementById(tabName).classList.add("active");
        });
    });
});
</script>
</body>
</html>