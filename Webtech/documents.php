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
    $result = $conn->query("SELECT doc_number FROM documents WHERE doc_number LIKE 'IMG-%' ORDER BY id DESC LIMIT 1");
    
    if ($result && $row = $result->fetch_assoc()) {
        $last_number = intval(substr($row['doc_number'], 4)); 
        return 'IMG-' . str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
    } else {
        return 'IMG-001';
    }
}

$next_doc_number = getNextDocNumber($conn);
$status_success = $_SESSION['status_success'] ?? null;
unset($_SESSION['status_success']);

$status_error = $_SESSION['status_error'] ?? null;
unset($_SESSION['status_error']);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Documents - Happy Hallow Barangay System</title>
    <link rel="stylesheet" href="css/style.css" />
    <style>
        .autocomplete-dropdown {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 1px solid #ddd;
            width: calc(100% - 2px);
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
        }
        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .autocomplete-item:hover {
            background-color: #f0f0f0;
        }
        .input-group.has-autocomplete {
            position: relative;
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

          <?php if (isset($status_success)): ?>
            <div class="alert success"><?php echo htmlspecialchars($status_success); ?></div>
          <?php endif; ?>
          <?php if (isset($status_error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($status_error); ?></div>
          <?php endif; ?>

          <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr">
            <div class="card">
              <h3>Upload Document / Request Certificate</h3>
              <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">

                  <div class="input-group has-autocomplete" style="grid-column: 1 / -1">
                    <label for="residentSearch">Resident Name</label>
                    <input
                      type="text"
                      id="residentSearch"
                      name="resident_name" 
                      placeholder="Search and Select Resident"
                      autocomplete="off"
                      required
                    />
                    <input type="hidden" id="residentId" name="resident_id" required>
                    <div id="searchResults" class="autocomplete-dropdown"></div> 
                  </div>

                  <div class="input-group">
                    <label for="docType">Document Type</label>
                    <select id="docType" name="docType" required>
                      <option value="">Select Document Type</option>
                      <option value="Barangay Clearance">Barangay Clearance</option>
                      <option value="Barangay Certificate">Barangay Certificate</option>
                      <option value="Certificate of Residency">Certificate of Residency</option>
                      <option value="Other Upload">Other Upload (Image/PDF/Excel)</option>
                    </select>
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
                    <label for="docImage">Upload Document (Image/PDF/Excel)</label>
                    <input
                      type="file"
                      id="docImage"
                      name="docImage"
                      accept="image/*, application/pdf, .pdf, .xlsx, .xls"
                    />
                      <small id="fileRequirement" style="color: red; display:none;">
                          * File upload is required for 'Other Upload'.
                      </small>
                  </div>

                  <div class="input-group" style="grid-column: 1 / -1">
                    <label for="purpose">Purpose / Details</label>
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
              <h3>Stored Documents</h3>
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
                  $result = $conn->query("SELECT id, doc_number, resident_name, file_path, file_type, created_at, doc_type FROM documents ORDER BY created_at DESC");
                  
                  if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $file_type_display = $row['doc_type'];
                        
                        if (!empty($row['file_type'])) {
                            $file_type_display = strtoupper($row['file_type']) . ' Upload';
                        } elseif ($row['doc_type'] == 'Other Upload' && empty($row['file_path'])) {
                            $file_type_display = 'Missing File';
                        }
                        $view_link = !empty($row['file_path']) ? 
                            "<a href='" . htmlspecialchars($row['file_path']) . "' class='action-link view' target='_blank'>View</a>" :
                            "N/A";

                        echo "<tr>
                                <td>" . htmlspecialchars($row['doc_number']) . "</td>
                                <td>" . htmlspecialchars($row['resident_name']) . "</td>
                                <td>" . htmlspecialchars($file_type_display) . "</td>
                                <td>" . htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))) . "</td>
                                <td class='actions-cell'>" . $view_link . "</td>
                            </tr>";
                    }
                  } else {
                    echo "<tr><td colspan='5'>No stored documents found.</td></tr>";
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

      function setupResidentSearch() {
          const searchInput = document.getElementById('residentSearch');
          const residentIdInput = document.getElementById('residentId');
          const resultsContainer = document.getElementById('searchResults');
          let timeout = null;

          searchInput.addEventListener('input', function() {
              clearTimeout(timeout);
              const query = this.value.trim();

              if (query.length < 3) {
                  resultsContainer.innerHTML = '';
                  resultsContainer.style.display = 'none';
                  residentIdInput.value = ''; 
                  return;
              }

              timeout = setTimeout(() => {
                  fetch(`search_resident.php?q=${encodeURIComponent(query)}`)
                      .then(response => response.json())
                      .then(data => {
                          resultsContainer.innerHTML = '';
                          if (data.length > 0) {
                              data.forEach(resident => {
                                  const item = document.createElement('div');
                                  item.classList.add('autocomplete-item');
                                  item.textContent = resident.name;
                                  item.setAttribute('data-id', resident.id);
                                  item.setAttribute('data-name', resident.name);
                                  item.addEventListener('click', function() {
                                      searchInput.value = this.getAttribute('data-name');
                                      residentIdInput.value = this.getAttribute('data-id');
                                      resultsContainer.innerHTML = '';
                                      resultsContainer.style.display = 'none';
                                  });
                                  resultsContainer.appendChild(item);
                              });
                              resultsContainer.style.display = 'block';
                          } else {
                              resultsContainer.innerHTML = '<div>No residents found.</div>';
                              resultsContainer.style.display = 'block';
                              residentIdInput.value = '';
                          }
                      })
                      .catch(error => {
                          console.error('Error fetching resident data:', error);
                          resultsContainer.innerHTML = '<div>Error searching.</div>';
                      });
              }, 300); 
          });
          document.addEventListener('click', (e) => {
              if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                  resultsContainer.style.display = 'none';
              }
          });
      }
      function toggleFileRequirement() {
          const docType = document.getElementById('docType').value;
          const fileInput = document.getElementById('docImage');
          const fileRequirement = document.getElementById('fileRequirement');

          if (docType === 'Other Upload') {
              fileInput.required = true;
              fileRequirement.style.display = 'block';
          } else {
              fileInput.required = false;
              fileRequirement.style.display = 'none';
          }
      }
      
      window.onload = function () {
        setupLogout();
        setupResidentSearch(); 
        document.getElementById('docType').addEventListener('change', toggleFileRequirement);
        toggleFileRequirement(); 
      };
    </script>
  </body>
</html>