-- FeeFlow Database Update Script
-- Version: 1.3 (Complete App & Student Management)
-- Description: Consolidates all missing columns for students, fees, and categories.

USE feeflow_db;

-- 1. Ensure students have a session column
ALTER TABLE `students` ADD COLUMN IF NOT EXISTS `session` varchar(50) DEFAULT NULL AFTER `parent_name`;

-- 2. Update Fees table for advanced collection
ALTER TABLE `fees` 
ADD COLUMN IF NOT EXISTS `fee_category_id` int(11) DEFAULT NULL AFTER `student_id`,
ADD COLUMN IF NOT EXISTS `custom_fee_name` varchar(255) DEFAULT NULL AFTER `fee_category_id`,
ADD COLUMN IF NOT EXISTS `payment_date` date DEFAULT NULL AFTER `amount`,
ADD COLUMN IF NOT EXISTS `payment_method` varchar(50) DEFAULT 'Cash' AFTER `payment_date`;

-- 3. Core Tables for App Features
CREATE TABLE IF NOT EXISTS `fee_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institute_id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Add missing recognition and affiliation to institutes if missing
ALTER TABLE `institutes` 
ADD COLUMN IF NOT EXISTS `recognition_text` varchar(255) DEFAULT NULL AFTER `address`,
ADD COLUMN IF NOT EXISTS `affiliation_text` varchar(255) DEFAULT NULL AFTER `recognition_text`,
ADD COLUMN IF NOT EXISTS `receipt_prefix` varchar(50) DEFAULT NULL AFTER `affiliation_text`;
