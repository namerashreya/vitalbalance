-- Create user_remedies table for storing user's personal natural remedies
-- This allows users to track their own natural remedies on the dashboard

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

-- Success message
SELECT 'User remedies table created successfully!' AS Status;
