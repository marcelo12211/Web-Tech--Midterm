document.addEventListener("DOMContentLoaded", () => {
  const user = requireLogin();
  if (!user) return;

  document.getElementById("userName").textContent = user.name || "User";
  document.getElementById("logoutBtn").addEventListener("click", logout);

  const addBtn = document.getElementById("addRecordBtn");
  const modal = document.getElementById("recordModal");
  const modalTitle = document.getElementById("modalTitle");
  const recordForm = document.getElementById("recordForm");
  const cancelModal = document.getElementById("cancelModal");
  const tbody = document.querySelector("#recordsTable tbody");

  function loadRecords() {
    const records = JSON.parse(localStorage.getItem("rms_records") || "[]");
    renderRecordsTable(records);
  }

  loadRecords();

  function openModal(mode = "add", rec = null) {
    modal.classList.add("show");
    modal.setAttribute("aria-hidden", "false");
    if (mode === "add") {
      modalTitle.textContent = "Add Record";
      recordForm.reset();
      recordForm.id.value = "";
      recordForm.dateCreated.valueAsDate = new Date();
    } else {
      modalTitle.textContent = "Edit Record";
      recordForm.id.value = rec.id;
      recordForm.title.value = rec.title;
      recordForm.description.value = rec.description;
      recordForm.dateCreated.value = rec.dateCreated;
    }
  }

  function closeModal() {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
  }

  addBtn.addEventListener("click", () => openModal("add"));

  cancelModal.addEventListener("click", () => closeModal());

  recordForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const form = new FormData(recordForm);
    if (!form.get("title") || !form.get("dateCreated")) {
      alert("Please fill in required fields.");
      return;
    }

    if (form.get("id")) {
      const resp = updateRecord(form);
      if (resp.success) {
        loadRecords();
        closeModal();
      } else alert(resp.message || "Update failed");
    } else {
      const resp = addRecord(form);
      if (resp.success) {
        loadRecords();
        closeModal();
      } else alert(resp.message || "Add failed");
    }
  });

  tbody.addEventListener("click", (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const id = btn.dataset.id;

    if (btn.classList.contains("btn-edit")) {
      const records = JSON.parse(localStorage.getItem("rms_records") || "[]");
      const rec = records.find((r) => String(r.id) === String(id));
      if (rec) openModal("edit", rec);
      return;
    }

    if (btn.dataset.action === "delete") {
      if (!confirm("Delete this record?")) return;
      const resp = deleteRecord(id);
      if (resp.success) loadRecords();
      else alert(resp.message || "Delete failed");
    }
  });
});
