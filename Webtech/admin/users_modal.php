<div id="userModal" class="modal">
  <div class="modal-content">
    <span id="closeModal" class="close">&times;</span>
    <h3>Add User</h3>

    <form method="POST">
      <input type="hidden" name="add_user" value="1">

      <label>Full Name</label>
      <input type="text" name="fullname" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Role</label>
      <select name="role">
        <option value="admin">Admin</option>
        <option value="client">Client</option>
      </select>

      <button type="submit" class="save-btn">Save User</button>
    </form>
  </div>
</div>

<div id="editModal" class="modal">
  <div class="modal-content">
    <span id="closeEdit" class="close">&times;</span>
    <h3>Edit User</h3>

    <form method="POST">
      <input type="hidden" name="edit_user" value="1">
      <input type="hidden" id="editId" name="id">

      <label>Full Name</label>
      <input type="text" id="editName" name="fullname" required>

      <label>Email</label>
      <input type="email" id="editEmail" name="email" required>

      <label>Role</label>
      <select id="editRole" name="role">
        <option value="admin">Admin</option>
        <option value="client">Client</option>
      </select>

      <button type="submit" class="save-btn">Update User</button>
    </form>
  </div>
</div>

<style>
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: #fff;
  padding: 20px;
  width: 350px;
  border-radius: 10px;
}

.close {
  float: right;
  cursor: pointer;
  font-size: 20px;
}
</style>
