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

/*DASHBOARD STATSSS*/
app.get("/admin/dashboard/stats", async (req, res) => {
  try {
    const [[total]] = await pool.query(
      "SELECT COUNT(*) AS count FROM residents"
    );

    const [[senior]] = await pool.query(
      "SELECT COUNT(*) AS count FROM residents WHERE is_senior = 1"
    );

    const [[pwd]] = await pool.query(
      "SELECT COUNT(*) AS count FROM residents WHERE is_disabled = 1"
    );

    const [[pregnant]] = await pool.query(
      "SELECT COUNT(*) AS count FROM residents WHERE is_pregnant = 1"
    );

    const regular =
      total.count - (senior.count + pwd.count + pregnant.count);

    res.json({
      total_residents: total.count,
      senior: senior.count,
      pwd: pwd.count,
      pregnant: pregnant.count,
      regular: regular < 0 ? 0 : regular,
      total_children: 5
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Failed to fetch dashboard stats" });
  }
});
/*for manage residents fetch */
app.get("/admin/residents", async (req, res) => {
  try {
    const { search = "", purok = "", category = "" } = req.query;

    let sql = "SELECT * FROM residents WHERE 1=1";
    let params = [];

    if (search) {
      sql += " AND (first_name LIKE ? OR surname LIKE ? OR person_id = ?)";
      params.push(`%${search}%`, `%${search}%`, isNaN(search) ? 0 : Number(search));
    }

    if (purok) {
      sql += " AND purok = ?";
      params.push(Number(purok));
    }

    if (category === "senior") {
      sql += " AND is_senior = 1";
    } else if (category === "pwd") {
      sql += " AND is_disabled = 1";
    } else if (category === "pregnant") {
      sql += " AND is_pregnant = 1 AND sex = 'Female'";
    }

    sql += " ORDER BY surname ASC";

    const [rows] = await pool.query(sql, params);
    res.json(rows);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Failed to fetch residents" });
  }
});

app.listen(PORT, "0.0.0.0", () => {
  console.log(`Admin API running on http://127.0.0.1:${PORT}`);
});
