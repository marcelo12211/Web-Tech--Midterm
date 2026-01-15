<?php
session_start();
include '../db_connect.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_name = $_POST['user_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user_role = $_POST['user_role'] ?? '';
    $user_status = $_POST['user_status'] ?? 0;

    if (empty($user_name) || empty($username) || empty($password) || empty($user_role)) {
        $error = "Please fill in all required fields.";
    } else {
        $sql_check = "SELECT user_id FROM users WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $error = "Error: The username '" . htmlspecialchars($username) . "' is already taken.";
            $stmt_check->close();
        } else {
            $stmt_check->close();
            $prefix = ($user_role === 'Admin') ? '9' : '5';
            $sql_max = "SELECT MAX(user_id) as max_id FROM users WHERE CAST(user_id AS CHAR) LIKE '$prefix%'";
            $result_max = $conn->query($sql_max);
            $row = $result_max->fetch_assoc();
            
            if ($row['max_id']) {
                $new_user_id = $row['max_id'] + 1;
            } else {
                $new_user_id = ($user_role === 'Admin') ? 90001 : 50001;
            }
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql_insert = "INSERT INTO users (user_id, user_name, username, password, user_role, user_status) 
                           VALUES (?, ?, ?, ?, ?, ?)";
                           
            $stmt_insert = $conn->prepare($sql_insert);
            if ($stmt_insert) {
                $stmt_insert->bind_param("issssi", $new_user_id, $user_name, $username, $hashed_password, $user_role, $user_status);

                if ($stmt_insert->execute()) {
                    $_SESSION['success_message'] = "New " . $user_role . " added! ID: " . $new_user_id;
                    header("Location: manage_users.php");
                    exit();
                } else {
                    $error = "Error saving user: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
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
    <title>Add New User</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css" />
    <style>
        .form-card {
            max-width: 600px;
            margin: 20px auto;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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
                    <li><a href="manage_users.php" class="active">Manage Users</a></li>
                    <li><a href="documents.php">Documents</a></li>
                    <li class="nav-divider"></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <div class="topbar">
                <div class="topbar-right">
                    <span class="user-info">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                    <a href="logout.php" class="btn logout-btn">Logout</a>
                </div>
            </div>

            <div class="page-content">
                <h2>Add New System User</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" style="background-color: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="card form-card">
                    <form method="POST" action="add_new_user.php">
                        
                        <div class="form-group">
                            <label for="user_name">Full Name</label>
                            <input type="text" id="user_name" name="user_name" required value="<?php echo htmlspecialchars($_POST['user_name'] ?? ''); ?>" />
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" />
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required />
                        </div>

                        <div class="form-group">
                            <label for="user_role">Role *</label>
                            <select id="user_role" name="user_role" required>
                                <option value="">Select Role</option>
                                <option value="Admin" <?php echo (($_POST['user_role'] ?? '') == 'Admin' ? 'selected' : ''); ?>>Admin</option>
                                <option value="Staff" <?php echo (($_POST['user_role'] ?? '') == 'Staff' ? 'selected' : ''); ?>>Staff</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="user_status">Status *</label>
                            <select id="user_status" name="user_status" required>
                                <option value="1" <?php echo (($_POST['user_status'] ?? 1) == 1 ? 'selected' : ''); ?>>Active</option>
                                <option value="0" <?php echo (($_POST['user_status'] ?? 1) == 0 ? 'selected' : ''); ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-actions">
                            <a href="manage_users.php" class="btn secondary-btn">Cancel</a>
                            <button type="submit" class="btn primary-btn">Create User</button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>