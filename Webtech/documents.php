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
        return 'IMG-' . str_pad($row['id'] + 1, 3, '0', STR_PAD_LEFT);
    } else {
        return 'IMG-001';
    }
}

$success = isset($_GET['success']) ? true : false;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Documents - Happy Hallow Barangay System</title>
    <link rel="stylesheet" href="css/style.css" />
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
            <div class="alert success">Document uploaded successfully!</div>
          <?php endif; ?>

          <?php $next_doc_number = getNextDocNumber($conn); ?>

          <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr">
            <div class="card">
              <h3>Upload Document Image</h3>
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
                    <label for="docImage">Upload Image</label>
                    <input
                      type="file"
                      id="docImage"
                      name="docImage"
                      accept="image/*"
                      required
                    />
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
              <h3>🗄️ Stored Documents</h3>
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
                  $result = $conn->query("SELECT id, doc_number, resident_name, file_path, created_at FROM documents ORDER BY created_at DESC");
                  
                  if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['doc_number']) . "</td>
                                <td>" . htmlspecialchars($row['resident_name']) . "</td>
                                <td>Image</td>
                                <td>" . htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))) . "</td>
                                <td class='actions-cell'>
                                    <a href='" . htmlspecialchars($row['file_path']) . "' class='action-link view' target='_blank'>View</a>
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