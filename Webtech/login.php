<?php
//DATABASE CONNECTION
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "happyhallow";
$port = 3306;

$conn = mysqli_connect($servername, $username, $password, $dbname,$port);
mysqli_set_charset($conn, 'utf8mb4');

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// LOGIN HANDLING
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
      session_start();
      session_regenerate_id(true);
      $_SESSION['user_id'] = $id;
      $_SESSION['email'] = $email;
      $_SESSION['role'] = $role;

      if ($role == "admin") {
        header("Location: admin/admin_menu.html");
      } else {
        header("Location: client_main.php");
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

  <style>
    /* login box left, blue background */
    body.auth-page {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      background: linear-gradient(135deg, #031b50, #1e3a8a);
      overflow: hidden;
      position: relative;
      padding-left: 750px;
    }

    /* login box */
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

    /* input boxes */
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

    /* login button */
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

    /* create account */
    .btn.link {
      color: #bfdbfe;
      font-size: 14px;
      display: inline-block;
      margin-top: 10px;
    }

    .btn.link:hover {
      text-decoration: underline;
    }

    .role-selector { 
      margin-top: 16px; 
      display: flex; 
      justify-content: center; 
      gap: 10px; 
    } 

    .role-btn { 
      background: rgba(255,255,255,0.2); 
      border: 1px solid rgba(255,255,255,0.3); 
      color: #fff; 
      padding: 8px 16px; 
      border-radius: 6px; 
      cursor: pointer; 
      transition: all 0.3s ease; 
    } 

    .role-btn.active { 
      background: #3b82f6; 
      border-color: #60a5fa; 
    } 

    .role-btn:hover {
      background: rgba(59,130,246,0.4); 
    }

    /* Animation */
    @keyframes slideInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    .form-message {
      margin-top: 15px;
      color: yellow;
      font-weight: bold;
    }
  </style>
</head>

<body class="auth-page">
  <main class="auth-card">
    <div class="brand">
      <img src="assets/logo.png" alt="Logo" class="logo" style="width:50px;height:50px;border-radius:8px;">
      <h1>Record Management System</h1>
    </div>

    <p>Welcome back! Please login to continue.</p>

    <form method="POST" class="form-grid">
      <label>Email
        <input type="email" name="email" required placeholder="you@gmail.com">
      </label>
      <label>Password
        <input type="password" name="password" required placeholder="Your password">
      </label>

      <!-- Hidden input for role -->
      <input type="hidden" name="role" id="roleInput" value="client">

      <div class="form-actions">
        <button type="submit" class="btn primary">Login</button>
      </div>
      <div class="form-actions">
        <a href="register.php" class="btn link">Create account</a>
      </div>

      <!-- Role selector -->
      <div class="role-selector">
        <button type="button" class="role-btn active" id="clientBtn">Client</button>
        <button type="button" class="role-btn" id="adminBtn">Admin</button>
      </div>

      <!-- Display message from PHP -->
      <?php if (!empty($message)): ?>
        <p class="form-message"><?php echo $message; ?></p>
      <?php endif; ?>
    </form>
  </main>

  <script>
    // handle role switching
    const roleInput = document.getElementById("roleInput");
    const clientBtn = document.getElementById("clientBtn");
    const adminBtn = document.getElementById("adminBtn");

    clientBtn.addEventListener("click", () => {
      roleInput.value = "client";
      clientBtn.classList.add("active");
      adminBtn.classList.remove("active");
    });

    adminBtn.addEventListener("click", () => {
      roleInput.value = "admin";
      adminBtn.classList.add("active");
      clientBtn.classList.remove("active");
    });
  </script>
</body>
</html>
