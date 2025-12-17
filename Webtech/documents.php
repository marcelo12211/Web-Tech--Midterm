<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); 
header('Pragma: no-cache');   
header('Expires: 0');         
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';

include 'db_connect.php'; 

function getNextDocNumber($conn) {
    if (!$conn) {
        return 'IMG-ERR';
    }
    $result = $conn->query("SELECT id FROM documents ORDER BY id DESC LIMIT 1");
    
    if ($result && $row = $result->fetch_assoc()) {
        return 'DOC-' . str_pad($row['id'] + 1, 3, '0', STR_PAD_LEFT);
    } else {
        return 'DOC-001';
    }
}

function getFileIcon($fileType) {
    $type = strtolower($fileType);
    if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        return '🖼️';
    } elseif ($type === 'pdf') {
        return '📄';
    } elseif (in_array($type, ['doc', 'docx'])) {
        return '📝';
    } else {
        return '📎';
    }
}

$success = isset($_GET['success']) ? true : false;
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Documents - Happy Hallow Barangay System</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
      .file-type-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
      }
      .file-type-image { background: #dbeafe; color: #1e40af; }
      .file-type-pdf { background: #fee2e2; color: #991b1b; }
      .file-type-doc { background: #ddd6fe; color: #5b21b6; }
      .file-type-other { background: #e5e7eb; color: #374151; }
      
      .alert {
        padding: 12px 20px;
        margin-bottom: 20px;
        border-radius: 4px;
        font-weight: 500;
      }
      .alert.success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
      }
      .alert.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
      }
      
      .file-info {
        font-size: 12px;
        color: #6b7280;
        margin-top: 5px;
      }
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
                <li><a href="documents.php" class="active">Documents</a></li>
                <li><a href="staff_documents.php">Generate Certificates</a></li>
            </ul>
        </nav>
    </aside>	

      <div class="main-content">
        <header class="topbar">
          <div class="topbar-right">
            <span id="userName" class="user-info">Welcome, <?php echo htmlspecialchars($logged_in_username); ?></span>
            <button id="logoutBtn" class="btn logout-btn">Logout</button>
          </div>
        </header>

        <main class="page-content">
          <h2>Document Upload & Storage</h2>

          <?php if ($success): ?>
            <div class="alert success">✓ Document uploaded successfully!</div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert error">✗ <?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <?php $next_doc_number = getNextDocNumber($conn); ?>

          <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr">
            <div class="card">
              <h3>Upload Document</h3>
              <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                  <div class="input-group" style="grid-column: 1 / -1">
                    <label for="residentSearch">Resident Name/ID</label>
                    <input
                      type="text"
                      id="residentSearch"
                      name="resident_name" 
                      placeholder="Search and Select Resident"
                      required
                    />
                  </div>

                  <div class="input-group">
                    <label for="docNumberDisplay">Document Number (Auto)</label>
                    <input
                      type="text"
                      id="docNumberDisplay"
                      value="<?php echo htmlspecialchars($next_doc_number); ?>"
                      readonly 
                    />
                    <input
                      type="hidden"
                      name="docNumber"
                      value="<?php echo htmlspecialchars($next_doc_number); ?>"
                    />
                  </div>

                  <div class="input-group" style="grid-column: 1 / -1">
                    <label for="docFile">Upload File</label>
                    <input
                      type="file"
                      id="docFile"
                      name="docFile"
                      accept="image/*,.pdf,.doc,.docx"
                      required
                    />
                    <div class="file-info">
                      Accepted formats: Images (JPG, PNG, GIF, WEBP), PDF, Word Documents (DOC, DOCX)
                      <br>Maximum file size: 10MB
                    </div>
                  </div>

                  <div class="input-group" style="grid-column: 1 / -1">
                    <label for="purpose">Purpose of Document</label>
                    <textarea id="purpose" name="purpose" rows="3" required></textarea>
                  </div>
                </div>

                <div class="form-actions">
                  <button type="submit" class="btn primary-btn create">
                    Upload Document
                  </button>
                </div>
              </form>
            </div>

            <div class="card">
              <h3>📁 Stored Documents</h3>
              <div class="table-responsive">
                <table>
                  <thead>
                    <tr>
                      <th>Doc. No.</th>
                      <th>Resident Name</th>
                      <th>Type</th>
                      <th>Date Uploaded</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="documentStorageTable">
                  <?php
                  $result = $conn->query("SELECT id, doc_number, resident_name, file_path, file_type, created_at FROM documents ORDER BY created_at DESC");
                  
                  if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $fileType = strtolower($row['file_type']);
                        $icon = getFileIcon($fileType);
                        
                        $badgeClass = 'file-type-other';
                        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $badgeClass = 'file-type-image';
                        } elseif ($fileType === 'pdf') {
                            $badgeClass = 'file-type-pdf';
                        } elseif (in_array($fileType, ['doc', 'docx'])) {
                            $badgeClass = 'file-type-doc';
                        }
                        
                        echo "<tr>
                                <td>" . htmlspecialchars($row['doc_number']) . "</td>
                                <td>" . htmlspecialchars($row['resident_name']) . "</td>
                                <td>
                                    <span class='file-type-badge $badgeClass'>
                                        $icon " . strtoupper($fileType) . "
                                    </span>
                                </td>
                                <td>" . htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))) . "</td>
                                <td class='actions-cell'>
                                    <a href='" . htmlspecialchars($row['file_path']) . "' class='action-link view' target='_blank'>View/Download</a>
                                </td>
                            </tr>";
                    }
                  } else {
                      echo "<tr><td colspan='5'>Error loading documents: " . $conn->error . "</td></tr>";
                  }
                  ?>
                  </tbody>
                </table>
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
          window.location.href = "logout.php";
        });
      }
      window.onload = function () {
        setupLogout();
      };
    </script>
  </body>
</html>