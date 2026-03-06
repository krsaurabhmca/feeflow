-- FeeFlow Database Update Script
-- Version: 1.2 (App Connectivity & Registration Enhancements)
-- Description: Adds missing columns for custom fee names, payment dates, and registration.

USE feeflow_db;

-- 1. Ensure students can have a session (already in 1.1 but good to be sure)
-- ALTER TABLE `students` ADD COLUMN `session` varchar(50) DEFAULT NULL;

-- 2. Update Fees table to handle custom entries from app
ALTER TABLE `fees` 
ADD COLUMN `fee_category_id` int(11) DEFAULT NULL AFTER `student_id`,
ADD COLUMN `custom_fee_name` varchar(255) DEFAULT NULL AFTER `fee_category_id`,
ADD COLUMN `payment_date` date DEFAULT NULL AFTER `amount`,
ADD COLUMN `payment_method` varchar(50) DEFAULT 'Cash' AFTER `payment_date`;

-- 3. Ensure fee_categories table exists
CREATE TABLE IF NOT EXISTS `fee_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Ensure classes table exists
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
