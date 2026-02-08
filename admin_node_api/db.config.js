module.exports = {
  host: process.env.DB_HOST || "127.0.0.1",
  user: process.env.DB_USER || "root",
  password: process.env.DB_PASSWORD || "123",
  database: process.env.DB_NAME || "happyhalloww",
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
};
