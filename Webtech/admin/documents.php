<?php
$documents = [
    [
        'id' => 'BC-25-00123',
        'type' => 'Clearance',
        'issued_to' => 'Dela Cruz, Juan C.',
        'date_issued' => '2025-12-09',
        'status' => 'Released'
    ],
    [
        'id' => 'CR-25-00045',
        'type' => 'Residency',
        'issued_to' => 'Santos, Maria F.',
        'date_issued' => '2025-12-05',
        'status' => 'Released'
    ],
    [
        'id' => 'CR-25-00088',
        'type' => 'Residency',
        'issued_to' => 'Ramos, Lita S.',
        'date_issued' => '2025-11-20',
        'status' => 'Pending Pickup'
    ],
];

function get_status_class($status) {
    return match ($status) {
        'Released' => 'badge-success',
        'Pending Pickup' => 'badge-warning',
        default => ''
    };
}

function get_type_class($type) {
    return match ($type) {
        'Clearance' => 'badge-info',
        'Residency' => 'badge-primary',
        'Indigency' => 'badge-warning',
        default => ''
    };
}

/* =========================
   FILTER LOGIC
   ========================= */

$search = strtolower(trim($_GET['search'] ?? ''));
$typeFilter = $_GET['type'] ?? '';

$filteredDocuments = array_filter($documents, function ($doc) use ($search, $typeFilter) {

    $matchesSearch =
        empty($search) ||
        str_contains(strtolower($doc['id']), $search) ||
        str_contains(strtolower($doc['issued_to']), $search);

    $matchesType =
        empty($typeFilter) ||
        $doc['type'] === $typeFilter;

    return $matchesSearch && $matchesType;
});

$current_user = "Admin";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Documents</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="app-container">
    <aside class="sidebar">
        <div class="logo">Happy Hallow<br>Barangay System</div>
        <nav class="main-nav">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="residents.php">Manage Residents</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a class="active" href="documents.php">Documents</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <span class="user-info">Welcome, <?= htmlspecialchars($current_user) ?></span>
        </header>

        <section class="page-content">
            <div class="page-header-title">
                <h2>Barangay Documents & Certificates</h2>
                <a href="export_documents.php" class="btn success-btn">Export Report</a>
            </div>

            <!-- DOCUMENT TEMPLATES -->
            <div class="card document-templates-card">
                <h3>Document Templates</h3>
                <p>Select a template to generate a document.</p>

                <div class="document-grid-container">
                    <div class="template-card">
                        <h4>Barangay Clearance</h4>
                        <p>Residency and clearance certification.</p>
                        <a href="generate_clearance.php" class="btn primary-btn">Generate</a>
                    </div>

                    <div class="template-card">
                        <h4>Certificate of Residency</h4>
                        <p>Proof of residency.</p>
                        <a href="generate_residency.php" class="btn outline-btn">Generate</a>
                    </div>

                    <div class="template-card">
                        <h4>Certificate of Indigency</h4>
                        <p>Low-income assistance certificate.</p>
                        <a href="generate_indigency.php" class="btn info-btn-template">Generate</a>
                    </div>
                </div>
            </div>

            <!-- GENERATED DOCUMENTS TABLE -->
            <div class="card data-table-card">
                <h3>Generated Documents</h3>

                <!-- FILTER FORM -->
                <form method="GET" class="filter-group-horizontal" style="margin-bottom:20px;">
                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="Search by name or ID"
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    >

                    <select name="type" class="filter-select">
                        <option value="">All Types</option>
                        <option value="Clearance" <?= (($typeFilter ?? '') === 'Clearance') ? 'selected' : '' ?>>Clearance</option>
                        <option value="Residency" <?= (($typeFilter ?? '') === 'Residency') ? 'selected' : '' ?>>Residency</option>
                        <option value="Indigency" <?= (($typeFilter ?? '') === 'Indigency') ? 'selected' : '' ?>>Indigency</option>
                    </select>

                    <button type="submit" class="btn primary-btn">
                        Filter
                    </button>
                </form>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Doc ID</th>
                                <th>Type</th>
                                <th>Issued To</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($filteredDocuments)): ?>
                            <?php foreach ($filteredDocuments as $doc): ?>
                                <tr>
                                    <td><?= htmlspecialchars($doc['id']) ?></td>
                                    <td>
                                        <span class="category-tag <?= get_type_class($doc['type']) ?>">
                                            <?= htmlspecialchars($doc['type']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($doc['issued_to']) ?></td>
                                    <td><?= htmlspecialchars($doc['date_issued']) ?></td>
                                    <td>
                                        <span class="status-badge <?= get_status_class($doc['status']) ?>">
                                            <?= htmlspecialchars($doc['status']) ?>
                                        </span>
                                    </td>
                                    <td class="action-cell">
                                        <a class="action-btn text-link"><i class="fas fa-eye"></i> View</a>
                                        <a class="action-btn delete-text"><i class="fas fa-archive"></i> Archive</a>

                                        <?php if ($doc['status'] === 'Pending Pickup'): ?>
                                            <a class="success-btn-small">
                                                <i class="fas fa-check"></i> Release
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;color:#6c757d;">
                                    No matching documents found.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
    </main>
</div>
</body>
</html>
