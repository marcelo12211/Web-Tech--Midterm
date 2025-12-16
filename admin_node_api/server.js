const express = require("express");
const mysql = require("mysql2/promise");
const dbConfig = require("./db.config");
const cors = require("cors");
const app = express();
const port = 3000;
app.use(
  cors({
    origin: "http://localhost/",
    methods: ["GET"],
    allowedHeaders: ["Content-Type"],
  })
);
app.use(express.json());
const pool = mysql.createPool(dbConfig);
app.get("/api/users", async (req, res) => {
  const { search, role, status } = req.query;

  let sql = "SELECT user_id, fullname, email, role, status FROM users";
  let whereClauses = [];
  let params = [];
  if (search) {
    whereClauses.push("(fullname LIKE ? OR email LIKE ?)");
    const searchTerm = `%${search}%`;
    params.push(searchTerm, searchTerm);
  }

  if (role) {
    whereClauses.push("role = ?");
    params.push(role);
  }

  if (status) {
    whereClauses.push("status = ?");
    params.push(status);
  }

  if (whereClauses.length > 0) {
    sql += " WHERE " + whereClauses.join(" AND ");
  }

  sql += " ORDER BY fullname ASC";

  try {
    const [users] = await pool.query(sql, params);
    res.json({
      users: users,
      count: users.length,
      error: null,
    });
  } catch (error) {
    console.error("Database Query Error:", error);
    res.status(500).json({
      users: [],
      count: 0,
      error: "Failed to fetch users from database.",
    });
  }
});
app.listen(port, () => {
  console.log(`Node.js Admin API is LIVE at http://localhost:${port}`);
});
