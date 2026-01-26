const roleInput = document.getElementById("roleInput");
const clientBtn = document.getElementById("clientBtn");
const adminBtn = document.getElementById("adminBtn");

if (clientBtn && adminBtn) {
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
}
