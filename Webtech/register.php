<?php
session_start();
include __DIR__ . '/db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['name']);
    $email = $_POST['email']; 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "client"; 

    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $message = "⚠️ Email already registered!";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $password, $role);

        if (mysqli_stmt_execute($stmt)) {
            $message = "✅ Account created successfully! Redirecting to login...";
            header("Refresh:2; url=login.php");
        } else {
            $message = "❌ Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_stmt_close($check);
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>RMS — Register</title>
  <link rel="stylesheet" href="css/design.css">
</head>

<body class="auth-page">
  <main class="auth-card">
    <div class="brand">
      <h1>Create Account</h1>
    </div>

    <form method="POST" class="form-grid">
      <label>Full name
        <input type="text" name="name">
      </label>
      <label>ID
        <input type="email" name="email">
      </label>
      <label>Password
        <input type="password" name="password">
      </label>

      <button type="submit" class="btn primary">Register</button>
      <a href="login.php" class="btn link">Back to login</a>

      <?php if (!empty($message)): ?>
        <p class="form-message"><?= $message; ?></p>
      <?php endif; ?>
    </form>
  </main>
</body>
</html>
