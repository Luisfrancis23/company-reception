-- =========================================================
-- Company Reception Management System
-- Database: company_reception
-- Import this file in phpMyAdmin to set everything up.
-- =========================================================

CREATE DATABASE IF NOT EXISTS company_reception;
USE company_reception;

DROP TABLE IF EXISTS visitors;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------
-- Table: users
-- Stores receptionist login accounts
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    fullname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    security_question VARCHAR(255) NOT NULL,
    security_answer VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Table: visitors
-- Stores every visitor record with full details
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS visitors (
    id INT(11) NOT NULL AUTO_INCREMENT,
    visitor_code VARCHAR(20) NOT NULL UNIQUE,
    visitor_name VARCHAR(100) NOT NULL,
    gender VARCHAR(10) DEFAULT NULL,
    company VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    id_number VARCHAR(50) DEFAULT NULL,
    purpose VARCHAR(255) NOT NULL,
    department VARCHAR(100) NOT NULL,
    employee_to_meet VARCHAR(100) NOT NULL,
    visit_date DATE NOT NULL,
    check_in DATETIME DEFAULT NULL,
    check_out DATETIME DEFAULT NULL,
    duration VARCHAR(50) DEFAULT NULL,
    status ENUM('Waiting','Approved','In Meeting','Checked Out') NOT NULL DEFAULT 'Waiting',
    photo VARCHAR(255) DEFAULT NULL,
    signature_in VARCHAR(255) DEFAULT NULL,
    signature_out VARCHAR(255) DEFAULT NULL,
    created_by INT(11) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_visitor_code (visitor_code),
    INDEX idx_visit_date (visit_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Sample login account
-- Username: admin
-- Password: admin123
-- Security Question: What is your favorite color?
-- Security Answer: blue
-- ---------------------------------------------------------
INSERT INTO users (fullname, username, password, security_question, security_answer)
VALUES (
    'Jane Receptionist',
    'admin',
    '$2y$12$/bnegCgzv/H8CRbwfl0wF.vy0fa0ubHks5M0QHvu5RCkapS1OrUfS',
    'What is your favorite color?',
    '$2y$12$gFSSL18fwDPssTWZn/C11enUtmQRIixE9DFqHG5vqsLwgJnCHghfm'
);
-- Username: admin / Password: admin123
-- Security answer "blue" is hashed using password_hash().

-- ---------------------------------------------------------
-- Sample visitor data (20+ records)
-- ---------------------------------------------------------
INSERT INTO visitors (visitor_code, visitor_name, gender, company, phone, email, address, id_number, purpose, department, employee_to_meet, visit_date, check_in, check_out, duration, status) VALUES
('VIS-0001', 'John Cruz', 'Male', 'ABC Trading', '09171234567', 'john@abc.com', '123 Rizal St, Manila', 'ID-001', 'Business Meeting', 'Sales', 'Mr. Santos', CURDATE(), CONCAT(CURDATE(), ' 09:00:00'), CONCAT(CURDATE(), ' 10:30:00'), '1 Hour 30 Minutes', 'Checked Out'),
('VIS-0002', 'Maria Lopez', 'Female', 'XYZ Corp', '09281234567', 'maria@xyz.com', '456 Mabini St, Quezon City', 'ID-002', 'Job Interview', 'Human Resources', 'HR Manager', CURDATE(), CONCAT(CURDATE(), ' 10:30:00'), NULL, NULL, 'In Meeting'),
('VIS-0003', 'Peter Tan', 'Male', 'Freelancer', '09391234567', 'peter@email.com', '789 Luna St, Makati', 'ID-003', 'Deliver Documents', 'Admin', 'Ms. Reyes', CURDATE(), CONCAT(CURDATE(), ' 11:00:00'), NULL, NULL, 'Waiting'),
('VIS-0004', 'Ana Santos', 'Female', 'Tech Solutions', '09182345678', 'ana@techsol.com', '321 Bonifacio St, Taguig', 'ID-004', 'Product Demo', 'IT Department', 'Mr. Garcia', CURDATE(), CONCAT(CURDATE(), ' 13:00:00'), NULL, NULL, 'Approved'),
('VIS-0005', 'Robert Lim', 'Male', 'Global Trade Inc', '09451234567', 'robert@global.com', '654 Aguinaldo St, Pasig', 'ID-005', 'Supplier Meeting', 'Procurement', 'Ms. Tan', DATE_SUB(CURDATE(), INTERVAL 1 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 08:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 10:00:00'), '1 Hour 30 Minutes', 'Checked Out'),
('VIS-0006', 'Lisa Chua', 'Female', 'Chua Enterprises', '09561234567', 'lisa@chuaent.com', '987 Dela Cruz St, Mandaluyong', 'ID-006', 'Audit', 'Finance', 'Mr. Co', DATE_SUB(CURDATE(), INTERVAL 1 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 12:00:00'), '3 Hours 0 Minutes', 'Checked Out'),
('VIS-0007', 'Mike Reyes', 'Male', 'Reyes Law Firm', '09781234567', 'mike@reyeslaw.com', '147 Paterno St, Manila', 'ID-007', 'Legal Consultation', 'Legal', 'Atty. Cruz', DATE_SUB(CURDATE(), INTERVAL 1 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 14:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 1 DAY), ' 15:30:00'), '1 Hour 30 Minutes', 'Checked Out'),
('VIS-0008', 'Sofia Gonzales', 'Female', 'Gonzales Group', '09991234567', 'sofia@gonzales.com', '258 Rizal Ave, Caloocan', 'ID-008', 'Partnership Discussion', 'Business Dev', 'Mr. Gonzales', DATE_SUB(CURDATE(), INTERVAL 2 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 10:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 11:00:00'), '1 Hour 0 Minutes', 'Checked Out'),
('VIS-0009', 'David Fernandez', 'Male', 'Fernandez Builders', '09173456789', 'david@fbuilders.com', '369 Magsaysay St, Las Pinas', 'ID-009', 'Project Proposal', 'Engineering', 'Engr. Santos', DATE_SUB(CURDATE(), INTERVAL 2 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 09:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 2 DAY), ' 11:45:00'), '2 Hours 15 Minutes', 'Checked Out'),
('VIS-0010', 'Grace Torres', 'Female', 'Torres Medical', '09456789123', 'grace@torresmed.com', '741 Salud St, Paranaque', 'ID-010', 'Medical Equipment Demo', 'Facilities', 'Dr. Santos', DATE_SUB(CURDATE(), INTERVAL 3 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 13:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 14:00:00'), '1 Hour 0 Minutes', 'Checked Out'),
('VIS-0011', 'Henry Sy', 'Male', 'Sy Logistics', '09567891234', 'henry@sylog.com', '852 Mabuhay St, Muntinlupa', 'ID-011', 'Logistics Contract', 'Operations', 'Mr. Lim', DATE_SUB(CURDATE(), INTERVAL 3 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 10:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 3 DAY), ' 11:30:00'), '1 Hour 30 Minutes', 'Checked Out'),
('VIS-0012', 'Isabel Cruz', 'Female', 'Cruz Accounting', '09678912345', 'isabel@cruzacc.com', '963 Katipunan St, Quezon City', 'ID-012', 'Tax Consultation', 'Finance', 'Mr. Reyes', DATE_SUB(CURDATE(), INTERVAL 4 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 10:30:00'), '1 Hour 30 Minutes', 'Checked Out'),
('VIS-0013', 'Jack Rivera', 'Male', 'Rivera Marketing', '09789123456', 'jack@riveramkt.com', '159 Ortigas Ave, Pasig', 'ID-013', 'Marketing Campaign', 'Marketing', 'Ms. Go', DATE_SUB(CURDATE(), INTERVAL 4 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 14:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 4 DAY), ' 16:00:00'), '2 Hours 0 Minutes', 'Checked Out'),
('VIS-0014', 'Karen Lee', 'Female', 'Lee Innovations', '09891234567', 'karen@leeinnov.com', '753 Shaw Blvd, Mandaluyong', 'ID-014', 'Innovation Pitch', 'R&D', 'Dr. Tan', DATE_SUB(CURDATE(), INTERVAL 5 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 11:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 12:00:00'), '1 Hour 0 Minutes', 'Checked Out'),
('VIS-0015', 'Mark Villanueva', 'Male', 'Villanueva Corp', '09123456789', 'mark@villcorp.com', '456 Taft Ave, Manila', 'ID-015', 'Board Meeting', 'Executive', 'CEO', DATE_SUB(CURDATE(), INTERVAL 5 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 08:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 5 DAY), ' 10:00:00'), '2 Hours 0 Minutes', 'Checked Out'),
('VIS-0016', 'Nancy Castillo', 'Female', 'Castillo Printing', '09234567891', 'nancy@castprint.com', '789 Aurora Blvd, Quezon City', 'ID-016', 'Printing Services', 'Admin', 'Ms. Reyes', DATE_SUB(CURDATE(), INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 10:00:00'), '1 Hour 0 Minutes', 'Checked Out'),
('VIS-0017', 'Oscar Mendoza', 'Male', 'Mendoza Transport', '09345678912', 'oscar@mendotrans.com', '123 Rizal Ave, Pasay', 'ID-017', 'Fleet Management', 'Logistics', 'Mr. Sy', DATE_SUB(CURDATE(), INTERVAL 6 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 13:30:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 6 DAY), ' 15:00:00'), '1 Hour 30 Minutes', 'Checked Out'),
('VIS-0018', 'Patricia Ong', 'Female', 'Ong Realty', '09456789123', 'pat@ongrealty.com', '456 Roxas Blvd, Manila', 'ID-018', 'Property Inspection', 'Facilities', 'Mr. Cruz', DATE_SUB(CURDATE(), INTERVAL 7 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 10:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 11:30:00'), '1 Hour 30 Minutes', 'Checked Out'),
('VIS-0019', 'Quinn Salvador', 'Male', 'Salvador Tech', '09567891234', 'quinn@salvtech.com', '789 Commonwealth Ave, Quezon City', 'ID-019', 'Software Demo', 'IT Department', 'Mr. Garcia', DATE_SUB(CURDATE(), INTERVAL 7 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 14:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 7 DAY), ' 16:30:00'), '2 Hours 30 Minutes', 'Checked Out'),
('VIS-0020', 'Rosa Dimagiba', 'Female', 'Dimagiba Events', '09678912345', 'rosa@dimagiba.com', '123 EDSA, Mandaluyong', 'ID-020', 'Event Planning', 'Marketing', 'Ms. Tan', DATE_SUB(CURDATE(), INTERVAL 8 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 09:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 8 DAY), ' 11:00:00'), '2 Hours 0 Minutes', 'Checked Out'),
('VIS-0021', 'Sam Torres', 'Male', 'Torres Consulting', '09789123456', 'sam@torrescon.com', '456 Ayala Ave, Makati', 'ID-021', 'Consultancy', 'Business Dev', 'Mr. Gonzales', DATE_SUB(CURDATE(), INTERVAL 9 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 9 DAY), ' 10:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 9 DAY), ' 12:00:00'), '2 Hours 0 Minutes', 'Checked Out'),
('VIS-0022', 'Tina Hernandez', 'Female', 'Hernandez Foods', '09891234567', 'tina@hernanfoods.com', '789 Banawe St, Quezon City', 'ID-022', 'Food Supply Contract', 'Procurement', 'Ms. Lim', DATE_SUB(CURDATE(), INTERVAL 10 DAY), CONCAT(DATE_SUB(CURDATE(), INTERVAL 10 DAY), ' 11:00:00'), CONCAT(DATE_SUB(CURDATE(), INTERVAL 10 DAY), ' 12:30:00'), '1 Hour 30 Minutes', 'Checked Out');
