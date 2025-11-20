-- Migration script to update existing database
-- Run this AFTER the initial install-hostinger.sql if database already exists

-- Add new columns to users table
ALTER TABLE users 
  ADD COLUMN IF NOT EXISTS city VARCHAR(50) DEFAULT NULL AFTER email,
  ADD COLUMN IF NOT EXISTS zip_code VARCHAR(10) DEFAULT NULL AFTER city,
  ADD COLUMN IF NOT EXISTS approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved' AFTER is_verified;

-- Add index for approval_status
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_approval (approval_status);

-- Migrate existing is_verified data to approval_status
UPDATE users 
SET approval_status = CASE 
  WHEN is_verified = 1 THEN 'approved' 
  WHEN is_verified = 0 AND role = 'student' THEN 'pending'
  ELSE 'approved'
END
WHERE approval_status IS NULL OR approval_status = 'approved';

-- Update existing teacher users with city/zip from teacher_profiles
UPDATE users u
INNER JOIN teacher_profiles tp ON u.id = tp.user_id
SET u.city = tp.city, u.zip_code = tp.zip_code
WHERE u.role = 'student' AND (u.city IS NULL OR u.zip_code IS NULL);

SELECT 'Migration completed successfully!' as Status;
