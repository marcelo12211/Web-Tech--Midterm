<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "happy hallow";
$port = 3306;
$conn = mysqli_connect($servername, $username, $password, $dbname, $port); 
mysqli_set_charset($conn, 'utf8mb4');

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// handle regis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['name']); // full name
    $email = $_POST['email']; 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
  
    // check if email already exists
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $message = "⚠️ Email already registered!";
    } else {
        // insert user with fullname
        $stmt = mysqli_prepare($conn, "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $password, $role);

        if (mysqli_stmt_execute($stmt)) {
            $message = "✅ Account created successfully! You can now log in.";
            header("Refresh:2; url=login.php"); // redirect after 2 seconds
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

  <style>
    body.auth-page {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      background: linear-gradient(135deg, #031b50, #1e3a8a);
      overflow: hidden;
      position: relative;
      padding-left: 8%;
    }

    .auth-card {
      width: 420px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      padding: 32px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
      text-align: center;
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
      animation: slideInLeft 0.6s ease;
      position: relative;
      z-index: 2;
    }

    .auth-card h1 {
      color: #fff;
    }

    .auth-card p {
      color: #cbd5e1;
      margin-bottom: 16px;
    }

    .form-grid input {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: #fff;
      font-size: 15px;
      padding: 8px;
      border-radius: 6px;
      width: 100%;
      margin-bottom: 10px;
    }

    .form-grid input::placeholder {
      color: #f1f1f1;
    }

    .btn.primary {
      background: #3b82f6;
      color: #fff;
      font-weight: bold;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      transition: background 0.3s ease;
      cursor: pointer;
    }

    .btn.primary:hover {
      background: #1d4ed8;
    }

    .btn.link {
      color: #bfdbfe;
      font-size: 14px;
      display: inline-block;
      margin-top: 10px;
    }

    .btn.link:hover {
      text-decoration: underline;
    }

    .form-message {
      margin-top: 15px;
      color: yellow;
      font-weight: bold;
    }

    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }
  </style>
</head>

<body class="auth-page">
  <main class="auth-card">
    <div class="brand">
      <img src="assets/logo.png" alt="Logo" class="logo" style="width:50px;height:50px;border-radius:8px;">
      <h1>Create Account</h1>
    </div>

    <form method="POST" class="form-grid">
      <label>Full name
        <input type="text" name="name" required placeholder="Jane Doe">
      </label>
      <label>Email
        <input type="email" name="email" required placeholder="you@gmail.com">
      </label>
      <label>Password
        <input type="password" name="password" required placeholder="Choose a password">
      </label>
      
        <label>Role
            <select name="role" required>
                <option value="client" selected>Client</option>
                <option value="admin">Admin</option>
            </select>
        </label>

      <div class="form-actions">
        <button type="submit" class="btn primary">Register</button>
        <a href="login.php" class="btn link">Back to login</a>
      </div>

      <?php if (!empty($message)): ?>
        <p class="form-message"><?php echo $message; ?></p>
      <?php endif; ?>
    </form>
  </main>
</body>
</html>
