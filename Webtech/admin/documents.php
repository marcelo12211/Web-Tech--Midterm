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
function get_status_tag_class($status) {
    switch ($status) {
        case 'Released':
            return 'badge-yes';
        case 'Pending Pickup':
            return 'badge-no';
        case 'Clearance':
            return 'badge-info';
        case 'Residency':
            return 'badge-info'; 
        default:
            return '';
    }
}

function get_residency_style($type) {
    if ($type === 'Residency') {
        return 'style="background-color: #cce5ff; color: #004085"';
    }
    return '';
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Documents</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link rel="stylesheet" href="css/style.css" />
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
            <span class="user-info">Welcome, <?php echo "Admin";?></span>
          </div>
        </div>

        <div class="page-content">
          <div class="page-header-title">
            <h2>Barangay Documents & Certificates</h2>
            <a
              href="#"
              class="btn primary-btn add-new-floating-btn"
              style="
                background-color: var(--success-color);
                border-color: #1e7e34;
              "
            >
              Export Report
            </a>
          </div>

          <div class="card data-table-card" style="margin-bottom: 30px">
            <h3>Document Templates</h3>
            <p style="margin-bottom: 20px; color: var(--secondary-color)">
              Select a template below to generate a new document for a resident.
            </p>

            <div class="document-grid-container">
              <div class="template-card">
                <h4>Barangay Clearance</h4>
                <p>Certificate for local residency and clearance purposes.</p>
                <a href="generate_clearance.php" class="btn primary-btn full-width-btn">Generate</a>
              </div>

              <div class="template-card">
                <h4>Certificate of Residency</h4>
                <p>Proof of residency within Barangay Happy Hallow.</p>
                <a
                  href="generate_residency.php"
                  class="btn primary-btn full-width-btn"
                  style="
                    background-color: var(--info-color);
                    border-color: #0d6efd;
                  "
                  >Generate</a
                >
              </div>
              
              </div>
          </div>

          <br />

          <div class="card data-table-card">
            <h3>Generated Document History</h3>

            <div
              class="data-control-panel-mini"
              style="margin-bottom: 20px; padding: 0; box-shadow: none"
            >
              <div
                class="filter-group-horizontal"
                style="align-items: center; justify-content: space-between"
              >
                <input
                  type="text"
                  placeholder="Search by Requester or ID..."
                  class="search-input-full"
                  style="width: 300px"
                  name="search"
                />
                <div class="filter-wrapper" style="flex-basis: 200px">
                  <select class="filter-select" name="doc_type_filter">
                    <option value="">-- Document Type --</option>
                    <option value="Clearance">Clearance</option>
                    <option value="Residency">Residency</option>
                  </select>
                </div>
              </div>
            </div>

            <table>
              <thead>
                <tr>
                  <th>Doc ID</th>
                  <th>Type</th>
                  <th>Issued To</th>
                  <th>Date Issued</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($documents)): ?>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($doc['id']); ?></td>
                            <td>
                                <span 
                                    class="category-tag <?php echo get_status_tag_class($doc['type']); ?>" 
                                    <?php echo get_residency_style($doc['type']); ?>
                                >
                                    <?php echo htmlspecialchars($doc['type']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($doc['issued_to']); ?></td>
                            <td><?php echo htmlspecialchars($doc['date_issued']); ?></td>
                            <td>
                                <span class="category-tag <?php echo get_status_tag_class($doc['status']); ?>">
                                    <?php echo htmlspecialchars($doc['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_document.php?id=<?php echo urlencode($doc['id']); ?>" class="action-btn edit-btn" title="View PDF">View</a>
                                
                                <a href="archive_document.php?id=<?php echo urlencode($doc['id']); ?>" class="action-btn delete-btn" title="Archive">Archive</a>

                                <?php if ($doc['status'] === 'Pending Pickup'): ?>
                                    <a
                                        href="release_document.php?id=<?php echo urlencode($doc['id']); ?>"
                                        class="action-btn primary-btn"
                                        style="
                                            background-color: var(--success-color);
                                            border-color: #1e7e34;
                                        "
                                        title="Mark as Released"
                                    >Release</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #6c757d;">No documents found.</td>
                    </tr>
                <?php endif; ?>
              </tbody>
            </table>
            
            </div>
        </div>
      </div>
    </div>
  </body>
</html>