-- Complete Database Setup for VitalBalance
-- Run this script to create all necessary tables for the application

-- ============================================
-- 1. CREATE MEDICATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS medications (
    medication_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    medication_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    prescribed_by VARCHAR(255) DEFAULT NULL,
    natural_remedies TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('active', 'completed', 'stopped') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_start_date (start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. CREATE USER_REMEDIES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS user_remedies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    remedy_name VARCHAR(255) NOT NULL,
    remedy_notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. UPDATE HEALTH_RECORDS TABLE (if needed)
-- ============================================
-- Add columns if they don't exist
ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS value VARCHAR(100) AFTER record_type;

ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS unit VARCHAR(50) AFTER value;

ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS recorded_at DATETIME DEFAULT NULL AFTER date_recorded;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_user_recorded ON health_records(user_id, recorded_at);
CREATE INDEX IF NOT EXISTS idx_record_type ON health_records(record_type);

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT '✅ All tables created successfully! Your VitalBalance database is ready.' AS Status;
