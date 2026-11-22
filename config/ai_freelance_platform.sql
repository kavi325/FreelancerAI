CREATE DATABASE IF NOT EXISTS ai_freelance_platform
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ai_freelance_platform;

CREATE TABLE IF NOT EXISTS `USER` (
    user_id INT NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    account_status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    UNIQUE KEY uq_user_email (email),
    CONSTRAINT chk_user_account_status CHECK (account_status IN ('active', 'suspended', 'closed'))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS USER_PHONE (
    user_id INT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    PRIMARY KEY (user_id, phone_number),
    CONSTRAINT fk_user_phone_user FOREIGN KEY (user_id) REFERENCES `USER` (user_id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS FREELANCER (
    user_id INT NOT NULL,
    professional_title VARCHAR(150) NOT NULL,
    biography TEXT NULL,
    experience_years DECIMAL(4,1) NOT NULL,
    hourly_rate DECIMAL(10,2) NOT NULL,
    country VARCHAR(100) NOT NULL,
    availability_status VARCHAR(30) NOT NULL,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_freelancer_user FOREIGN KEY (user_id) REFERENCES `USER` (user_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_freelancer_experience CHECK (experience_years >= 0),
    CONSTRAINT chk_freelancer_rate CHECK (hourly_rate >= 0),
    CONSTRAINT chk_freelancer_availability CHECK (availability_status IN ('available', 'busy', 'unavailable'))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS MANAGEMENT (
    user_id INT NOT NULL,
    department VARCHAR(100) NOT NULL,
    designation VARCHAR(100) NOT NULL,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_management_user FOREIGN KEY (user_id) REFERENCES `USER` (user_id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ADMINISTRATION (
    user_id INT NOT NULL,
    admin_level VARCHAR(50) NOT NULL,
    department VARCHAR(100) NOT NULL,
    assigned_date DATE NOT NULL,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_administration_user FOREIGN KEY (user_id) REFERENCES `USER` (user_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_administration_level CHECK (admin_level IN ('super_admin', 'moderator', 'support'))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS CLIENT (
    user_id INT NOT NULL,
    organization_name VARCHAR(150) NULL,
    client_type VARCHAR(30) NOT NULL,
    organization_type VARCHAR(100) NULL,
    business_description TEXT NULL,
    country VARCHAR(100) NOT NULL,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_client_user FOREIGN KEY (user_id) REFERENCES `USER` (user_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_client_type CHECK (client_type IN ('individual', 'organization')),
    CONSTRAINT chk_client_organization_name CHECK (client_type = 'individual' OR organization_name IS NOT NULL)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS AI_SKILL (
    skill_id INT NOT NULL AUTO_INCREMENT,
    skill_name VARCHAR(100) NOT NULL,
    skill_description TEXT NULL,
    PRIMARY KEY (skill_id),
    UNIQUE KEY uq_ai_skill_name (skill_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS AI_SERVICE (
    service_id INT NOT NULL AUTO_INCREMENT,
    service_name VARCHAR(150) NOT NULL,
    service_description TEXT NULL,
    PRIMARY KEY (service_id),
    UNIQUE KEY uq_ai_service_name (service_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS PROJECT (
    project_id INT NOT NULL AUTO_INCREMENT,
    project_title VARCHAR(200) NOT NULL,
    project_description TEXT NOT NULL,
    budget_min DECIMAL(12,2) NOT NULL,
    budget_max DECIMAL(12,2) NOT NULL,
    project_status VARCHAR(30) NOT NULL DEFAULT 'open',
    deadline DATE NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (project_id),
    CONSTRAINT chk_project_budget_min CHECK (budget_min >= 0),
    CONSTRAINT chk_project_budget_max CHECK (budget_max >= budget_min),
    CONSTRAINT chk_project_status CHECK (project_status IN ('open', 'in_progress', 'completed', 'cancelled'))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS USER_PROJECT (
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (project_id, user_id),
    UNIQUE KEY uq_user_project_project (project_id),
    CONSTRAINT fk_user_project_project FOREIGN KEY (project_id) REFERENCES PROJECT (project_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_user_project_client FOREIGN KEY (user_id) REFERENCES CLIENT (user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS FREELANCER_SKILL (
    user_id INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (user_id, skill_id),
    CONSTRAINT fk_freelancer_skill_freelancer FOREIGN KEY (user_id) REFERENCES FREELANCER (user_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_freelancer_skill_skill FOREIGN KEY (skill_id) REFERENCES AI_SKILL (skill_id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS PROJECT_REQUIREMENT (
    project_id INT NOT NULL,
    skill_id INT NOT NULL,
    requirement_description TEXT NULL,
    required_proficiency VARCHAR(50) NOT NULL,
    PRIMARY KEY (project_id, skill_id),
    CONSTRAINT fk_project_requirement_project FOREIGN KEY (project_id) REFERENCES PROJECT (project_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_project_requirement_skill FOREIGN KEY (skill_id) REFERENCES AI_SKILL (skill_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_required_proficiency CHECK (required_proficiency IN ('beginner', 'intermediate', 'advanced', 'expert'))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS PROPOSAL (
    proposal_id INT NOT NULL AUTO_INCREMENT,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    proposed_amount DECIMAL(12,2) NOT NULL,
    estimated_duration VARCHAR(100) NOT NULL,
    cover_letter TEXT NULL,
    proposal_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (proposal_id),
    UNIQUE KEY uq_proposal_project_freelancer (project_id, user_id),
    CONSTRAINT fk_proposal_project FOREIGN KEY (project_id) REFERENCES PROJECT (project_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_proposal_freelancer FOREIGN KEY (user_id) REFERENCES FREELANCER (user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_proposed_amount CHECK (proposed_amount >= 0),
    CONSTRAINT chk_proposal_status CHECK (proposal_status IN ('pending', 'accepted', 'rejected', 'withdrawn'))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ASSIGNMENT (
    assignment_id INT NOT NULL AUTO_INCREMENT,
    proposal_id INT NOT NULL,
    agreed_amount DECIMAL(12,2) NOT NULL,
    assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assignment_status VARCHAR(30) NOT NULL DEFAULT 'in_progress',
    start_date DATE NULL,
    completion_date DATE NULL,
    PRIMARY KEY (assignment_id),
    UNIQUE KEY uq_assignment_proposal (proposal_id),
    CONSTRAINT fk_assignment_proposal FOREIGN KEY (proposal_id) REFERENCES PROPOSAL (proposal_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_agreed_amount CHECK (agreed_amount >= 0),
    CONSTRAINT chk_assignment_status CHECK (assignment_status IN ('in_progress', 'completed', 'cancelled')),
    CONSTRAINT chk_assignment_dates CHECK (completion_date IS NULL OR start_date IS NULL OR completion_date >= start_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS PAYMENT (
    payment_id INT NOT NULL AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_date DATETIME NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    transaction_reference VARCHAR(100) NULL,
    PRIMARY KEY (payment_id),
    UNIQUE KEY uq_payment_transaction_reference (transaction_reference),
    CONSTRAINT fk_payment_assignment FOREIGN KEY (assignment_id) REFERENCES ASSIGNMENT (assignment_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_payment_amount CHECK (amount > 0),
    CONSTRAINT chk_payment_method CHECK (payment_method IN ('bank_transfer', 'credit_card', 'paypal')),
    CONSTRAINT chk_payment_status CHECK (payment_status IN ('pending', 'completed', 'failed', 'refunded'))
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS REVIEW (
    review_id INT NOT NULL AUTO_INCREMENT,
    assignment_id INT NOT NULL,
    reviewer_user_id INT NOT NULL,
    reviewed_user_id INT NOT NULL,
    rating TINYINT NOT NULL,
    review_comment TEXT NULL,
    review_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (review_id),
    CONSTRAINT fk_review_assignment FOREIGN KEY (assignment_id) REFERENCES ASSIGNMENT (assignment_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_review_reviewer FOREIGN KEY (reviewer_user_id) REFERENCES `USER` (user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_review_reviewed FOREIGN KEY (reviewed_user_id) REFERENCES `USER` (user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_review_rating CHECK (rating BETWEEN 1 AND 5),
    CONSTRAINT chk_review_different_users CHECK (reviewer_user_id <> reviewed_user_id)
) ENGINE=InnoDB;
