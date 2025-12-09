<?php
session_start();
include __DIR__ . '/db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE email = ? AND role = ?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $role);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $hash);

    if (mysqli_stmt_fetch($stmt)) {
        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            if ($role == "admin") {
                header("Location: admin/admin_menu.html");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $message = "❌ Incorrect password.";
        }
    } else {
        $message = "⚠️ Account not found.";
    }

    mysqli_stmt_close($stmt);
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
    <label>ID</label>
    <input type="email" name="email" required placeholder="you@gmail.com">
  </div>

  <div class="form-group">
    <label>Password</label>
    <input type="password" name="password" required placeholder="Your password">
  </div>


      <input type="hidden" name="role" id="roleInput" value="client">

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

  <script src="js/login.js"></script>
</body>
</html>
