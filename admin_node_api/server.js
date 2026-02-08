const express = require("express");
const cors = require("cors");
const mysql = require("mysql2/promise");
const dbConfig = require("./db.config");
const path = require("path");
const multer = require("multer");
const fs = require("fs");
const app = express();
const PORT = 5000;
app.use(cors());
app.use(express.json());

app.use("/uploads", express.static(path.join(__dirname, "uploads")));
app.use(
  "/bootstrap",
  express.static(path.join(__dirname, "node_modules/bootstrap/dist"))
);

const pool = mysql.createPool(dbConfig);

const ensureDir = (dir) => {
  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true });
  }
};

ensureDir("uploads");
ensureDir("uploads/pwd_documents");
ensureDir("uploads/senior_documents");

app.get("/health", (req, res) => {
  res.json({ status: "ok" });
});

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
      role: rows[0].role,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Login error" });
  }
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

    const regular = total.count - (senior.count + pwd.count + pregnant.count);

    res.json({
      total_residents: total.count,
      senior: senior.count,
      pwd: pwd.count,
      pregnant: pregnant.count,
      regular: regular < 0 ? 0 : regular,
      total_children: 5,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Failed to fetch dashboard stats" });
  }
});

app.get("/admin/residents", async (req, res) => {
  try {
    const { search = "", purok = "", category = "" } = req.query;
    let sql = "SELECT * FROM residents WHERE 1=1";
    let params = [];

    if (search) {
      sql += " AND (first_name LIKE ? OR surname LIKE ? OR person_id = ?)";
      params.push(
        `%${search}%`,
        `%${search}%`,
        isNaN(search) ? 0 : Number(search)
      );
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
    console.error("Error fetching residents:", err.message);
    res.status(500).json({ error: "Failed to fetch residents", details: err.message });
  }
});

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    if (file.fieldname === "pwd_id_image") {
      cb(null, "uploads/pwd_documents");
    } else if (file.fieldname === "senior_id_image") {
      cb(null, "uploads/senior_documents");
    } else {
      cb(null, "uploads");
    }
  },
  filename: (req, file, cb) => {
    cb(null, Date.now() + "_" + file.originalname);
  },
});

const upload = multer({
  limits: { fileSize: 5 * 1024 * 1024 },
  storage,
});

app.put("/admin/residents/:id", async (req, res) => {
  try {
    const residentId = req.params.id;
    const data = req.body;
    await pool.query(
      `UPDATE residents SET
        household_id = ?, first_name = ?, middle_name = ?, surname = ?, suffix = ?,
        sex = ?, birthdate = ?, civil_status = ?, nationality = ?, religion = ?,
        purok = ?, address = ?, education_level = ?, occupation = ?, vaccination = ?,
        children_count = ?, is_senior = ?, is_disabled = ?, is_pregnant = ?, health_insurance = ?
       WHERE person_id = ?`,
      [
        data.household_id,
        data.first_name,
        data.middle_name || "",
        data.surname,
        data.suffix || "",
        data.sex,
        data.birthdate,
        data.civil_status,
        data.nationality,
        data.religion || null,
        data.purok,
        data.address,
        data.education_level || null,
        data.occupation || null,
        data.vaccination || null,
        data.children_count || 0,
        data.special_status === "Senior Citizen" ? 1 : 0,
        data.special_status === "PWD" ? 1 : 0,
        data.special_status === "Pregnant" ? 1 : 0,
        data.health_insurance || null,
        residentId,
      ]
    );
    res.json({ success: true });
  } catch (err) {
    console.error("UPDATE RESIDENT ERROR:", err);
    res.status(500).json({ error: "Failed to update resident" });
  }
});

app.post(
  "/admin/residents",
  upload.fields([
    { name: "pwd_id_image", maxCount: 1 },
    { name: "senior_id_image", maxCount: 1 },
  ]),
  async (req, res) => {
    try {
      const data = req.body;
      const [result] = await pool.query(
        `INSERT INTO residents
        (household_id, first_name, middle_name, surname, suffix, sex, birthdate,
         civil_status, nationality, religion, purok, address, education_level,
         occupation, vaccination, children_count, is_senior, is_disabled, is_pregnant)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)`,
        [
          data.household_id,
          data.first_name,
          data.middle_name || "",
          data.surname,
          data.suffix || "",
          data.sex,
          data.birthdate,
          data.civil_status,
          data.nationality,
          data.religion || null,
          data.purok,
          data.address,
          data.education_level,
          data.occupation || "",
          data.vaccination || "",
          data.children_count || 0,
          data.special_status === "Senior Citizen" ? 1 : 0,
          data.special_status === "PWD" ? 1 : 0,
          data.special_status === "Pregnant" ? 1 : 0,
        ]
      );

      const residentId = result.insertId;
      if (data.special_status === "PWD") {
        await pool.query(
          `INSERT INTO disabled_persons (resident_id, pwd_gov_id, disability_type, id_picture_path) VALUES (?,?,?,?)`,
          [
            residentId,
            data.pwd_gov_id,
            data.disability_type,
            req.files?.pwd_id_image?.[0]?.path || null,
          ]
        );
      }
      if (data.special_status === "Senior Citizen") {
        await pool.query(
          `INSERT INTO senior_citizens (resident_id, senior_gov_id, id_picture_path) VALUES (?,?,?)`,
          [
            residentId,
            data.senior_gov_id,
            req.files?.senior_id_image?.[0]?.path || null,
          ]
        );
      }
      res.json({ success: true });
    } catch (err) {
      console.error("ADD RESIDENT ERROR:", err);
      res.status(500).json({ error: "Failed to add resident" });
    }
  }
);
app.listen(PORT, "0.0.0.0", () => {
  console.log(`Admin API running on port ${PORT}`);
});
