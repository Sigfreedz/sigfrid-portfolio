-- database/portfolio.sql
-- Complete schema for Sigfrid's portfolio database

-- Database: sigfrid_portfolio
-- XAMPP MariaDB (localhost, root, no password)

-- =====================================================
-- SKILLS / TECH STACK TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(80) DEFAULT NULL,
  `icon` varchar(100) DEFAULT 'placeholder.svg',
  `level` int(11) NOT NULL DEFAULT 1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALTER statements if table already exists without featured/icon columns:
ALTER TABLE `skills` ADD COLUMN IF NOT EXISTS `featured` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `skills` ADD COLUMN IF NOT EXISTS `icon` varchar(100) DEFAULT 'placeholder.svg';
ALTER TABLE `skills` ADD COLUMN IF NOT EXISTS `category` varchar(80) DEFAULT NULL;
ALTER TABLE `skills` ADD COLUMN IF NOT EXISTS `sort_order` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `skills` ADD COLUMN IF NOT EXISTS `description` varchar(255) DEFAULT NULL;

-- =====================================================
-- DATA: Tech Stack items (Featured ~12 for main page, rest for full stack page)
-- =====================================================
INSERT INTO `skills` (`name`, `category`, `icon`, `level`, `featured`, `sort_order`, `description`) VALUES
-- Frontend
('HTML5', 'FRONTEND', 'html5.svg', 4, 1, 1, 'Semantic markup and structure'),
('CSS3', 'FRONTEND', 'css3.svg', 4, 1, 2, 'Styling, flexbox, grid, and Nord theme'),
('JavaScript', 'FRONTEND', 'javascript.svg', 3, 1, 3, 'Client-side interactivity and async fetch'),

-- Backend
('PHP', 'BACKEND', 'php.svg', 4, 1, 4, 'Server-side logic and JSON endpoints'),
('Java', 'BACKEND', 'java.svg', 3, 1, 5, 'Object-oriented programming'),
('C# / .NET', 'BACKEND', 'dotnet.svg', 3, 1, 6, 'ASP.NET Core & desktop apps'),
('Python', 'BACKEND', 'python.svg', 3, 1, 7, 'Scripting and automation'),
('Entity Framework', 'BACKEND', 'entityframework.svg', 2, 0, 8, 'ORM for .NET databases'),

-- Databases
('MySQL', 'DATABASES', 'mysql.svg', 4, 1, 9, 'Relational database management'),
('SQL', 'DATABASES', 'sql.svg', 4, 0, 10, 'Queries, joins, and database design'),

-- Networking
('Cisco Packet Tracer', 'NETWORKING', 'cisco.svg', 3, 1, 11, 'Network simulation and topologies'),

-- Security
('TryHackMe', 'SECURITY', 'tryhackme.svg', 3, 1, 12, 'Hands-on cybersecurity labs'),
('picoCTF', 'SECURITY', 'picoctf.svg', 3, 0, 13, 'Capture the flag challenges'),

-- Tools
('Git', 'TOOLS', 'git.svg', 3, 1, 14, 'Version control'),
('GitHub', 'TOOLS', 'github.svg', 4, 1, 15, 'Code hosting and collaboration'),
('VS Code', 'TOOLS', 'vscode.svg', 5, 1, 16, 'Primary code editor'),
('XAMPP', 'TOOLS', 'xampp.svg', 4, 0, 17, 'Local development server'),
('Unity', 'TOOLS', 'unity.svg', 2, 0, 18, 'Game development engine')
ON DUPLICATE KEY UPDATE 
  `category` = VALUES(`category`),
  `icon` = VALUES(`icon`),
  `featured` = VALUES(`featured`),
  `sort_order` = VALUES(`sort_order`),
  `description` = VALUES(`description`);

-- =====================================================
-- PROJECTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(140) NOT NULL,
  `description` text DEFAULT NULL,
  `tech_tags` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `demo_url` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `projects` ADD COLUMN IF NOT EXISTS `featured` tinyint(1) NOT NULL DEFAULT 0;

INSERT INTO `projects`
  (`title`, `description`, `tech_tags`, `github_url`, `demo_url`, `cover_image`, `featured`, `sort_order`)
VALUES
  ('DeviceLabs', 'A web-based device selection tool that helps PLV BSIT students choose the right device for their needs.', 'React,TypeScript,Supabase', 'https://github.com/Sigfreedz/DeviceSelect', NULL, 'images/featured_projects/devicelabs-main.png', 1, 1),
  ('DiagnoSys', 'A diagnostic clinic database and Flask CRUD interface for managing patient and laboratory information.', 'Python,Flask,MySQL,PyMySQL,Jinja,Bootstrap', 'https://github.com/Sigfreedz/PatientInfoSystem-Advanced-IM', NULL, 'images/featured_projects/patientinfosystem.png', 1, 2),
  ('iDoctor', 'A responsive digital hub for iDoctor Eye Clinic with services, products, specialists, and appointment booking.', 'HTML,CSS,JavaScript,Bootstrap,EmailJS', 'https://github.com/Sigfreedz/Eye-Clinic-Primary-Digital-Hub', 'https://eye-clinic-primary-digital-hub.vercel.app', 'images/featured_projects/idoctor-main.png', 1, 3);

-- =====================================================
-- NEW TABLE: skill_radar
-- =====================================================
CREATE TABLE IF NOT EXISTS `skill_radar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL COMMENT 'Area of interest/focus',
  `value` int(11) NOT NULL DEFAULT 50 COMMENT 'Illustrative value 0-100 (not proficiency)',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Display order on radar chart',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Areas of interest for About Me radar visualization';

INSERT INTO `skill_radar` (`label`, `value`, `sort_order`) VALUES
('Web Development', 75, 1),
('Networking', 70, 2),
('Offensive Security', 65, 3),
('Databases', 60, 4),
('Programming', 80, 5),
('Forensics / OSINT', 55, 6)
ON DUPLICATE KEY UPDATE 
  `value` = VALUES(`value`),
  `sort_order` = VALUES(`sort_order`);
