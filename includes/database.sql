-- FeeFlow Database Structure
CREATE DATABASE IF NOT EXISTS feeflow_db;
USE feeflow_db;

-- 1. Institutes Table
CREATE TABLE IF NOT EXISTS `institutes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `recognition_text` varchar(255) DEFAULT NULL,
  `affiliation_text` varchar(255) DEFAULT NULL,
  `receipt_prefix` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `qr_payment_link` text DEFAULT NULL,
  `tnc` text DEFAULT NULL,
  `receipt_color` varchar(20) DEFAULT '#dc2626',
  `signature_data` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Classes Table
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `created_at?` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `institute_id` (`institute_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Students Table
CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `roll_no` varchar(50) DEFAULT NULL,
  `parent_name` varchar(255) DEFAULT NULL,
  `session` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `admission_date?` date DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `ledger_token` varchar(100) DEFAULT NULL,
  `created_at?` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ledger_token` (`ledger_token`),
  KEY `institute_id` (`institute_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Fee Categories Table
CREATE TABLE IF NOT EXISTS `fee_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `default_amount` decimal(10,2) DEFAULT 0.00,
  `created_at?` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `institute_id` (`institute_id`),
  CONSTRAINT `fee_categories_ibfk_1` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Fees (Transactions) Table
CREATE TABLE IF NOT EXISTS `fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `fee_category_id` int(11) DEFAULT NULL,
  `custom_fee_name` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `receipt_no` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `fee_details` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_no` (`receipt_no`),
  KEY `institute_id` (`institute_id`),
  KEY `student_id` (`student_id`),
  KEY `fee_category_id` (`fee_category_id`),
  CONSTRAINT `fees_ibfk_1` FOREIGN KEY (`institute_id`) REFERENCES `institutes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fees_ibfk_3` FOREIGN KEY (`fee_category_id`) REFERENCES `fee_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
