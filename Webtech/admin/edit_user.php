<?php
session_start();
include '../db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin';
$errors = [];
$user_data = null;
$user_id = $_GET['id'] ?? null;

function set_value($field, $default = '') {
    global $_POST, $user_data;
    if (isset($_POST[$field])) {
        return htmlspecialchars($_POST[$field]);
    } elseif ($user_data && isset($user_data[$field])) {
        return htmlspecialchars($user_data[$field]);
    }
    return $default;
}

if ($user_id) {
    $sql = "SELECT user_id, fullname, email, role, status FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
    } else {
        $_SESSION['error_message'] = "User not found or invalid ID.";
        header("Location: users.php");
        exit();
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "User ID not specified for editing.";
    header("Location: users.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'staff';
    $status = $_POST['status'] ?? 'active';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($fullname)) { $errors[] = "Full Name is required."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "A valid Email is required."; }
    $valid_roles = ['admin', 'staff', 'clerk'];
    if (!in_array(strtolower($role), $valid_roles)) { $errors[] = "Invalid role selected."; }
    
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) { 
            $errors[] = "New password must be at least 6 characters long."; 
        }
        if ($new_password !== $confirm_password) { 
            $errors[] = "New password and Confirm password do not match."; 
        }
    }
    
    if ($email !== $user_data['email'] && empty($errors)) {
        $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $check_stmt->bind_param("si", $email, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $errors[] = "This email is already registered to another user.";
        }
        $check_stmt->close();
    }
    if (empty($errors)) {
        $password_update = "";
        $bind_params = [];
        $bind_types = "ssssi";
        if (!empty($new_password)) {
            $password_update = ", password = ?";
            $bind_types = "sssssi";
            $password_to_save = $new_password; 
            $bind_params = [&$fullname, &$email, &$role, &$status, &$password_to_save, &$user_id];
        } else {
            $bind_params = [&$fullname, &$email, &$role, &$status, &$user_id];
        }

        $sql_update = "UPDATE users SET fullname = ?, email = ?, role = ?, status = ?"
                    . $password_update 
                    . " WHERE user_id = ?";
        
        $stmt_update = $conn->prepare($sql_update);
        
        if ($stmt_update === false) {
            $errors[] = "Database error (Prepare Update): " . $conn->error;
        } else {
            array_unshift($bind_params, $bind_types);
            call_user_func_array([$stmt_update, 'bind_param'], $bind_params);
            
            if ($stmt_update->execute()) {
                $_SESSION['success_message'] = "User **" . htmlspecialchars($fullname) . "** updated successfully!";
                header("Location: users.php");
                exit();
            } else {
                $errors[] = "Error updating user: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
    if (!empty($errors)) {
         $user_data = [
             'fullname' => $fullname,
             'email' => $email,
             'role' => $role,
             'status' => $status,
             'user_id' => $user_id
         ];
    }
}

if ($conn && $conn->ping()) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit User | <?php echo htmlspecialchars($user_data['fullname'] ?? 'User'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
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
.page-content h3 { 
    margin: 0 0 15px 0; 
    padding-bottom: 5px; 
    border-bottom: 1px solid var(--border-color); 
    color: var(--primary-dark);
}

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
            <h2><i class="fas fa-user-edit"></i> Edit User: <?php echo htmlspecialchars($user_data['fullname'] ?? 'Unknown'); ?></h2>
            
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
                <form action="edit_user.php?id=<?php echo htmlspecialchars($user_id); ?>" method="POST">
                    
                    <h3>User Information (ID: <?php echo htmlspecialchars($user_id); ?>)</h3>
                    
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
                            <option value="staff" <?php echo (set_value('role') == 'staff' ? 'selected' : ''); ?>>Staff</option>
                            <option value="clerk" <?php echo (set_value('role') == 'clerk' ? 'selected' : ''); ?>>Clerk</option>
                            <option value="admin" <?php echo (set_value('role') == 'admin' ? 'selected' : ''); ?>>Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="active" <?php echo (set_value('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="inactive" <?php echo (set_value('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        </select>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 30px 0;">
                    
                    <h3>Change Password (Leave blank if unchanged)</h3>

                    <p style="color: var(--danger-color); font-size: 0.9em; margin-bottom: 25px; font-weight: 600;">
                        **CRITICAL WARNING:** The new password will be stored in PLAIN TEXT. This is a severe security risk!
                    </p>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>

                    <div class="form-actions">
                        <a href="users.php" class="btn"><i class="fas fa-times-circle"></i> Cancel</a>
                        <button type="submit" class="btn primary-btn"><i class="fas fa-save"></i> Update User</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>