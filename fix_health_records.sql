-- VitalHealth - Health Records Table Update Script
-- This script safely updates the health_records table to support the new features

-- Step 1: Add the 'value' column if it doesn't exist
ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS `value` VARCHAR(100) AFTER record_type;

-- Step 2: Add the 'unit' column if it doesn't exist
ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS `unit` VARCHAR(50) AFTER `value`;

-- Step 3: Add the 'recorded_at' column if it doesn't exist
ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS `recorded_at` DATETIME DEFAULT NULL AFTER date_recorded;

-- Step 4: Copy existing data to new columns (if they have data)
UPDATE health_records 
SET `value` = metric_value 
WHERE (`value` IS NULL OR `value` = '') AND metric_value IS NOT NULL;

UPDATE health_records 
SET `unit` = metric_unit 
WHERE (`unit` IS NULL OR `unit` = '') AND metric_unit IS NOT NULL;

UPDATE health_records 
SET `recorded_at` = CONCAT(date_recorded, ' 00:00:00')
WHERE `recorded_at` IS NULL AND date_recorded IS NOT NULL;

-- Step 5: Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_user_date ON health_records(user_id, date_recorded);
CREATE INDEX IF NOT EXISTS idx_record_type ON health_records(record_type);

-- Success message
SELECT 'Health records table updated successfully!' AS Status;
