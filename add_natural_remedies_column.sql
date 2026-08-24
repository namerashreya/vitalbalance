-- Add natural_remedies column to medications table
-- This allows users to track natural remedies alongside their medications

ALTER TABLE medications 
ADD COLUMN IF NOT EXISTS natural_remedies TEXT AFTER prescribed_by;

-- Success message
SELECT 'Natural remedies column added successfully to medications table!' AS Status;
