-- Add all required columns to users table
-- Run this SQL in your phpMyAdmin or MySQL console

-- Note: If your MySQL version doesn't support IF NOT EXISTS in ALTER TABLE,
-- run these separately and ignore errors if columns already exist:

-- Add name column (or fullname if name doesn't work)
ALTER TABLE users ADD COLUMN name VARCHAR(100) NOT NULL;
-- If above fails, try: ALTER TABLE users ADD COLUMN fullname VARCHAR(100) NOT NULL;

-- Add age column
ALTER TABLE users ADD COLUMN age INT NOT NULL;

-- Add gender column
ALTER TABLE users ADD COLUMN gender VARCHAR(10) NOT NULL;

-- Add role column (if not exists)
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user';

-- Add created_at timestamp
ALTER TABLE users ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP;

-- Add password reset columns
ALTER TABLE users ADD COLUMN reset_token VARCHAR(64) NULL;
ALTER TABLE users ADD COLUMN reset_expires DATETIME NULL;

-- Alternative: If you need to create the entire table structure from scratch, use this:
/*
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    reset_token VARCHAR(64) NULL,
    reset_expires DATETIME NULL
);
*/

