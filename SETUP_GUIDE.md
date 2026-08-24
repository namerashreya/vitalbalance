# 🚀 Quick Setup Guide - VitalBalance Database

## ⚠️ Error Fix: Table 'medications' doesn't exist

You're seeing this error because the required database tables haven't been created yet.

## ✅ Solution: Run the Setup Script

### Option 1: Run Complete Setup (Recommended)

1. Open **phpMyAdmin** (http://localhost/phpmyadmin)
2. Select your database: `vitalbalance_db`
3. Click on the **SQL** tab
4. Copy and paste the contents of `SETUP_DATABASE.sql`
5. Click **Go**

This will create:
- ✅ `medications` table (for Wellness Tracker)
- ✅ `user_remedies` table (for Dashboard Natural Remedies)
- ✅ Update `health_records` table (if needed)

### Option 2: Import the SQL File

1. Open **phpMyAdmin**
2. Select your database: `vitalbalance_db`
3. Click **Import**
4. Choose file: `SETUP_DATABASE.sql`
5. Click **Go**

## 📋 What Gets Created

### 1. Medications Table
Stores wellness tracker data:
- Medication name, dosage, frequency
- Start/end dates
- Prescribed by (doctor name)
- Natural remedies field
- Notes
- Status (active/completed/stopped)

### 2. User Remedies Table
Stores personal natural remedies from dashboard:
- Remedy name
- Remedy notes/benefits
- Created date

### 3. Health Records Updates
Adds missing columns if needed:
- `value` column
- `unit` column
- `recorded_at` column

## 🎯 After Running the SQL

Once you run the SQL script:

1. ✅ Wellness Tracker will work
2. ✅ Dashboard Natural Remedies will work
3. ✅ No more table errors!

## 🔍 Verify Tables Were Created

Run this query in phpMyAdmin to check:

```sql
SHOW TABLES LIKE 'medications';
SHOW TABLES LIKE 'user_remedies';
```

You should see both tables listed.

## 📝 Quick SQL (Copy & Paste)

If you want to run it quickly, here's the complete SQL:

```sql
-- Create medications table
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

-- Create user_remedies table
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
```

---

**That's it! Run the SQL and your application will be ready to use! 🎉**
