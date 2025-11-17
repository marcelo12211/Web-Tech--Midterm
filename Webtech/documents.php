<?php
include 'db_connect.php'; 

// Function to generate next document number
function getNextDocNumber($conn) {
    $result = $conn->query("SELECT id FROM documents ORDER BY id DESC LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        return 'IMG-' . str_pad($row['id'] + 1, 3, '0', STR_PAD_LEFT);
    } else {
        return 'IMG-001';
    }
}

// Handle success message after upload
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
            <li><a href="residents.php">Residents</a></li>
            <li><a href="addnewresidents.php">Add Resident</a></li>
            <li><a href="deaths.php">Deaths</a></li>
            <li><a href="documents.php" class="active">Documents</a></li>
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
          <h2>Document Upload & Storage</h2>

          <?php if ($success): ?>
            <div class="alert success">Document uploaded successfully!</div>
          <?php endif; ?>

          <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr">
            <!-- Upload Form -->
            <div class="card">
              <h3>Upload Document Image</h3>
              <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                  <div class="input-group" style="grid-column: 1 / -1">
                    <label for="residentSearch">Resident Name/ID</label>
                    <input
                      type="text"
                      id="residentSearch"
                      name="resident"
                      placeholder="Search and Select Resident"
                      required
                    />
                  </div>

                  <div class="input-group">
                    <label for="docNumber">Document Number (Auto)</label>
                    <input
                      type="text"
                      id="docNumber"
                      name="docNumber"
                      value="<?php echo getNextDocNumber($conn); ?>"
                      disabled
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

            <!-- Stored Documents Table -->
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
                  $result = $conn->query("SELECT * FROM documents ORDER BY created_at DESC");
                  while ($row = $result->fetch_assoc()) {
                      echo "<tr>
                        <td>IMG-" . str_pad($row['id'], 3, '0', STR_PAD_LEFT) . "</td>
                        <td>{$row['resident_name']}</td>
                        <td>Image</td>
                        <td>{$row['created_at']}</td>
                        <td class='actions-cell'>
                          <a href='{$row['file_path']}' class='action-link view' target='_blank'>View</a>
                        </td>
                      </tr>";
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
          userNameSpan.textContent = "Welcome, Guest";
        }
      }

      window.onload = function () {
        showUser();
        setupLogout();
      };
    </script>
  </body>
</html>
