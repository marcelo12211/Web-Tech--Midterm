<?php
include "../db_connect.php";


// DELETE user
if (isset($_GET['deactivate'])) {
    $user_id = intval($_GET['deactivate']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
    header("Location: users.php");
    exit;
}

$message = "";

// ADD user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_user'])) {
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $message = "⚠ Email already registered!";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $password, $role);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $message = "✅ User added!";
            header("Refresh:1");
        }
        mysqli_stmt_close($check);
    }

    // EDIT user
    if (isset($_POST['edit_user'])) {
        $id = intval($_POST['id']);
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $email = $_POST['email'];
        $role = $_POST['role'];

        $stmt = mysqli_prepare($conn, "UPDATE users SET fullname=?, email=?, role=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $fullname, $email, $role, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $message = "✅ User updated!";
        header("Refresh:1");
    }
}

$result = mysqli_query($conn, "SELECT id, fullname, email, role FROM users ORDER BY id");
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users | Admin</title>

<link rel="stylesheet" href="css/users.css">
</head>

<body>

<div class="container">

<div class="top-bar" style="justify-content: center; gap: 10px;">
  <a href="admin_menu.html" class="back-btn">← Back</a>
  <input type="text" id="searchInput" placeholder="Search users...">
  <select id="roleFilter">
    <option value="">All Roles</option>
    <option value="admin">Admin</option>
    <option value="client">Client</option>
  </select>
  <input type="number" id="minId" placeholder="Min ID">
  <input type="number" id="maxId" placeholder="Max ID">
  <button id="filterBtn" class="add-btn">Filter</button>
  <button class="add-btn" id="openModal">+ Add User</button>
</div>

<h2>Manage Users</h2>
<?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

<table>
  <thead>
    <tr><th>ID</th><th>Full Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
  </thead>
  <tbody>
    <?php foreach($users as $u): ?>
    <tr>
      <td><?= $u['id'] ?></td>
      <td><?= htmlspecialchars($u['fullname']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= ucfirst($u['role']) ?></td>
      <td>
        <button class="edit-btn"
          data-id="<?= $u['id'] ?>"
          data-name="<?= htmlspecialchars($u['fullname']) ?>"
          data-email="<?= htmlspecialchars($u['email']) ?>"
          data-role="<?= $u['role'] ?>"
        >Edit</button>

        <a href="?deactivate=<?= $u['id'] ?>" onclick="return confirm('Deactivate user?');">
          <button class="deactivate-btn">Deactivate</button>
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<?php include "users_modal.php"; ?>
<script src="js_admin/users.js"></script>
</body>
</html>
