document.addEventListener("DOMContentLoaded", () => {
  const addModal = document.getElementById("userModal");
  const openAdd = document.getElementById("openModal");
  const closeAdd = document.getElementById("closeModal");

  const editModal = document.getElementById("editModal");
  const closeEdit = document.getElementById("closeEdit");

  openAdd.addEventListener("click", () => addModal.style.display = "flex");
  closeAdd.addEventListener("click", () => addModal.style.display = "none");
  closeEdit.addEventListener("click", () => editModal.style.display = "none");

  window.addEventListener("click", e => {
    if (e.target === addModal) addModal.style.display = "none";
    if (e.target === editModal) editModal.style.display = "none";
  });

  document.querySelectorAll(".edit-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      document.getElementById("editId").value = btn.dataset.id;
      document.getElementById("editName").value = btn.dataset.name;
      document.getElementById("editEmail").value = btn.dataset.email;
      document.getElementById("editRole").value = btn.dataset.role;
      editModal.style.display = "flex";
    });
  });

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

  searchInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      filterRows();
    }
  });

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

      headers.forEach(h => h.style.background = "#003366");
      header.style.background = "#0055aa";
    });
  });
});
