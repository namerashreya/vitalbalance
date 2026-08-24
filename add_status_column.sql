-- Add status column to user_diseases table
-- Run this in phpMyAdmin SQL tab if you get "Unknown column 'status'" error

-- For MySQL 5.7+ (supports IF NOT EXISTS)
-- ALTER TABLE user_diseases 
-- ADD COLUMN IF NOT EXISTS status ENUM('active', 'managed', 'resolved') DEFAULT 'active' 
-- AFTER disease_id;

-- For older MySQL versions or if above doesn't work, use this:
-- First check if column exists, if not, run the ALTER statement below

ALTER TABLE user_diseases 
ADD COLUMN status ENUM('active', 'managed', 'resolved') DEFAULT 'active' 
AFTER disease_id;

-- Note: If you get "Duplicate column name" error, the column already exists
-- and you can ignore this error.
