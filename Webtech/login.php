<?php
session_start();
include __DIR__ . '/db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_id = trim($_POST['login_id']); // Ito ang User ID (e.g., 1001 or 5001)
    $password = $_POST['password'];
    
    // 1. Tiyakin na ang ID ay numeric
    if (!is_numeric($login_id)) {
        $message = "⚠️ ID must be numeric.";
    } else {
        // 2. Tiyakin na may laman ang ID
        if (empty($login_id)) {
            $message = "⚠️ Please enter your User ID.";
        } else {
            // 3. I-SELECT ang user_id, password (plaintext), AT role mula sa database gamit ang user_id
            $stmt = mysqli_prepare($conn, "SELECT user_id, password, role FROM users WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $login_id);
            mysqli_stmt_execute($stmt);
            
            // I-bind ang 3 resulta: $user_id, $db_password, at $user_role
            mysqli_stmt_bind_result($stmt, $user_id, $db_password, $user_role); 

            if (mysqli_stmt_fetch($stmt)) {
                
                // --- PANSAMANTALANG PAGBABAGO DITO: Inalis ang password_verify() ---
                
                // I-check kung ang ipinasok na password ay EKSAKTONG TUGMA sa password sa database.
                if ($password === $db_password) { 
                    
                    $first_digit = substr($login_id, 0, 1);
                    
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['role'] = $user_role;

                    // 4. Redirection Logic batay sa unang digit ng ID (tulad ng gusto mo)
                    if ($first_digit == '1') {
                        // Nagsisimula sa 1 (hal. 1001) -> ADMIN
                        header("Location: admin/admin_menu.html"); 
                    } elseif ($first_digit == '5') {
                        // Nagsisimula sa 5 (hal. 5001) -> STAFF
                        header("Location: index.php"); 
                    } else {
                         // Default redirect
                         $message = "⚠️ Invalid ID format or role.";
                    }
                    exit;
                } else {
                    // Ito ay lalabas kung ang password ay mali
                    $message = "❌ Incorrect password.";
                }
            } else {
                $message = "⚠️ Account not found.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>RMS — Login</title>
    <link rel="stylesheet" href="css/design.css"> 
</head>

<body class="auth-page">
    <main class="auth-card">
        <div class="brand">
            <h1>Record Management System</h1>
        </div>

        <p>Welcome! Please login to continue.</p>

        <form method="POST" class="form-grid">

            <div class="form-group">
                <label>User ID</label> 
                <input type="number" name="login_id" required> 
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn primary">Login</button>
            </div>
            <div class="form-actions">
                <a href="register.php" class="btn link">Create account</a>
            </div>
            
            <?php if (!empty($message)): ?>
                <p class="form-message"><?php echo $message; ?></p>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>