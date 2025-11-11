// users.js — handles Add + Edit User modals and interactions
document.addEventListener("DOMContentLoaded", () => {
  const addModal = document.getElementById("userModal");
  const editModal = document.getElementById("editModal");
  const openModalBtn = document.getElementById("openModal");
  const closeAdd = document.getElementById("closeModal");
  const closeEdit = document.getElementById("closeEdit");

  // Open Add User modal
  if (openModalBtn) {
    openModalBtn.addEventListener("click", () => {
      addModal.style.display = "flex";
    });
  }

  // Close modals
  [closeAdd, closeEdit].forEach(btn => {
    if (btn) btn.addEventListener("click", () => {
      addModal.style.display = "none";
      editModal.style.display = "none";
    });
  });

  // Close modal if clicking outside
  window.addEventListener("click", (e) => {
    if (e.target === addModal) addModal.style.display = "none";
    if (e.target === editModal) editModal.style.display = "none";
  });

  // Handle Edit buttons
  const editButtons = document.querySelectorAll(".edit-btn");
  editButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      document.getElementById("editId").value = btn.dataset.id;
      document.getElementById("editName").value = btn.dataset.name;
      document.getElementById("editEmail").value = btn.dataset.email;
      document.getElementById("editRole").value = btn.dataset.role;
      editModal.style.display = "flex";
    });
  });
});
