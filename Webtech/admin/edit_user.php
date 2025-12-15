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
        $bind_types = "ssssi";
        if (!empty($new_password)) {
            $password_update = ", password = ?";
            $bind_types = "sssssi";
            $bind_params = [&$fullname, &$email, &$role, &$status, &$new_password, &$user_id];
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
            call_user_func_array(array($stmt_update, 'bind_param'), $bind_params);
            
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
                <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
                    
                    <h3>User Information (ID: <?php echo $user_id; ?>)</h3>
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
                    
                    <hr style="border-top: 1px solid var(--border-color); margin: 30px 0;">
                    
                    <h3>Change Password (Leave blank if unchanged)</h3>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password">
                    </div>

                    <div class="form-actions">
                        <a href="users.php" class="btn">Cancel</a>
                        <button type="submit" class="btn primary-btn"><i class="fas fa-save"></i> Update User</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>