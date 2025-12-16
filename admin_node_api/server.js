const express = require("express");
const cors = require("cors");
const mysql = require("mysql2/promise");
const dbConfig = require("./db.config");

const app = express();
const PORT = 5000;

app.use(cors());
app.use(express.json());

const pool = mysql.createPool(dbConfig);

/* Health check */
app.get("/health", (req, res) => {
  res.json({ status: "ok" });
});

/* Admin login validation */
app.post("/admin/login", async (req, res) => {
  const { email, password } = req.body;

  if (!email || !password) {
    return res.status(400).json({ error: "Missing credentials" });
  }

  try {
    const [rows] = await pool.query(
      "SELECT user_id, role FROM users WHERE email = ? AND password = ?",
      [email, password]
    );

    if (rows.length === 0 || rows[0].role !== "admin") {
      return res.status(401).json({ error: "Invalid admin credentials" });
    }

    res.json({
      success: true,
      user_id: rows[0].user_id,
      role: rows[0].role
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Login error" });
  }
});

/* Get all users (admin) */
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
