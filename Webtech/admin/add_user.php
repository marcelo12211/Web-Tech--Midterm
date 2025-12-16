<?php
session_start();
// Tiyakin na ang path ay tama
include '../db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'staff';
    
    // --- Validation ---
    if (empty($fullname)) { $errors[] = "Full Name is required."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "A valid Email is required."; }
    if (empty($password) || strlen($password) < 6) { $errors[] = "Password is required and must be at least 6 characters long."; }
    if ($password !== $confirm_password) { $errors[] = "Passwords do not match."; }
    $valid_roles = ['admin', 'staff', 'clerk'];
    if (!in_array(strtolower($role), $valid_roles)) { $errors[] = "Invalid role selected."; }
    
    // Check if email already exists
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            $errors[] = "This email is already registered.";
        }
        $check_stmt->close();
    }
    
    // --- Execution ---
    if (empty($errors)) {
        // !!! SECURITY WARNING: Dito tinanggal ang password_hash() !!!
        $raw_password = $password; 
        $status = 'active'; 
        
        $sql = "INSERT INTO users (fullname, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            $errors[] = "Database error (Prepare): " . $conn->error;
        } else {
            // Gumagamit ng $raw_password (plain text)
            $stmt->bind_param("sssss", $fullname, $email, $raw_password, $role, $status); 
            
            if ($stmt->execute()) {
                // Binago ang success message para maging malinaw ang security issue
                $_SESSION['success_message'] = "User **" . htmlspecialchars($fullname) . "** added successfully. **WARNING: Password stored in plain text.**";
                header("Location: users.php");
                exit(); 
            } else {
                $errors[] = "Error adding user: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Close connection if it's still open
if ($conn && $conn->ping()) {
    $conn->close();
}

function set_value($field, $default = '') {
    global $_POST;
    return htmlspecialchars($_POST[$field] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add New User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
/* --- General and Layout Styles (Consistent with users.php) --- */
:root {
    --primary-color: #226b8dff;
    --primary-dark: #226b8dff;
    --secondary-color: #5f6368;
    --danger-color: #ea4335;
    --background-color: #f8f9fa;
    --card-background: #ffffff;
    --sidebar-bg: #212121;
    --text-color: #202124;
    --text-light: #5f6368;
    --border-color: #dadce0;
    --radius: 10px;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
* { box-sizing: border-box; }
body {
    margin: 0;
    font-family: "Roboto", Arial, sans-serif;
    background: var(--background-color);
    color: var(--text-color);
}
a { text-decoration: none; }
.app-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar Styles */
.sidebar {
    width: 250px;
    background: var(--sidebar-bg);
    color: white;
}
.logo {
    padding: 25px;
    text-align: center;
    font-weight: 700;
    font-size: 1.15rem;
    line-height: 1.3;
}
.main-nav ul { list-style: none; padding: 0; margin: 0; }
.main-nav a {
    display: block;
    padding: 14px 20px;
    color: #bdc1c6;
}
.main-nav a:hover,
.main-nav a.active {
    background: var(--primary-dark);
    color: white;
}

/* Main Content/Topbar Styles */
.main-content { flex: 1; }
.topbar {
    background: white;
    padding: 15px 30px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
.topbar-right { display: flex; align-items: center; }
.user-info { margin-right: 15px; color: var(--text-light); }
.logout-btn {
    padding: 8px 15px;
    border: 1px solid var(--border-color);
    background: transparent;
    color: var(--text-color);
    font-size: 0.9rem;
    cursor: pointer;
    border-radius: 6px;
    transition: background-color 0.2s;
    font-weight: 500;
}
.logout-btn:hover { background: var(--background-color); }

.page-content { padding: 30px; }
.page-content h2 { margin-top: 0; margin-bottom: 25px; color: var(--text-color); display: flex; align-items: center; gap: 10px; }

/* Button Styles */
.btn {
    padding: 10px 18px;
    border-radius: 6px;
    font-weight: 500;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
}
.primary-btn {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}
.primary-btn:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
}

/* Alert Styles */
.alert-error {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: 500;
    background-color: #fce4e4;
    color: var(--danger-color);
    border: 1px solid var(--danger-color);
}
.alert-error ul {
    margin: 5px 0 0 15px;
    padding: 0;
}
.alert-error strong {
    font-weight: 700;
}

/* --- Form Specific Styles --- */
.form-card {
    background: var(--card-background);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 30px;
    max-width: 600px; 
    margin: 0 auto; 
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-color);
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group select {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    background-color: #ffffff;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(34, 107, 141, 0.2); 
}

.form-actions {
    margin-top: 30px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.form-actions .btn {
    min-width: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* Media Queries */
@media (max-width: 768px) {
    .sidebar { width: 100%; height: auto; }
    .app-container { flex-direction: column; }
    .page-content { padding: 20px; }
    .form-card { padding: 20px; }
    .form-actions { flex-direction: column; }
    .form-actions .btn { width: 100%; }
}
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
                <li><a href="users.php" class="active">Manage Users</a></li>
                <li><a href="documents.php">Documents</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-right">
                <span class="user-info">Welcome, <?php echo $logged_in_username; ?></span>
                <a href="logout.php" class="btn logout-btn">Logout</a>
            </div>
        </div>
        <div class="page-content">
            <h2><i class="fas fa-user-plus"></i> Add New System User</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert-error">
                    <strong>Error!</strong> Please correct the following issues:
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="form-card">
                <form action="add_user.php" method="POST">
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" value="<?php echo set_value('fullname'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo set_value('email'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="role">User Role</label>
                        <select id="role" name="role" required>
                            <option value="staff" <?php echo (set_value('role', 'staff') == 'staff' ? 'selected' : ''); ?>>Staff</option>
                            <option value="clerk" <?php echo (set_value('role') == 'clerk' ? 'selected' : ''); ?>>Clerk</option>
                            <option value="admin" <?php echo (set_value('role') == 'admin' ? 'selected' : ''); ?>>Administrator</option>
                        </select>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">
                    
                    <p style="color: var(--danger-color); font-size: 0.9em; margin-bottom: 25px; font-weight: 600;">
                        **CRITICAL WARNING:** The password will be stored in PLAIN TEXT. This is a severe security risk!
                    </p>
                    
                    <div class="form-group">
                        <label for="password">Password (Min. 6 characters)</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="form-actions">
                        <a href="users.php" class="btn"><i class="fas fa-times-circle"></i> Cancel</a>
                        <button type="submit" class="btn primary-btn"><i class="fas fa-save"></i> Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>