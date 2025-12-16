console.log("=== SERVER VERSION WITH HEALTH ROUTE LOADED ===");

const express = require("express");
const cors = require("cors");

const app = express();
const PORT = 5000;

app.use(cors());
app.use(express.json());

app.get("/health", (req, res) => {
  res.json({ status: "ok" });
});

app.get("/admin/users", (req, res) => {
  res.json({ message: "Admin users endpoint ready" });
});

app.listen(PORT, "0.0.0.0", () => {
  console.log(`Admin API running on http://127.0.0.1:${PORT}`);
});
