<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Deaths and Records - Happy Hallow Barangay System</title>
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
                <li><a href="deaths.php" class="active" >Deaths</a></li>
                <li><a href="documents.php">Documents</a></li>
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
          <div class="directory-header">
            <h2>Death Records</h2>
            <button class="btn primary-btn" onclick="openDeathRecordModal()">
              + Add New Record
            </button>
          </div>

          <div class="card filter-section">
            <h3>Death Statistics and Filter</h3>
            <div class="dashboard-grid">
              <div class="stat-card report-stat">
                <p class="stat-label">Total Death Records</p>
                <p class="stat-value"><span>[98]</span></p>
              </div>

              <div
                class="stat-card report-stat"
                style="border-left-color: #dc3545"
              >
                <p class="stat-label">Cause: Kidney Failure</p>
                <p class="stat-value"><span>[15]</span></p>
              </div>

              <div
                class="stat-card report-stat"
                style="border-left-color: #ffc107"
              >
                <p class="stat-label">Cause: Old Age</p>
                <p class="stat-value"><span>[22]</span></p>
              </div>
            </div>

            <div class="filter-dropdowns">
              <div class="input-group">
                <label for="filterCause">Cause of Death</label>
                <select id="filterCause">
                  <option value="">All Causes</option>
                  <option value="kidney">Kidney Failure</option>
                  <option value="oldage">Old Age (Natural)</option>
                  <option value="accident">Accident</option>
                </select>
              </div>
              <div class="input-group">
                <label for="filterYear">Year of Death</label>
                <input
                  type="number"
                  id="filterYear"
                  min="1900"
                  max="2100"
                  placeholder="e.g., 2024"
                />
              </div>
              <div class="input-group">
                <label>&nbsp;</label>
                <button class="btn primary-btn" style="width: 100%">
                  Filter
                </button>
              </div>
            </div>
          </div>

          <div class="card data-table-card">
            <h3>Death Record Details</h3>
            <div class="search-results-info">
              Displaying 1-10 of 98 records.
            </div>
            <div class="table-responsive">
              <table>
                <thead>
                  <tr>
                    <th>Rec. No.</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Cause of Death</th>
                    <th>Date of Death</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="deathRecordTable">
                  <tr>
                    <td>D-001</td>
                    <td>Dela Cruz, Juan M.</td>
                    <td>78</td>
                    <td>Old Age (Natural)</td>
                    <td>2024-03-15</td>
                    <td class="actions-cell">
                      <a
                        href="#"
                        class="action-link view"
                        onclick="viewDetails(this)"
                        >View Details</a
                      >
                    </td>
                  </tr>
                  <tr>
                    <td>D-002</td>
                    <td>Santos, Maria F.</td>
                    <td>65</td>
                    <td>Kidney Failure</td>
                    <td>2024-02-28</td>
                    <td class="actions-cell">
                      <a
                        href="#"
                        class="action-link view"
                        onclick="viewDetails(this)"
                        >View Details</a
                      >
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </main>
      </div>
    </div>

    <div id="deathRecordModal" class="modal-backdrop">
      <div class="modal-content-wrapper">
        <div class="form-modal-card">
          <a class="close-btn" onclick="closeModal('deathRecordModal')"
            >&times;</a
          >
          <h2 class="form-title">Register Death Record</h2>

          <form>
            <div class="form-section">
              <h3>Basic Information</h3>
              <div class="form-grid">
                <div class="input-group">
                  <label for="recordNumber"
                    >Record Number (Auto-Generated)</label
                  >
                  <input
                    type="text"
                    id="recordNumber"
                    value="D-[AUTO-GENERATE]"
                    disabled
                  />
                </div>
                <div class="input-group">
                  <label for="residentName">Full Name</label>
                  <input type="text" id="residentName" required />
                </div>
                <div class="input-group">
                  <label for="residentAge">Age</label>
                  <input type="number" id="residentAge" min="1" required />
                </div>
                <div class="input-group">
                  <label for="dateOfDeath">Date of Death</label>
                  <input type="date" id="dateOfDeath" required />
                </div>
                <div class="input-group">
                  <label for="causeOfDeath">Cause of Death</label>
                  <select id="causeOfDeath" required>
                    <option value="">Select Cause</option>
                    <option value="kidney">Kidney Failure</option>
                    <option value="oldage">Old Age (Natural)</option>
                    <option value="accident">Accident</option>
                    <option value="other">Other</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-section">
              <h3>Special Status</h3>
              <div class="form-grid">
                <div class="input-group">
                  <label for="isPwd">Person with Disability (PWD)?</label>
                  <select id="isPwd" onchange="toggleIdFields()">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                  </select>
                </div>
                <div class="input-group">
                  <label for="isSeniorCitizen">Senior Citizen?</label>
                  <select id="isSeniorCitizen" onchange="toggleIdFields()">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                  </select>
                </div>
              </div>
              <div
                id="pwdIdField"
                class="input-group"
                style="display: none; margin-top: 20px"
              >
                <label for="pwdId">PWD ID Number</label>
                <input type="text" id="pwdId" />
              </div>
              <div
                id="seniorCitizenIdFields"
                class="form-grid"
                style="display: none; margin-top: 20px"
              >
                <div class="input-group">
                  <label for="ncscRrn">NCSC-RRN Number</label>
                  <input type="text" id="ncscRrn" />
                </div>
                <div class="input-group">
                  <label for="oscaId">OSCA ID Number</label>
                  <input type="text" id="oscaId" />
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button
                type="button"
                class="btn secondary"
                onclick="closeModal('deathRecordModal')"
              >
                Cancel
              </button>
              <button type="submit" class="btn primary-btn create">
                Save Record
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      function toggleIdFields() {
        const isPwd = document.getElementById("isPwd").value === "yes";
        const isSeniorCitizen =
          document.getElementById("isSeniorCitizen").value === "yes";

        document.getElementById("pwdIdField").style.display = isPwd
          ? "flex"
          : "none";
        document.getElementById("seniorCitizenIdFields").style.display =
          isSeniorCitizen ? "grid" : "none";
      }

      function openDeathRecordModal() {
        document.getElementById("deathRecordModal").classList.add("show");
      }

      function closeModal(modalId) {
        document.getElementById(modalId).classList.remove("show");
      }

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
          userNameSpan.textContent = `Welcome, Guest`;
        }
      }

      window.onload = function () {
        showUser();
        setupLogout();
      };
    </script>
  </body>
</html>