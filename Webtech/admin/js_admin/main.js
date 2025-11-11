document.addEventListener("DOMContentLoaded", () => {
  loadDashboard();

  // logout
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault(); // stops page from jumping to top
      window.location.href = "../login.php";
    });
  }
});

function loadDashboard() {
  const dashboardData = {
    users: 34,
    records: 210,
    staff: 5,
    logs: 142
  };

  document.getElementById("userCount").textContent = dashboardData.users;
  document.getElementById("recordCount").textContent = dashboardData.records;
  document.getElementById("staffCount").textContent = dashboardData.staff;
  document.getElementById("logCount").textContent = dashboardData.logs;
}
