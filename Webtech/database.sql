-- Database: record_management (generic placeholders)
CREATE DATABASE IF NOT EXISTS record_management;
USE record_management;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255)
);

DROP TABLE IF EXISTS records;
CREATE TABLE records (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  description TEXT,
  date_created DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Sample users (plain text passwords for demo)
INSERT INTO users (name, email, password) VALUES
('James', 'james@example.com', '12345'),
('Levi', 'levi@example.com', '12345');

-- Sample records
INSERT INTO records (title, description, date_created) VALUES
('Project Proposal', 'Initial draft for upcoming system project.', '2025-10-10'),
('Meeting Notes', 'Summary of team discussion held on Oct 12.', '2025-10-12'),
('Budget Plan', 'Draft budget breakdown for Q4 operations.', '2025-10-14');
