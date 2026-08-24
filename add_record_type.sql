-- Add the missing record_type column
ALTER TABLE health_records 
ADD COLUMN `record_type` VARCHAR(50) NOT NULL DEFAULT 'vitals' AFTER user_id;

SELECT 'record_type column added successfully!' AS Result;
