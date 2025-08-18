-- Main User Table
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Roles Table
CREATE TABLE `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_name` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permissions Table
CREATE TABLE `permissions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `permission_name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pivot Table for User-Role relationship (Many-to-Many)
CREATE TABLE `user_roles` (
  `user_id` INT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pivot Table for Role-Permission relationship (Many-to-Many)
CREATE TABLE `role_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site Settings Table (Key-Value Store)
CREATE TABLE `settings` (
  `setting_key` VARCHAR(50) NOT NULL PRIMARY KEY,
  `setting_value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Menus Table
CREATE TABLE `menus` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `link` VARCHAR(255) NOT NULL,
  `parent_id` INT UNSIGNED DEFAULT 0,
  `menu_type` ENUM('admin', 'dashboard', 'public') NOT NULL,
  `display_order` INT DEFAULT 0,
  `icon` VARCHAR(50) DEFAULT NULL -- e.g., for Font Awesome class
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pivot Table for Menu-Permission relationship
CREATE TABLE `menu_permissions` (
    `menu_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`menu_id`, `permission_id`),
    FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Plugins Table
CREATE TABLE `plugins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `plugin_folder` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `is_active` BOOLEAN NOT NULL DEFAULT 0,
  `version` VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Default Data
--

-- Default Roles
INSERT INTO `roles` (`role_name`, `description`) VALUES
('Admin', 'Administrator with full access'),
('User', 'Standard user with basic permissions');

-- Default Site Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'My Awesome Site'),
('site_logo_path', ''),
('footer_text', '© 2024 My Awesome Site. All rights reserved.'),
('default_font', 'cursive'),
('footer_links', '[]'), -- Store footer links as a JSON array
('active_theme', 'default');
