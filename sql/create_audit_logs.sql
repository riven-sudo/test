-- Audit logs table
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_type` VARCHAR(32) DEFAULT NULL,
  `user_id` INT DEFAULT NULL,
  `username` VARCHAR(255) DEFAULT NULL,
  `action` VARCHAR(128) NOT NULL,
  `entity` VARCHAR(128) DEFAULT NULL,
  `entity_id` VARCHAR(128) DEFAULT NULL,
  `old_value` TEXT DEFAULT NULL,
  `new_value` TEXT DEFAULT NULL,
  `meta` TEXT DEFAULT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `url` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indexes for common queries
CREATE INDEX IF NOT EXISTS idx_audit_created_at ON `audit_logs` (`created_at`);
CREATE INDEX IF NOT EXISTS idx_audit_user_id ON `audit_logs` (`user_id`);
CREATE INDEX IF NOT EXISTS idx_audit_action ON `audit_logs` (`action`);
