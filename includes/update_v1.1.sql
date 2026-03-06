-- FeeFlow Database Update Script
-- Version: 1.1 (Professional Receipt & Institutional Enhancements)
-- Description: Adds fields for recognition, affiliation, receipt prefix, and student session.

USE feeflow_db;

-- Update Institutes Table
ALTER TABLE `institutes` 
ADD COLUMN `recognition_text` varchar(255) DEFAULT NULL AFTER `address`,
ADD COLUMN `affiliation_text` varchar(255) DEFAULT NULL AFTER `recognition_text`,
ADD COLUMN `receipt_prefix` varchar(50) DEFAULT NULL AFTER `affiliation_text`;

-- Update Students Table
ALTER TABLE `students` 
ADD COLUMN `session` varchar(50) DEFAULT NULL AFTER `parent_name`;

-- Optional: Initial data for existing institutes to prevent empty fields
-- UPDATE `institutes` SET `receipt_prefix` = CONCAT(LPAD(id, 3, '0'), '-');
