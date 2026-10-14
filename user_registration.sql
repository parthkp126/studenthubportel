CREATE DATABASE IF NOT EXISTS studentportelhub
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE studentportelhub;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    enrollment VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(254) NOT NULL UNIQUE,
    mobile VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(100) NOT NULL,
    year VARCHAR(30) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    role ENUM('Admin', 'Student') NOT NULL DEFAULT 'Student',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @role_column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'role'
);

SET @role_migration = IF(
    @role_column_exists = 0,
    "ALTER TABLE users ADD COLUMN role ENUM('Admin','Student') NOT NULL DEFAULT 'Student'",
    'SELECT 1'
);

PREPARE role_migration_stmt FROM @role_migration;
EXECUTE role_migration_stmt;
DEALLOCATE PREPARE role_migration_stmt;