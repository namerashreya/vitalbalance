-- Simple fix - Just add the columns we need
-- Run this in phpMyAdmin SQL tab

ALTER TABLE health_records 
ADD COLUMN `value` VARCHAR(100);

ALTER TABLE health_records 
ADD COLUMN `unit` VARCHAR(50);

ALTER TABLE health_records 
ADD COLUMN `recorded_at` DATETIME;

SELECT 'Columns added successfully!' AS Result;
