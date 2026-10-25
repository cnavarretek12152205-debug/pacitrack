CREATE DATABASE IF NOT EXISTS pacitrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pacitrack;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('Administrator','Operations User') NOT NULL DEFAULT 'Operations User',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS equipment (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  equipment_id VARCHAR(40) NOT NULL UNIQUE,
  name VARCHAR(180) NOT NULL,
  client VARCHAR(180) NOT NULL,
  location VARCHAR(140) NOT NULL,
  status ENUM('In service','Maintenance due','Under repair','In storage','Retired') NOT NULL DEFAULT 'In service',
  due_date DATE NOT NULL,
  warranty_expiry DATE NULL,
  equipment_type VARCHAR(100) NOT NULL DEFAULT 'General',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(100) NOT NULL,
  details TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Demo password for both accounts: PaciTrack2025!
INSERT IGNORE INTO users (full_name,email,password_hash,role) VALUES
('Alex Mendoza','admin@pacificsurgical.ph','$2y$12$x4maQ4YFxHLLu7q1rOD2IOljs93Mgi3C208VSRB5Jej1IaA8jRR.','Administrator'),
('Operations User','operations@pacificsurgical.ph','$2y$12$x4maQ4YFxHLLu7q1rOD2IOljs93Mgi3C208VSRB5Jej1IaA8jRR.','Operations User');

INSERT IGNORE INTO equipment (equipment_id,name,client,location,status,due_date,warranty_expiry,equipment_type) VALUES
('EQ-2048','Arthroscopic Camera System','St. Luke’s Medical Center','Quezon City','In service','2026-11-18','2026-11-15','Capital / OR'),
('EQ-1964','Spinal Instrument Set','Makati Medical Center','Makati City','Maintenance due','2026-10-28','2027-04-28','Spine'),
('EQ-1892','Surgical Power Tool','Chong Hua Hospital','Cebu City','In service','2026-12-12','2027-06-12','Joints'),
('EQ-1765','Endoscopic Imaging Tower','Davao Doctors Hospital','Davao City','Under repair','2026-11-02','2026-11-10','Capital / OR'),
('EQ-1722','Laparoscopic Instrument Set','Asian Hospital and Medical Center','Muntinlupa City','In storage','2026-12-01','2027-03-01','Capital / OR');
