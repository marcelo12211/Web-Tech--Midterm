<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "happyhallow";
$port = 3306;
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);
mysqli_set_charset($conn, 'utf8mb4');

if (!$conn) die("Connection failed: " . mysqli_connect_error());

// handle deactivate
if (isset($_GET['deactivate'])) {
    $user_id = intval($_GET['deactivate']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
    header("Location: users.php");
    exit;
}

// handle add user
$message = "";
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
            $message = "⚠️ Email already registered!";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $fullname, $email, $password, $role);
            if (mysqli_stmt_execute($stmt)) {
                $message = "✅ User added successfully!";
                header("Refresh:1");
            } else {
                $message = "❌ Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check);
    }

    // handle edit user
    if (isset($_POST['edit_user'])) {
        $id = intval($_POST['id']);
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $email = $_POST['email'];
        $role = $_POST['role'];

        $stmt = mysqli_prepare($conn, "UPDATE users SET fullname=?, email=?, role=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $fullname, $email, $role, $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "✅ User updated successfully!";
            header("Refresh:1");
        } else {
            $message = "❌ Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// get all users
$result = mysqli_query($conn, "SELECT id, fullname, email, role FROM users ORDER BY id");
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users | Admin</title>
<style>
  body { font-family: 'Segoe UI', sans-serif; background: #f4f6fa; margin:0; padding:20px; }
  h2 { color:#003366; margin-bottom:15px; }
  .container { max-width:1000px; margin:auto; background:white; border-radius:12px; box-shadow:0 3px 8px rgba(0,0,0,0.1); padding:20px 30px; }
  .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
  .back-btn { background:#003366; color:white; border:none; border-radius:6px; padding:8px 14px; text-decoration:none; }
  .back-btn:hover { background:#0055aa; }
  .add-btn { background:#3b82f6; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer; transition:0.2s; }
  .add-btn:hover { background:#1d4ed8; }
  table { border-collapse:collapse; width:100%; margin-top:10px; }
  th, td { border:1px solid #e2e8f0; padding:10px; text-align:left; }
  th { background:#003366; color:white; }
  tr:nth-child(even){ background:#f8fafc; }
  tr:hover{ background:#e9effa; }
  button { padding:6px 12px; border-radius:5px; cursor:pointer; border:none; }
  .deactivate-btn { background:#dc2626; color:white; }
  .deactivate-btn:hover { background:#b91c1c; }
  .edit-btn { background:#f59e0b; color:white; }
  .edit-btn:hover { background:#b45309; }
  .message { margin-top:12px; font-weight:bold; color:green; }
  /* Modal */
  .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); justify-content:center; align-items:center; }
  .modal-content { background:#fff; padding:25px; border-radius:10px; width:380px; box-shadow:0 2px 10px rgba(0,0,0,0.2); }
  .modal-content h3 { margin-top:0; color:#003366; }
  .close-btn { float:right; cursor:pointer; color:#ef4444; font-weight:bold; }
  input, select { width:100%; padding:8px; margin:6px 0 12px; border-radius:5px; border:1px solid #ccc; font-size:14px; }
</style>
</head>
<body>

<div class="container">

<div class="top-bar" style="justify-content: center; gap: 10px;">
  <a href="admin_menu.html" class="back-btn">← Back</a>

  <!-- Search input -->
  <input type="text" id="searchInput" placeholder="Search users..." 
  style="padding:6px 10px; border-radius:6px; border:1px solid #ccc; width:180px;">

  <!-- Role filter (All/Admin/Client) -->
  <select id="roleFilter" style="padding:6px 10px; border-radius:6px; border:1px solid #ccc;">
    <option value="">All Roles</option>
    <option value="admin">Admin</option>
    <option value="client">Client</option>
  </select>

  <!-- ID filter (number range) -->
  <input type="number" id="minId" placeholder="Min ID" style="padding:6px 10px; border-radius:6px; border:1px solid #ccc; width:70px;">
  <input type="number" id="maxId" placeholder="Max ID" style="padding:6px 10px; border-radius:6px; border:1px solid #ccc; width:70px;">

  <!-- filter button -->
  <button id="filterBtn" class="add-btn">Filter</button>

  <button class="add-btn" id="openModal">+ Add User</button>
</div>

  <h2>Manage Users</h2>
  <?php if(!empty($message)) echo "<p class='message'>$message</p>"; ?>

  <table>
    <thead>
      <tr><th>ID</th><th>Full Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
    </thead>
    <tbody>
      <?php if(!empty($users)): ?>
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
      <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Add User Modal -->
<div class="modal" id="userModal">
  <div class="modal-content">
    <span class="close-btn" id="closeModal">&times;</span>
    <h3>Add New User</h3>
    <form method="POST">
      <input type="hidden" name="add_user" value="1">
      <label>Full Name</label><input type="text" name="fullname" required>
      <label>Email</label><input type="email" name="email" required>
      <label>Password</label><input type="password" name="password" required>
      <label>Role</label>
      <select name="role" required>
        <option value="client" selected>Client</option>
        <option value="admin">Admin</option>
      </select>
      <button type="submit" class="add-btn" style="width:100%;">Add User</button>
    </form>
  </div>
</div>

<!-- edit user modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <span class="close-btn" id="closeEdit">&times;</span>
    <h3>Edit User</h3>
    <form method="POST">
      <input type="hidden" name="edit_user" value="1">
      <input type="hidden" name="id" id="editId">
      <label>Full Name</label><input type="text" name="fullname" id="editName" required>
      <label>Email</label><input type="email" name="email" id="editEmail" required>
      <label>Role</label>
      <select name="role" id="editRole" required>
        <option value="client">Client</option>
        <option value="admin">Admin</option>
      </select>
      <button type="submit" class="add-btn" style="width:100%;">Update User</button>
    </form>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {
//  modals
  const addModal = document.getElementById("userModal");
  const openAdd = document.getElementById("openModal");
  const closeAdd = document.getElementById("closeModal");

  const editModal = document.getElementById("editModal");
  const closeEdit = document.getElementById("closeEdit");

  openAdd.addEventListener("click", () => addModal.style.display = "flex");
  closeAdd.addEventListener("click", () => addModal.style.display = "none");
  closeEdit.addEventListener("click", () => editModal.style.display = "none");

  // Close modals when clicking outside
  window.addEventListener("click", e => {
    if (e.target === addModal) addModal.style.display = "none";
    if (e.target === editModal) editModal.style.display = "none";
  });

  // edit button
  document.querySelectorAll(".edit-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.getElementById("editId").value = btn.dataset.id;
      document.getElementById("editName").value = btn.dataset.name;
      document.getElementById("editEmail").value = btn.dataset.email;
      document.getElementById("editRole").value = btn.dataset.role;
      editModal.style.display = "flex";
    });
  });

  // tablke and filter
  const table = document.querySelector("table");
  const headers = table.querySelectorAll("th");
  let sortDirection = 1;

  const searchInput = document.getElementById("searchInput");
  const roleFilter = document.getElementById("roleFilter");
  const minId = document.getElementById("minId");
  const maxId = document.getElementById("maxId");
  const filterBtn = document.getElementById("filterBtn");

  function filterRows() {
    const text = searchInput.value.toLowerCase();
    const roleValue = roleFilter.value.toLowerCase();
    const min = parseInt(minId.value) || Number.MIN_SAFE_INTEGER;
    const max = parseInt(maxId.value) || Number.MAX_SAFE_INTEGER;

    const rows = table.querySelectorAll("tbody tr");
    rows.forEach(row => {
      const cells = row.querySelectorAll("td");
      const id = parseInt(cells[0].textContent);
      const fullname = cells[1].textContent.toLowerCase();
      const email = cells[2].textContent.toLowerCase();
      const role = cells[3].textContent.toLowerCase();

      const matchesText = fullname.includes(text) || email.includes(text);
      const matchesRole = roleValue === "" || role === roleValue;
      const matchesId = id >= min && id <= max;

      row.style.display = (matchesText && matchesRole && matchesId) ? "" : "none";
    });
  }
  
  filterBtn.addEventListener("click", filterRows);
// press enter on searchbar
  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault(); // stops form submission 
      filterRows();       
    }
  });

//  sorting
  headers.forEach((header, index) => {
    header.style.cursor = "pointer";
    header.addEventListener("click", () => {
      const tbody = table.querySelector("tbody");
      const rows = Array.from(tbody.querySelectorAll("tr"));

      rows.sort((a, b) => {
        const aText = a.querySelectorAll("td")[index].textContent.trim().toLowerCase();
        const bText = b.querySelectorAll("td")[index].textContent.trim().toLowerCase();

        if (!isNaN(aText) && !isNaN(bText)) {
          return (Number(aText) - Number(bText)) * sortDirection;
        } else {
          return aText.localeCompare(bText) * sortDirection;
        }
      });

      tbody.innerHTML = "";
      rows.forEach(row => tbody.appendChild(row));

      sortDirection *= -1;

      // Reset header backgrounds
      headers.forEach(h => h.style.background = "#003366");
      header.style.background = "#0055aa";
    });
  });
});
</script>

</body>
</html>
