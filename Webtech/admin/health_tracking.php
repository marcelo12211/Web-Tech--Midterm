<?php
session_start();
include '../db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';

// --- 1. FETCH RESIDENTS FOR AUTOCOMPLETE ---
$residents_list = [];
$res_query = "SELECT surname, first_name, middle_name, suffix FROM residents ORDER BY surname ASC";
$res_result = mysqli_query($conn, $res_query);
if ($res_result) {
    while ($row = mysqli_fetch_assoc($res_result)) {
        $full_name = $row['surname'] . ", " . $row['first_name'];
        if (!empty($row['middle_name'])) {
            $full_name .= " " . substr($row['middle_name'], 0, 1) . ".";
        }
        if (!empty($row['suffix'])) {
            $full_name .= " " . $row['suffix'];
        }
        $residents_list[] = $full_name;
    }
}

// --- 2. FETCH MAINTENANCE DATA ---
$maintenance_query = "SELECT * FROM maintenance_profiles ORDER BY id DESC";
$maintenance_result = mysqli_query($conn, $maintenance_query);

// --- 3. FETCH VACCINATION DATA ---
$vaccine_query = "SELECT * FROM vaccination_records ORDER BY id DESC";
$vaccine_result = mysqli_query($conn, $vaccine_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Health Tracking - Happy Hallow System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        :root {
            --primary-color: #226b8dff;
            --primary-dark: #1a526b;
            --secondary-color: #5f6368;
            --warning-color: #fbbc04;
            --danger-color: #ea4335;
            --background-color: #f8f9fa;
            --sidebar-bg: #212121;
            --text-color: #202124;
            --text-light: #5f6368;
            --border-color: #dadce0;
            --radius: 10px;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Roboto", Arial, sans-serif; background: var(--background-color); color: var(--text-color); }
        .app-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: var(--sidebar-bg); color: white; }
        .logo { padding: 25px; text-align: center; font-weight: 700; font-size: 1.15rem; line-height: 1.3; }
        .main-nav ul { list-style: none; padding: 0; margin: 0; }
        .main-nav a { display: block; padding: 14px 20px; color: #bdc1c6; text-decoration: none; }
        .main-nav a:hover, .main-nav a.active { background: var(--primary-dark); color: white; }
        
        .main-content { flex: 1; }
        .topbar { background: white; padding: 15px 30px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: flex-end; align-items: center; }
        .user-info { margin-right: 15px; color: var(--text-light); }
        .logout-btn { padding: 8px 15px; border: 1px solid var(--border-color); background: transparent; color: var(--text-color); border-radius: 6px; text-decoration: none; font-weight: 500; }
        
        .page-content { padding: 30px; }
        .detail-tabs { border-bottom: 2px solid var(--border-color); margin-bottom: 25px; display: flex; }
        .detail-tab { padding: 12px 25px; cursor: pointer; font-weight: 500; color: var(--text-light); border-bottom: 2px solid transparent; transition: 0.3s; }
        .detail-tab:hover { color: var(--primary-color); }
        .detail-tab.active { color: var(--primary-color); border-bottom: 2px solid var(--primary-color); }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .card { background: white; border-radius: var(--radius); box-shadow: var(--shadow); padding: 25px; margin-bottom: 20px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        .btn-add { background-color: var(--primary-color); color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; }
        
        table { width: 100%; border-collapse: collapse; }
        thead { background: #eef2f5; color: var(--text-light); font-size: 0.9rem; text-align: left; }
        th, td { padding: 14px; border-bottom: 1px solid var(--border-color); }

        .special-status-bar { padding: 5px 12px; border-radius: 4px; font-weight: 600; font-size: 0.85rem; display: inline-block; }
        .badge-success { background-color: #e6f4ea; color: #1e7e34; }
        .badge-info { background-color: #eaf6fa; color: #008cba; }
        
        .action-btns { display: flex; gap: 12px; }
        .edit-icon { color: var(--primary-color); cursor: pointer; }
        .delete-icon { color: var(--danger-color); cursor: pointer; }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .modal-content { background-color: white; margin: 5% auto; padding: 30px; border-radius: var(--radius); width: 450px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; }
        .btn-secondary { background: #e0e0e0; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; }
        .btn-save { background: var(--primary-color); color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; }
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
                <li><a href="documents.php">Documents</a></li>
                <li><a href="health_tracking.php" class="active">Health Tracking</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span class="user-info">Welcome, <?php echo $logged_in_username; ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
        
        <div class="page-content">
            <h2 style="margin-bottom: 20px;">Health & Vaccination Tracking</h2>

            <div class="detail-tabs">
                <div class="detail-tab active" data-tab="maintenance-tab">Maintenance Monitoring</div>
                <div class="detail-tab" data-tab="vaccination-tab">Vaccination Program</div>
            </div>

            <div id="maintenance-tab" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h4 style="margin:0; color:var(--primary-color);">Maintenance Medicine Profiles</h4>
                        <button class="btn-add" onclick="prepareAddMaintenance()">
                            <i class="fas fa-plus"></i> Add Profile
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Resident Name</th>
                                <th>Condition</th>
                                <th>Medicine</th>
                                <th>Last Checkup</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($m_row = mysqli_fetch_assoc($maintenance_result)): 
                                $badge = ($m_row['status'] == 'For Refill') ? 'badge-info' : 'badge-success';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m_row['resident_name']); ?></td>
                                <td><?php echo htmlspecialchars($m_row['medical_condition']); ?></td>
                                <td><?php echo htmlspecialchars($m_row['medicine']); ?></td>
                                <td><?php echo $m_row['last_checkup']; ?></td>
                                <td><span class="special-status-bar <?php echo $badge; ?>"><?php echo $m_row['status']; ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <i class="fas fa-edit edit-icon" onclick='editMaintenance(<?php echo json_encode($m_row); ?>)'></i>
                                        <i class="fas fa-trash delete-icon" onclick="deleteRecord(<?php echo $m_row['id']; ?>, 'maintenance')"></i>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="vaccination-tab" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h4 style="margin:0; color:var(--primary-color);">Vaccination Records</h4>
                        <button class="btn-add" onclick="prepareAddVaccine()">
                            <i class="fas fa-plus"></i> Add Record
                        </button>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Resident Name</th>
                                <th>Vaccine Type</th>
                                <th>Dose</th>
                                <th>Date Administered</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($v_row = mysqli_fetch_assoc($vaccine_result)): 
                                $badge = ($v_row['status'] == 'Completed') ? 'badge-success' : 'badge-info';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($v_row['resident_name']); ?></td>
                                <td><?php echo htmlspecialchars($v_row['vaccine_type']); ?></td>
                                <td><?php echo htmlspecialchars($v_row['dose']); ?></td>
                                <td><?php echo $v_row['date_administered']; ?></td>
                                <td><span class="special-status-bar <?php echo $badge; ?>"><?php echo $v_row['status']; ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <i class="fas fa-edit edit-icon" onclick='editVaccine(<?php echo json_encode($v_row); ?>)'></i>
                                        <i class="fas fa-trash delete-icon" onclick="deleteRecord(<?php echo $v_row['id']; ?>, 'vaccine')"></i>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="maintenanceModal" class="modal">
    <div class="modal-content">
        <h3 id="maintenanceModalTitle">Add Maintenance Profile</h3>
        <form action="save_health.php" method="POST">
            <input type="hidden" name="form_type" value="maintenance">
            <input type="hidden" name="record_id" id="m_record_id">
            <div class="form-group">
                <label>Resident Full Name</label>
                <input type="text" name="resident_name" id="m_res_name" list="resList" placeholder="Search name..." required>
            </div>
            <div class="form-group">
                <label>Medical Condition</label>
                <input type="text" name="medical_condition" id="m_condition" required>
            </div>
            <div class="form-group">
                <label>Medicine</label>
                <input type="text" name="medicine" id="m_medicine" required>
            </div>
            <div class="form-group">
                <label>Last Checkup</label>
                <input type="date" name="last_checkup" id="m_checkup" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="m_status">
                    <option value="Active Intake">Active Intake</option>
                    <option value="For Refill">For Refill</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModals()">Cancel</button>
                <button type="submit" class="btn-save">Save Profile</button>
            </div>
        </form>
    </div>
</div>

<div id="vaccineModal" class="modal">
    <div class="modal-content">
        <h3 id="vaccineModalTitle">Add Vaccination Record</h3>
        <form action="save_health.php" method="POST">
            <input type="hidden" name="form_type" value="vaccine">
            <input type="hidden" name="record_id" id="v_record_id">
            <div class="form-group">
                <label>Resident Full Name</label>
                <input type="text" name="resident_name" id="v_res_name" list="resList" placeholder="Search name..." required>
            </div>
            <div class="form-group">
                <label>Vaccine Type</label>
                <input type="text" name="vaccine_type" id="v_type" required>
            </div>
            <div class="form-group">
                <label>Dose</label>
                <input type="text" name="dose" id="v_dose" placeholder="e.g. 1st Dose" required>
            </div>
            <div class="form-group">
                <label>Date Administered</label>
                <input type="date" name="date_administered" id="v_date" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="v_status">
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModals()">Cancel</button>
                <button type="submit" class="btn-save">Save Record</button>
            </div>
        </form>
    </div>
</div>

<datalist id="resList">
    <?php foreach ($residents_list as $name): ?>
        <option value="<?php echo htmlspecialchars($name); ?>">
    <?php endforeach; ?>
</datalist>

<script>
    // Tab switching logic
    document.querySelectorAll('.detail-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-tab');
            document.querySelectorAll('.detail-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });

    // Modal Control Functions
    function closeModals() {
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
    }

    // --- MAINTENANCE LOGIC ---
    function prepareAddMaintenance() {
        document.getElementById('maintenanceModalTitle').innerText = "Add Maintenance Profile";
        document.getElementById('m_record_id').value = "";
        document.getElementById('m_res_name').value = "";
        document.getElementById('m_condition').value = "";
        document.getElementById('m_medicine').value = "";
        document.getElementById('m_checkup').value = "";
        document.getElementById('maintenanceModal').style.display = 'block';
    }

    function editMaintenance(data) {
        document.getElementById('maintenanceModalTitle').innerText = "Edit Maintenance Profile";
        document.getElementById('m_record_id').value = data.id;
        document.getElementById('m_res_name').value = data.resident_name;
        document.getElementById('m_condition').value = data.medical_condition;
        document.getElementById('m_medicine').value = data.medicine;
        document.getElementById('m_checkup').value = data.last_checkup;
        document.getElementById('m_status').value = data.status;
        document.getElementById('maintenanceModal').style.display = 'block';
    }

    // --- VACCINE LOGIC ---
    function prepareAddVaccine() {
        document.getElementById('vaccineModalTitle').innerText = "Add Vaccination Record";
        document.getElementById('v_record_id').value = "";
        document.getElementById('v_res_name').value = "";
        document.getElementById('v_type').value = "";
        document.getElementById('v_dose').value = "";
        document.getElementById('v_date').value = "";
        document.getElementById('vaccineModal').style.display = 'block';
    }

    function editVaccine(data) {
        document.getElementById('vaccineModalTitle').innerText = "Edit Vaccination Record";
        document.getElementById('v_record_id').value = data.id;
        document.getElementById('v_res_name').value = data.resident_name;
        document.getElementById('v_type').value = data.vaccine_type;
        document.getElementById('v_dose').value = data.dose;
        document.getElementById('v_date').value = data.date_administered;
        document.getElementById('v_status').value = data.status;
        document.getElementById('vaccineModal').style.display = 'block';
    }

    // --- DELETE LOGIC ---
    function deleteRecord(id, type) {
        if (confirm("Are you sure you want to delete this record?")) {
            window.location.href = `save_health.php?delete_id=${id}&type=${type}`;
        }
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            closeModals();
        }
    }
</script>
</body>
</html>