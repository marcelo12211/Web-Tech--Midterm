<?php
session_start();
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
    if (empty($fullname)) { $errors[] = "Full Name is required."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "A valid Email is required."; }
    if (empty($password) || strlen($password) < 6) { $errors[] = "Password is required and must be at least 6 characters long."; }
    if ($password !== $confirm_password) { $errors[] = "Passwords do not match."; }
    $valid_roles = ['admin', 'staff', 'clerk'];
    if (!in_array(strtolower($role), $valid_roles)) { $errors[] = "Invalid role selected."; }
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
    if (empty($errors)) {
        $raw_password = $password; 
        $status = 'active'; 
        $sql = "INSERT INTO users (fullname, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $errors[] = "Database error (Prepare): " . $conn->error;
        } else {
            $stmt->bind_param("sssss", $fullname, $email, $raw_password, $role, $status);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User **" . htmlspecialchars($fullname) . "** added successfully (NOTE: Password stored in plain text).";
                header("Location: users.php");
                exit(); 
            } else {
                $errors[] = "Error adding user: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
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
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">User Role</label>
                        <select id="role" name="role" required>
                            <option value="staff" <?php echo (set_value('role', 'staff') == 'staff' ? 'selected' : ''); ?>>Staff</option>
                            <option value="clerk" <?php echo (set_value('role') == 'clerk' ? 'selected' : ''); ?>>Clerk</option>
                            <option value="admin" <?php echo (set_value('role') == 'admin' ? 'selected' : ''); ?>>Administrator</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <a href="users.php" class="btn">Cancel</a>
                        <button type="submit" class="btn primary-btn"><i class="fas fa-save"></i> Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>