function renderRecordsTable(records) {
  const tbody = document.querySelector("#recordsTable tbody");
  tbody.innerHTML = "";
  records.forEach((r) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${r.id}</td>
      <td>${escapeHtml(r.title)}</td>
      <td>${escapeHtml(r.description)}</td>
      <td>${r.dateCreated}</td>
      <td>
        <button class="btn subtle btn-edit" data-id="${r.id}">Edit</button>
        <button class="btn" style="background:#fee2e2;color:#b91c1c" data-id="${r.id}" data-action="delete">Delete</button>
      </td>`;
    tbody.appendChild(tr);
  });
}

function escapeHtml(s) {
  if (!s) return "";
  return s.replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;");
}

function getRecords() {
  return JSON.parse(localStorage.getItem("rms_records") || "[]");
}

function saveRecords(records) {
  localStorage.setItem("rms_records", JSON.stringify(records));
}

function addRecord(formData) {
  const records = getRecords();
  const newRecord = {
    id: Date.now(),
    title: formData.get("title"),
    description: formData.get("description"),
    dateCreated: formData.get("dateCreated"),
  };
  records.push(newRecord);
  saveRecords(records);
  return { success: true, record: newRecord };
}

function updateRecord(formData) {
  const records = getRecords();
  const id = Number(formData.get("id"));
  const index = records.findIndex((r) => r.id === id);
  if (index > -1) {
    records[index].title = formData.get("title");
    records[index].description = formData.get("description");
    records[index].dateCreated = formData.get("dateCreated");
    saveRecords(records);
    return { success: true, record: records[index] };
  }
  return { success: false, message: "Record not found" };
}

function deleteRecord(id) {
  let records = getRecords();
  records = records.filter((r) => r.id !== Number(id));
  saveRecords(records);
  return { success: true };
}
