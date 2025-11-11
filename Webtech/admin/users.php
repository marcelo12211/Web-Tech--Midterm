<?php
// Connect to database
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "happyhallow";
$conn = mysqli_connect($servername, $username, $password, $dbname, 3306);
mysqli_set_charset($conn, 'utf8mb4');
if (!$conn) die("Connection failed: " . mysqli_connect_error());

// Handle deactivate (delete) action
if (isset($_GET['deactivate'])) {
    $user_id = intval($_GET['deactivate']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
    header("Location: users.php");
    exit;
}

// Fetch all users
$result = mysqli_query($conn, "SELECT id, email, role FROM users ORDER BY id");
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users | Admin</title>
  <style>
    body { font-family: Arial; margin: 20px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #003366; color: white; }
    button { padding: 5px 10px; margin: 2px; cursor: pointer; }
    button:hover { background-color: #e0e0e0; }
  </style>
</head>
<body>
  <h2>Manage Users</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Role</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo $u['email']; ?></td>
            <td><?php echo ucfirst($u['role']); ?></td>
            <td>
              <a href="?deactivate=<?php echo $u['id']; ?>" onclick="return confirm('Are you sure you want to deactivate this user?');">
                <button>Deactivate</button>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
