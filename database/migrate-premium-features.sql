-- Add premium fields to users table
ALTER TABLE users 
  ADD COLUMN IF NOT EXISTS is_premium BOOLEAN DEFAULT 0 COMMENT 'Premium üyelik durumu' AFTER approval_status,
  ADD COLUMN IF NOT EXISTS premium_expires_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Premium üyelik bitiş tarihi' AFTER is_premium;

-- Add index for premium
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_premium (is_premium);

-- Add cv_url to teacher_profiles
ALTER TABLE teacher_profiles
  ADD COLUMN IF NOT EXISTS cv_url VARCHAR(255) DEFAULT NULL COMMENT 'CV dosyası (premium)' AFTER video_intro_url;

SELECT 'Premium features migration completed!' as Status;
