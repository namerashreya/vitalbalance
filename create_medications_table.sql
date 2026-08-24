-- Create medications table for the Wellness Tracker
-- This table stores user's medications and wellness items

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

-- Success message
SELECT 'Medications table created successfully!' AS Status;
