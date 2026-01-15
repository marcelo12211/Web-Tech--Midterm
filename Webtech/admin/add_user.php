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
    if (empty($password) || strlen($password) < 6) { $errors[] = "Password must be at least 6 characters."; }
    if ($password !== $confirm_password) { $errors[] = "Passwords do not match."; }
    
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
        $prefix = (strtolower($role) === 'admin') ? '9' : '5';
        $sql_max = "SELECT MAX(user_id) as max_id FROM users WHERE CAST(user_id AS CHAR) LIKE '$prefix%'";
        $result_max = $conn->query($sql_max);
        $row = $result_max->fetch_assoc();
        
        if ($row['max_id']) {
            $new_user_id = $row['max_id'] + 1;
        } else {
            $new_user_id = ($prefix === '9') ? 90001 : 50001;
        }

        $status = 'active'; 
        $sql = "INSERT INTO users (user_id, fullname, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("isssss", $new_user_id, $fullname, $email, $password, $role, $status); 
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User **" . htmlspecialchars($fullname) . "** added! ID: " . $new_user_id;
                header("Location: users.php");
                exit(); 
            } else {
                $errors[] = "Error adding user: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

function set_value($field, $default = '') {
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
        :root {
            --primary-color: #226b8dff;
            --primary-dark: #1a526b;
            --danger-color: #ea4335;
            --background-color: #f8f9fa;
            --border-color: #dadce0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        * { box-sizing: border-box; font-family: "Roboto", sans-serif; }
        body { margin: 0; background: var(--background-color); }
        .app-container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #212121; color: white; }
        .logo { padding: 25px; text-align: center; font-weight: 700; }
        .main-nav ul { list-style: none; padding: 0; }
        .main-nav a { display: block; padding: 14px 20px; color: #bdc1c6; text-decoration: none; }
        .main-nav a.active { background: var(--primary-dark); color: white; }
        .main-content { flex: 1; }
        .topbar { background: white; padding: 15px 30px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: flex-end; }
        .page-content { padding: 30px; }
        .form-card { background: white; border-radius: 10px; box-shadow: var(--shadow); padding: 30px; max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; }
        .alert-error { padding: 15px; background: #fce4e4; color: var(--danger-color); border: 1px solid var(--danger-color); border-radius: 6px; margin-bottom: 20px; }
        .btn { padding: 10px 18px; border-radius: 6px; border: 1px solid var(--border-color); cursor: pointer; text-decoration: none; font-weight: 500; }
        .primary-btn { background: var(--primary-color); color: white; border: none; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px; }
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
                <li><a href="health_tracking.php">Health Tracking</a></li>
            </ul>
        </nav>
    </div>

    <main class="main-content">
        <header class="topbar">
            <span style="margin-right:15px">Welcome, <?php echo $logged_in_username; ?></span>
            <a href="logout.php" class="btn">Logout</a>
        </header>

        <div class="page-content">
            <h2><i class="fas fa-user-plus"></i> Add New System User</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert-error">
                    <strong>Error!</strong>
                    <ul><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            
            <div class="form-card">
                <form action="add_user.php" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="fullname" value="<?php echo set_value('fullname'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo set_value('email'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>User Role</label>
                        <select name="role" required>
                            <option value="staff" <?php echo (set_value('role') == 'staff' ? 'selected' : ''); ?>>Staff</option>
                            <option value="admin" <?php echo (set_value('role') == 'admin' ? 'selected' : ''); ?>>Administrator</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    
                    <div class="form-actions">
                        <a href="users.php" class="btn">Cancel</a>
                        <button type="submit" class="btn primary-btn">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>