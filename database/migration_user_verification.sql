-- User Verification Migration
-- Run this in phpMyAdmin on the `naos` database

-- Step 1: Add verification columns to users table
ALTER TABLE `users`
  ADD COLUMN `national_id_path` varchar(255) DEFAULT NULL AFTER `is_profile_complete`,
  ADD COLUMN `id_verification_status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved' AFTER `national_id_path`,
  ADD COLUMN `id_verification_notes` text DEFAULT NULL AFTER `id_verification_status`;

-- Step 2: All existing users are already approved (default set above handles new inserts too)
-- New registrations will explicitly set 'pending' in PHP code.
