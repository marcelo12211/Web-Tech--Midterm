document.addEventListener("DOMContentLoaded", () => {
  const clientBtn = document.getElementById("clientBtn");
  const adminBtn = document.getElementById("adminBtn");
  const roleInput = document.getElementById("roleInput");
  const createAccountLink = document.getElementById("createAccountLink");

  // Default role
  let selectedRole = "client";

  // Switch to Client
  clientBtn.addEventListener("click", () => {
    selectedRole = "client";
    roleInput.value = "client"; // hidden input in login form
    createAccountLink.href = "register.php?role=client"; // update create account link
    clientBtn.classList.add("active");
    adminBtn.classList.remove("active");
  });

  // Switch to Admin
  adminBtn.addEventListener("click", () => {
    selectedRole = "admin";
    roleInput.value = "admin";
    createAccountLink.href = "register.php?role=admin";
    adminBtn.classList.add("active");
    clientBtn.classList.remove("active");
  });
});
