-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2026 at 07:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: property_db
--

-- --------------------------------------------------------

--
-- Table structure for table categories
--

CREATE TABLE categories (
  id int(11) NOT NULL,
  category_name varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table categories
--

INSERT INTO categories (id, category_name) VALUES
(2, 'Furniture'),
(1, 'IT Hardware'),
(4, 'Networking Equipment'),
(3, 'Office Appliances');

-- --------------------------------------------------------

--
-- Table structure for table history_log
--

CREATE TABLE history_log (
  id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  action_type varchar(50) NOT NULL,
  property_code varchar(50) DEFAULT NULL,
  details text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table history_log
--

INSERT INTO history_log (id, user_id, action_type, property_code, details, created_at) VALUES
(1, 1, 'Edit', 'PROP-2026-019', 'Updated details for property: manok na gray', '2026-04-11 09:19:52'),
(2, 1, 'Edit', 'PROP-2026-019', 'Updated details for property: manok na gray', '2026-04-11 09:32:22'),
(3, 1, 'Edit', 'PROP-2026-019', 'Updated details for property: manok na gray', '2026-04-11 09:32:31'),
(4, 1, 'Delete', 'PROP-2026-019', 'Deleted asset: manok na gray', '2026-04-11 09:32:34'),
(5, 1, 'Delete', 'PROP-2026-015', 'Permanently removed asset: computer set 42U', '2026-04-11 10:00:15'),
(6, 1, 'Edit', 'PROP-2026-014', 'Updated details for property: Canon EOS R6 Camera', '2026-04-11 10:14:12'),
(7, 1, 'Add', 'PROP-2026-020', 'Added new property: lanovo ', '2026-04-11 10:14:50'),
(8, 1, 'Edit', 'PROP-2026-020', 'Updated details for property: manok na gray', '2026-04-11 10:15:03'),
(9, 1, 'Edit', 'PROP-2026-020', 'Updated details for property: manok na gray', '2026-04-11 10:15:53');

-- --------------------------------------------------------

--
-- Table structure for table properties
--

CREATE TABLE properties (
  id int(11) NOT NULL,
  property_code varchar(20) NOT NULL,
  item_name varchar(100) NOT NULL,
  category_id int(11) DEFAULT NULL,
  status enum('Available','In Use','Maintenance','Disposed') DEFAULT 'Available',
  purchase_date date DEFAULT NULL,
  value decimal(10,2) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table properties
--

INSERT INTO properties (id, property_code, item_name, category_id, status, purchase_date, value, created_at) VALUES
(1, 'PROP-2026-001', 'Dell XPS 15 Laptop', 1, 'In Use', '2026-01-15', 75000.00, '2026-04-11 07:06:37'),
(2, 'PROP-2026-002', 'Ergonomic Office Chair', 2, 'Available', '2026-02-10', 8500.00, '2026-04-11 07:06:37'),
(3, 'PROP-2026-003', 'Logitech MX Master 3S', 1, 'In Use', '2026-02-12', 5500.00, '2026-04-11 07:06:37'),
(4, 'PROP-2026-004', 'Samsung 32-inch 4K Monitor', 1, 'Available', '2025-11-20', 18000.00, '2026-04-11 07:06:37'),
(5, 'PROP-2026-005', 'Cisco C9200L Switch', 4, 'Maintenance', '2026-03-05', 120000.00, '2026-04-11 07:06:37'),
(6, 'PROP-2026-006', 'Standing Desk - Walnut', 2, 'In Use', '2026-01-20', 22000.00, '2026-04-11 07:06:37'),
(7, 'PROP-2026-007', 'Epson EcoTank L3210', 3, 'Available', '2026-03-15', 9500.00, '2026-04-11 07:06:37'),
(8, 'PROP-2026-008', 'MacBook Pro M3 Max', 1, 'In Use', '2026-03-01', 165000.00, '2026-04-11 07:06:37'),
(9, 'PROP-2026-009', 'Ubiquiti UniFi AP 6', 4, 'Available', '2026-02-25', 11000.00, '2026-04-11 07:06:37'),
(10, 'PROP-2026-010', 'Conference Table (8 Seater)', 2, 'Available', '2025-12-10', 45000.00, '2026-04-11 07:06:37'),
(11, 'PROP-2026-011', 'Industrial Water Dispenser', 3, 'In Use', '2026-01-05', 12500.00, '2026-04-11 07:06:37'),
(12, 'PROP-2026-012', 'Sony WH-1000XM5 Headphones', 1, 'In Use', '2026-04-02', 19000.00, '2026-04-11 07:06:37'),
(13, 'PROP-2026-013', 'Whiteboard (4x6 feet)', 2, 'Available', '2026-01-12', 3500.00, '2026-04-11 07:06:37'),
(14, 'PROP-2026-014', 'Canon EOS R6 Camera', 1, 'Available', '2024-05-20', 135000.00, '2026-04-11 07:06:37'),
(20, 'PROP-2026-020', 'manok na gray', 2, 'Maintenance', '2026-04-01', 99999999.99, '2026-04-11 10:14:50');

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE users (
  id int(11) NOT NULL,
  username varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  role enum('Admin','Staff') DEFAULT 'Staff',
  last_login datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table users
--

INSERT INTO users (id, username, password, role, last_login) VALUES
(1, 'admin', 'admin123', 'Admin', '2026-04-11 18:55:00'),
(4, 'brix', '123', 'Staff', '2026-04-11 18:16:02');


--
-- Indexes for dumped tables
--

--
-- Indexes for table categories
--
ALTER TABLE categories
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY category_name (category_name);

--
-- Indexes for table history_log
--
ALTER TABLE history_log
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id);

--
-- Indexes for table properties
--
ALTER TABLE properties
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY property_code (property_code),
  ADD KEY category_id (category_id);

--
-- Indexes for table users
--
ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY username (username);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table categories
--
ALTER TABLE categories
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table history_log
--
ALTER TABLE history_log
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table properties
--
ALTER TABLE properties
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table users
--
ALTER TABLE users
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table history_log
--
ALTER TABLE history_log
  ADD CONSTRAINT history_log_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);

--
-- Constraints for table properties
--
ALTER TABLE properties
  ADD CONSTRAINT properties_ibfk_1 FOREIGN KEY (category_id) REFERENCES categories (id);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
