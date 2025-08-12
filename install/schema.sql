-- Base table structure for the application

-- Roles Table: Defines user roles (e.g., Admin, Member)
CREATE TABLE `%%PREFIX%%roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `permissions` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Roles
INSERT INTO `%%PREFIX%%roles` (`id`, `name`, `permissions`) VALUES
(1, 'Founder', '{"all": true}'),
(2, 'Member', '{}');

-- Users Table: Stores user information
CREATE TABLE `%%PREFIX%%users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '2',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Languages Table: For multi-language support
CREATE TABLE `%%PREFIX%%languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Languages
INSERT INTO `%%PREFIX%%languages` (`name`, `code`, `is_default`) VALUES
('English', 'en', 1),
('Română', 'ro', 0);

-- Links/Navigation Table
CREATE TABLE `%%PREFIX%%links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Link Translations Table
CREATE TABLE `%%PREFIX%%link_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_id` int(11) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_lang` (`link_id`,`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Templates Table
CREATE TABLE `%%PREFIX%%templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `folder_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `folder_name` (`folder_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Links
INSERT INTO `%%PREFIX%%links` (`id`, `parent_id`, `url`, `display_order`) VALUES
(1, 0, '/', 1),
(2, 0, '/users/login', 10),
(3, 0, '/users/register', 11),
(4, 0, '/dashboard', 5);

INSERT INTO `%%PREFIX%%link_translations` (`link_id`, `language_code`, `title`) VALUES
(1, 'en', 'Home'),
(1, 'ro', 'Acasă'),
(2, 'en', 'Login'),
(2, 'ro', 'Autentificare'),
(3, 'en', 'Register'),
(3, 'ro', 'Înregistrare'),
(4, 'en', 'Dashboard'),
(4, 'ro', 'Panou de control');

-- Default Template
INSERT INTO `%%PREFIX%%templates` (`name`, `folder_name`, `is_active`) VALUES
('Default Theme', 'default', 1);

-- Plugins Table
CREATE TABLE `%%PREFIX%%plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `folder_name` (`folder_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
