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

// --- DELETE LOGIC ---
if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $id = intval($_GET['delete_id']);
    $table = ($_GET['type'] == 'maintenance') ? 'maintenance_profiles' : 'vaccination_records';
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: health_tracking.php?deleted=1");
        exit();
    }
}

if (isset($_POST['save_record'])) {
    $type = $_POST['record_type'];
    $name = $_POST['resident_name'];
    $health_date = $_POST['last_checkup']; 
    $status = $_POST['status'];
    $record_id = $_POST['record_id']; 

    if ($type == 'maintenance') {
        $cond = $_POST['medical_condition'];
        $med = $_POST['medicine'];
        if (!empty($record_id)) {
            $stmt = $conn->prepare("UPDATE maintenance_profiles SET resident_name=?, medical_condition=?, medicine=?, last_checkup=?, status=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $cond, $med, $health_date, $status, $record_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO maintenance_profiles (resident_name, medical_condition, medicine, last_checkup, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $cond, $med, $health_date, $status);
        }
    } else {
        $v_type = $_POST['vaccine_type'];
        $dose = $_POST['dose'];
        if (!empty($record_id)) {
            $stmt = $conn->prepare("UPDATE vaccination_records SET resident_name=?, vaccine_type=?, dose=?, date_administered=?, status=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $v_type, $dose, $health_date, $status, $record_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO vaccination_records (resident_name, vaccine_type, dose, date_administered, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $v_type, $dose, $health_date, $status);
        }
    }

    if ($stmt->execute()) {
        header("Location: health_tracking.php?success=1");
        exit();
    }
}

$maintenance_result = $conn->query("SELECT * FROM maintenance_profiles ORDER BY id DESC");
$vaccine_result = $conn->query("SELECT * FROM vaccination_records ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Health Tracking - Happy Hallow System</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        :root { --primary-blue: #007bff; --hover-blue: #0056b3; --bg-light: #f8f9fa; --border-color: #e0e0e0; }
        html, body { height: 100vh; margin: 0; overflow: hidden; background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; }
        .app-container { display: flex; height: 100vh; }
        .sidebar { width: 260px; background: #1e2229; color: #fff; flex-shrink: 0; }
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .page-content { padding: 30px; flex: 1; overflow-y: auto; }
        .card { background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); padding: 30px; margin-bottom: 25px; }
        .logout-btn { 
            background: transparent; 
            color: #333 !important; 
            padding: 0; 
            text-decoration: underline; 
            font-weight: 600; 
            font-size: 14px;
        }
        .logout-btn:hover { color: #000 !important; }
        .add-btn { background-color: var(--primary-blue); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600; }
        .add-btn:hover { background-color: var(--hover-blue); }
        .data-table-container { border-radius: 10px; overflow: hidden; border: 1px solid var(--border-color); margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        thead th { background-color: #fcfcfc; color: #666; font-size: 12px; padding: 18px 15px; border-bottom: 2px solid var(--border-color); text-align: left; text-transform: uppercase; }
        tbody td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        
        .status-badge { padding: 5px 12px; background: #e7f3ff; color: #007bff; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .header-flex h2 { color: #0056b3; font-weight: 700; border-bottom: 3px solid #007bff; padding-bottom: 5px; }
        
        .action-icon { border:none; background:none; cursor:pointer; font-size: 16px; transition: 0.2s; text-decoration: none; }
        .edit-icon { color: #007bff; }
        .delete-icon { color: #dc3545; margin-left: 10px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fff; margin: 2% auto; padding: 25px; border-radius: 12px; width: 450px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .suggestions-list { position: absolute; background: white; border: 1px solid #ddd; z-index: 2000; width: 100%; max-height: 150px; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .suggestion-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="logo" style="padding: 20px; font-weight: bold; font-size: 18px;">Happy Hallow Barangay System</div>
            <nav class="main-nav">
                <ul style="list-style: none; padding: 0;">
                    <li><a href="index.php" style="color: white; text-decoration: none; display: block; padding: 15px 20px;">Dashboard</a></li>
                    <li><a href="household.php" style="color: white; text-decoration: none; display: block; padding: 15px 20px;">Households</a></li>
                    <li><a href="residents.php" style="color: white; text-decoration: none; display: block; padding: 15px 20px;">Residents</a></li>
                    <li><a href="addnewresidents.php" style="color: white; text-decoration: none; display: block; padding: 15px 20px;">Add Resident</a></li>
                    <li><a href="deaths.php" style="color: white; text-decoration: none; display: block; padding: 15px 20px;">Deaths</a></li>
                    <li><a href="documents.php" style="color: white; text-decoration: none; display: block; padding: 15px 20px;">Documents</a></li>
                    <li><a href="staff_documents.php" style="color: white; text-decoration: none; display: block; padding: 15px 20px;">Generate Certificates</a></li>
                    <li><a href="health_tracking.php" style="color: white; text-decoration: none; display: block; padding: 15px 20px; background: #007bff;">Health Tracking</a></li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">
            <header class="topbar" style="background: white; border-bottom: 1px solid #ddd;">
                <div class="topbar-right" style="padding: 15px 30px; display: flex; justify-content: flex-end; align-items: center; gap: 20px; width: 100%;">
                    <span>Welcome, <strong><?php echo htmlspecialchars($logged_in_username); ?></strong></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </header>

            <main class="page-content">
                <div class="card">
                    <div class="header-flex">
                        <h2>Maintenance Profiles</h2>
                        <button class="add-btn" onclick="prepareAdd('maintenance')">
                            <i class="fas fa-plus"></i> Add Maintenance
                        </button>
                    </div>
                    <div class="data-table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Resident Name</th>
                                    <th>Medical Condition</th>
                                    <th>Medicine</th>
                                    <th>Last Check-up</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($maintenance_result->num_rows > 0): while($row = $maintenance_result->fetch_assoc()): ?>
                                    <tr>
                                        <td style="font-weight: 500;"><?php echo htmlspecialchars($row['resident_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['medical_condition']); ?></td>
                                        <td><?php echo htmlspecialchars($row['medicine']); ?></td>
                                        <td><?php echo $row['last_checkup']; ?></td>
                                        <td><span class="status-badge"><?php echo $row['status']; ?></span></td>
                                        <td style="text-align: center;">
                                            <button class="action-icon edit-icon" onclick='editRecord(<?php echo json_encode($row); ?>, "maintenance")' title="Edit"><i class="fas fa-edit"></i></button>
                                            <a href="health_tracking.php?delete_id=<?php echo $row['id']; ?>&type=maintenance" class="action-icon delete-icon" onclick="return confirm('Are you sure you want to delete this record?')" title="Remove"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; else: echo "<tr><td colspan='6' style='text-align:center; padding:20px;'>No maintenance records found.</td></tr>"; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="header-flex">
                        <h2>Vaccination Records</h2>
                        <button class="add-btn" onclick="prepareAdd('vaccine')">
                            <i class="fas fa-plus"></i> Add Vaccination
                        </button>
                    </div>
                    <div class="data-table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Resident Name</th>
                                    <th>Vaccine Type</th>
                                    <th>Dose</th>
                                    <th>Administered Date</th>
                                    <th>Status</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($vaccine_result->num_rows > 0): while($row = $vaccine_result->fetch_assoc()): ?>
                                    <tr>
                                        <td style="font-weight: 500;"><?php echo htmlspecialchars($row['resident_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['vaccine_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['dose']); ?></td>
                                        <td><?php echo $row['date_administered']; ?></td>
                                        <td><span class="status-badge"><?php echo $row['status']; ?></span></td>
                                        <td style="text-align: center;">
                                            <button class="action-icon edit-icon" onclick='editRecord(<?php echo json_encode($row); ?>, "vaccine")' title="Edit"><i class="fas fa-edit"></i></button>
                                            <a href="health_tracking.php?delete_id=<?php echo $row['id']; ?>&type=vaccine" class="action-icon delete-icon" onclick="return confirm('Are you sure you want to delete this record?')" title="Remove"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; else: echo "<tr><td colspan='6' style='text-align:center; padding:20px;'>No vaccination records found.</td></tr>"; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="recordModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Add Record</h3>
            <form action="health_tracking.php" method="POST" autocomplete="off">
                <input type="hidden" name="record_id" id="record_id">
                <input type="hidden" name="record_type" id="record_type">
                
                <div class="form-group">
                    <label>Resident Name</label>
                    <input type="text" name="resident_name" id="resident_input" placeholder="Type name..." required>
                    <div id="suggestions" class="suggestions-list" style="display:none;"></div>
                </div>

                <div id="maintenanceFields">
                    <div class="form-group"><label>Medical Condition</label><input type="text" name="medical_condition" id="medical_condition"></div>
                    <div class="form-group"><label>Medicine</label><input type="text" name="medicine" id="medicine"></div>
                </div>

                <div id="vaccineFields" style="display:none;">
                    <div class="form-group"><label>Vaccine Type</label><input type="text" name="vaccine_type" id="vaccine_type"></div>
                    <div class="form-group"><label>Dose</label><input type="text" name="dose" id="dose"></div>
                </div>

                <div class="form-group">
                    <label id="dateLabel">Date</label>
                    <input type="date" name="last_checkup" id="last_checkup" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="statusSelect"></select>
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" name="save_record" style="flex:1; padding:12px; background:#007bff; color:white; border:none; border-radius:6px; cursor:pointer;">Save</button>
                    <button type="button" onclick="closeModal()" style="flex:1; padding:12px; background:#6c757d; color:white; border:none; border-radius:6px; cursor:pointer;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const modal = document.getElementById("recordModal");

        function prepareAdd(type) {
            modal.style.display = "block";
            $("#record_id").val("");
            $("form")[0].reset();
            setupFields(type);
        }

        function closeModal() { modal.style.display = "none"; }

        function setupFields(type) {
            $("#record_type").val(type);
            const statusSelect = $("#statusSelect").empty();
            if (type === 'maintenance') {
                $("#modalTitle").text("Add Maintenance Record");
                $("#maintenanceFields").show(); $("#vaccineFields").hide();
                $("#dateLabel").text("Last Check-up Date");
                statusSelect.append('<option value="Active Intake">Active Intake</option><option value="Completed">Completed</option>');
            } else {
                $("#modalTitle").text("Add Vaccination Record");
                $("#maintenanceFields").hide(); $("#vaccineFields").show();
                $("#dateLabel").text("Date Administered");
                statusSelect.append('<option value="Completed">Completed</option><option value="Pending">Pending</option>');
            }
        }

        function editRecord(data, type) {
            modal.style.display = "block";
            setupFields(type);
            $("#record_id").val(data.id);
            $("#resident_input").val(data.resident_name);
            if (type === 'maintenance') {
                $("#medical_condition").val(data.medical_condition);
                $("#medicine").val(data.medicine);
                $("#last_checkup").val(data.last_checkup);
            } else {
                $("#vaccine_type").val(data.vaccine_type);
                $("#dose").val(data.dose);
                $("#last_checkup").val(data.date_administered);
            }
            setTimeout(() => { $("#statusSelect").val(data.status); }, 100);
        }

        $("#resident_input").on("input", function() {
            let q = $(this).val();
            if (q.length > 0) {
                $.post("fetch_residents.php", { query: q }, function(data) { $("#suggestions").html(data).show(); });
            } else { $("#suggestions").hide(); }
        });

        $(document).on("click", ".suggestion-item", function() {
            $("#resident_input").val($(this).text());
            $("#suggestions").hide();
        });
    </script>
</body>
</html>