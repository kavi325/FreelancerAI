

-- Dumping database structure for ai_freelance_platform
CREATE DATABASE IF NOT EXISTS `ai_freelance_platform` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ai_freelance_platform`;

-- Dumping structure for table ai_freelance_platform.administration
CREATE TABLE IF NOT EXISTS `administration` (
  `user_id` int NOT NULL,
  `admin_level` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_date` date NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_administration_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_administration_level` CHECK ((`admin_level` in (_utf8mb4'super_admin',_utf8mb4'moderator',_utf8mb4'support')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.administration: ~1 rows (approximately)
INSERT INTO `administration` (`user_id`, `admin_level`, `department`, `assigned_date`) VALUES
	(3, 'super_admin', 'System Administration', '2026-11-28');

-- Dumping structure for table ai_freelance_platform.ai_service
CREATE TABLE IF NOT EXISTS `ai_service` (
  `service_id` int NOT NULL AUTO_INCREMENT,
  `service_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`service_id`),
  UNIQUE KEY `uq_ai_service_name` (`service_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.ai_service: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.ai_skill
CREATE TABLE IF NOT EXISTS `ai_skill` (
  `skill_id` int NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skill_description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`skill_id`),
  UNIQUE KEY `uq_ai_skill_name` (`skill_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.ai_skill: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.assignment
CREATE TABLE IF NOT EXISTS `assignment` (
  `assignment_id` int NOT NULL AUTO_INCREMENT,
  `proposal_id` int NOT NULL,
  `agreed_amount` decimal(12,2) NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assignment_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `start_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `uq_assignment_proposal` (`proposal_id`),
  CONSTRAINT `fk_assignment_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`proposal_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_agreed_amount` CHECK ((`agreed_amount` >= 0)),
  CONSTRAINT `chk_assignment_dates` CHECK (((`completion_date` is null) or (`start_date` is null) or (`completion_date` >= `start_date`))),
  CONSTRAINT `chk_assignment_status` CHECK ((`assignment_status` in (_utf8mb4'in_progress',_utf8mb4'completed',_utf8mb4'cancelled')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.assignment: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.client
CREATE TABLE IF NOT EXISTS `client` (
  `user_id` int NOT NULL,
  `organization_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_description` text COLLATE utf8mb4_unicode_ci,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_client_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_client_organization_name` CHECK (((`client_type` = _utf8mb4'individual') or (`organization_name` is not null))),
  CONSTRAINT `chk_client_type` CHECK ((`client_type` in (_utf8mb4'individual',_utf8mb4'organization')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.client: ~1 rows (approximately)
INSERT INTO `client` (`user_id`, `organization_name`, `client_type`, `organization_type`, `business_description`, `country`) VALUES
	(4, NULL, 'individual', NULL, NULL, 'Sri Lanka');

-- Dumping structure for table ai_freelance_platform.freelancer
CREATE TABLE IF NOT EXISTS `freelancer` (
  `user_id` int NOT NULL,
  `professional_title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biography` text COLLATE utf8mb4_unicode_ci,
  `experience_years` decimal(4,1) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `availability_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_freelancer_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_freelancer_availability` CHECK ((`availability_status` in (_utf8mb4'available',_utf8mb4'busy',_utf8mb4'unavailable'))),
  CONSTRAINT `chk_freelancer_experience` CHECK ((`experience_years` >= 0)),
  CONSTRAINT `chk_freelancer_rate` CHECK ((`hourly_rate` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.freelancer: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.freelancer_skill
CREATE TABLE IF NOT EXISTS `freelancer_skill` (
  `user_id` int NOT NULL,
  `skill_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`skill_id`),
  KEY `fk_freelancer_skill_skill` (`skill_id`),
  CONSTRAINT `fk_freelancer_skill_freelancer` FOREIGN KEY (`user_id`) REFERENCES `freelancer` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_freelancer_skill_skill` FOREIGN KEY (`skill_id`) REFERENCES `ai_skill` (`skill_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.freelancer_skill: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.management
CREATE TABLE IF NOT EXISTS `management` (
  `user_id` int NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_management_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.management: ~1 rows (approximately)
INSERT INTO `management` (`user_id`, `department`, `designation`) VALUES
	(2, 'Platform Operations', 'Operations Manager');

-- Dumping structure for table ai_freelance_platform.payment
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `assignment_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `transaction_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `uq_payment_transaction_reference` (`transaction_reference`),
  KEY `fk_payment_assignment` (`assignment_id`),
  CONSTRAINT `fk_payment_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`assignment_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_payment_amount` CHECK ((`amount` > 0)),
  CONSTRAINT `chk_payment_method` CHECK ((`payment_method` in (_utf8mb4'bank_transfer',_utf8mb4'credit_card',_utf8mb4'paypal'))),
  CONSTRAINT `chk_payment_status` CHECK ((`payment_status` in (_utf8mb4'pending',_utf8mb4'completed',_utf8mb4'failed',_utf8mb4'refunded')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.payment: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.project
CREATE TABLE IF NOT EXISTS `project` (
  `project_id` int NOT NULL AUTO_INCREMENT,
  `project_title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget_min` decimal(12,2) NOT NULL,
  `budget_max` decimal(12,2) NOT NULL,
  `project_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `deadline` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`project_id`),
  CONSTRAINT `chk_project_budget_max` CHECK ((`budget_max` >= `budget_min`)),
  CONSTRAINT `chk_project_budget_min` CHECK ((`budget_min` >= 0)),
  CONSTRAINT `chk_project_status` CHECK ((`project_status` in (_utf8mb4'open',_utf8mb4'in_progress',_utf8mb4'completed',_utf8mb4'cancelled')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.project: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.project_requirement
CREATE TABLE IF NOT EXISTS `project_requirement` (
  `project_id` int NOT NULL,
  `skill_id` int NOT NULL,
  `requirement_description` text COLLATE utf8mb4_unicode_ci,
  `required_proficiency` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`project_id`,`skill_id`),
  KEY `fk_project_requirement_skill` (`skill_id`),
  CONSTRAINT `fk_project_requirement_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_project_requirement_skill` FOREIGN KEY (`skill_id`) REFERENCES `ai_skill` (`skill_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_required_proficiency` CHECK ((`required_proficiency` in (_utf8mb4'beginner',_utf8mb4'intermediate',_utf8mb4'advanced',_utf8mb4'expert')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.project_requirement: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.proposal
CREATE TABLE IF NOT EXISTS `proposal` (
  `proposal_id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `user_id` int NOT NULL,
  `proposed_amount` decimal(12,2) NOT NULL,
  `estimated_duration` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_letter` text COLLATE utf8mb4_unicode_ci,
  `proposal_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `submitted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`proposal_id`),
  UNIQUE KEY `uq_proposal_project_freelancer` (`project_id`,`user_id`),
  KEY `fk_proposal_freelancer` (`user_id`),
  CONSTRAINT `fk_proposal_freelancer` FOREIGN KEY (`user_id`) REFERENCES `freelancer` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_proposal_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_proposal_status` CHECK ((`proposal_status` in (_utf8mb4'pending',_utf8mb4'accepted',_utf8mb4'rejected',_utf8mb4'withdrawn'))),
  CONSTRAINT `chk_proposed_amount` CHECK ((`proposed_amount` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.proposal: ~0 rows (approximately)

-- Dumping structure for table ai_freelance_platform.user
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_user_email` (`email`),
  CONSTRAINT `chk_user_account_status` CHECK ((`account_status` in (_utf8mb4'active',_utf8mb4'suspended',_utf8mb4'closed')))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.user: ~3 rows (approximately)
INSERT INTO `user` (`user_id`, `full_name`, `email`, `password_hash`, `account_status`, `created_at`) VALUES
	(2, 'Thilakarathne', 'management@gmail.com', '$2y$12$gxZFDvljmoYXbGFxqcud1uricM7qv5xnFmASuPDULc6W3wlh17yQ6', 'active', '2026-11-28 13:45:33'),
	(3, 'Platform Super Admin', 'admin@gmail.com', '$2y$12$GmWKf.OLpGSrcEk8vz3SWOv47wjlTqw1ca2lq8r99e4QVi6KbCw1W', 'active', '2026-11-28 13:45:33'),
	(4, 'Dushan Loku Liyanage', 'dmlliyanage@gmail.com', '$2y$12$As9v4i9wYkc7eF8bMcZiCebuciW3Psjhk0h6GJXlpXCS4AWGaSqtm', 'active', '2026-11-28 14:27:54');

-- Dumping structure for table ai_freelance_platform.user_phone
CREATE TABLE IF NOT EXISTS `user_phone` (
  `user_id` int NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`phone_number`),
  CONSTRAINT `fk_user_phone_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ai_freelance_platform.user_phone: ~2 rows (approximately)
INSERT INTO `user_phone` (`user_id`, `phone_number`) VALUES
	(4, '');

-- Dumping structure for table ai_freelance_platform.user_project
CREATE TABLE IF NOT EXISTS `user_project` (
  `project_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`project_id`,`user_id`),
  UNIQUE KEY `uq_user_project_project` (`project_id`),
  KEY `fk_user_project_client` (`user_id`),
  CONSTRAINT `fk_user_project_client` FOREIGN KEY (`user_id`) REFERENCES `client` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_user_project_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


