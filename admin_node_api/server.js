const express = require("express");
const cors = require("cors");
const mysql = require("mysql2/promise");
const dbConfig = require("./db.config");

const app = express();
const PORT = 5000;

app.use(cors());
app.use(express.json());

const pool = mysql.createPool(dbConfig);

app.get("/health", (req, res) => {
  res.json({ status: "ok" });
});

app.get("/admin/users", async (req, res) => {
  try {
    const [rows] = await pool.query(
      "SELECT user_id, fullname, email, role FROM users"
    );
    res.json(rows);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Database error" });
  }
});

app.listen(PORT, "0.0.0.0", () => {
  console.log(`Admin API running on http://127.0.0.1:${PORT}`);
});
