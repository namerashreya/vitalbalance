-- Update health_records table to support the new health tracking system
-- This adds the necessary columns for the enhanced health records feature

-- First, check if the table needs updating
ALTER TABLE health_records 
MODIFY COLUMN record_type VARCHAR(50) NOT NULL DEFAULT 'vitals';

-- Add recorded_at column if it doesn't exist (for datetime tracking)
ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER date_recorded;

-- Add value column if it doesn't exist (simplified value storage)
ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS value VARCHAR(100) AFTER record_type;

-- Add unit column if it doesn't exist
ALTER TABLE health_records 
ADD COLUMN IF NOT EXISTS unit VARCHAR(50) AFTER value;

-- Update existing records to use the new format
-- Copy metric_value to value if value is empty
UPDATE health_records 
SET value = metric_value 
WHERE value IS NULL OR value = '';

-- Copy metric_unit to unit if unit is empty
UPDATE health_records 
SET unit = metric_unit 
WHERE unit IS NULL OR unit = '';

-- Copy date_recorded to recorded_at if recorded_at is empty
UPDATE health_records 
SET recorded_at = CONCAT(date_recorded, ' 00:00:00')
WHERE recorded_at IS NULL;

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_user_recorded ON health_records(user_id, recorded_at);
CREATE INDEX IF NOT EXISTS idx_record_type ON health_records(record_type);
