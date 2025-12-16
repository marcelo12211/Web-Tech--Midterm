<?php
session_start();
include __DIR__ . '/db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];

    if (empty($login_id)) {
        $message = "Please enter your User ID.";
    } elseif (!is_numeric($login_id)) {
        $message = "ID must be numeric.";
    } else {

        $first_digit = substr($login_id, 0, 1);

        /* ===============================
           ADMIN LOGIN → NODE VALIDATION
           =============================== */
        if ($first_digit === '9') {

            // Get admin email from DB
            $stmt = mysqli_prepare($conn, "SELECT email FROM users WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $login_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $email);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if (empty($email)) {
                $message = "Account not found.";
            } else {

                $payload = json_encode([
                    "email" => $email,
                    "password" => $password
                ]);

                $ch = curl_init("http://127.0.0.1:5000/admin/login");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);

                if (!empty($result['success']) && $result['role'] === 'admin') {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['role'] = 'admin';

                    header("Location: /webtechfinals/Web-Tech--Midterm/Webtech/admin/admin_dashboard.php");
                    exit;
                } else {
                    $message = "Invalid admin credentials.";
                }
            }

        } else {

            /* ===============================
               CLIENT / STAFF LOGIN (PHP)
               =============================== */
            $stmt = mysqli_prepare($conn, "SELECT user_id, password, role FROM users WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $login_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user_id, $db_password, $user_role);

            if (mysqli_stmt_fetch($stmt)) {
                if ($password === $db_password) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['role'] = $user_role;

                    if ($first_digit === '5') {
                        header("Location: index.php");
                        exit;
                    } else {
                        $message = "Invalid ID format or role.";
                    }
                } else {
                    $message = "Incorrect password.";
                }
            } else {
                $message = "Account not found.";
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
                <input type="text" name="login_id" inputmode="numeric" pattern="[0-9]+" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn primary">Login</button>
            </div>

            <?php if (!empty($message)): ?>
                <p class="form-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

        </form>
    </main>
</body>
</html>
