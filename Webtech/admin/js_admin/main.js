
document.addEventListener("DOMContentLoaded", () => {
  loadDashboard();
});

function loadDashboard() {
  const dashboardData = {
    users: 34,
    records: 210,
    staff: 5,
    logs: 142
  };

  // Update DOM
  document.getElementById("userCount").textContent = dashboardData.users;
  document.getElementById("recordCount").textContent = dashboardData.records;
  document.getElementById("staffCount").textContent = dashboardData.staff;
  document.getElementById("logCount").textContent = dashboardData.logs;
}

function logout() {
  alert("Logging out...");

  window.location.href = "login.html";
}
