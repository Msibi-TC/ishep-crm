-- ISHEP plain-PHP MVP manual installation script
-- Target: the existing, operator-created ishep_crm database.
-- This script intentionally does not CREATE, DROP, TRUNCATE, or recreate a database.
-- Review it before importing manually through phpMyAdmin.

USE `ishep_crm`;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `membership_type_id` bigint unsigned DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `account_status` varchar(255) NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_account_status_index` (`account_status`), KEY `users_membership_type_index` (`membership_type_id`),
  CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL, `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL, PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL, `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL, PRIMARY KEY (`id`),
  UNIQUE KEY `roles_code_unique` (`code`), KEY `roles_is_system_index` (`is_system`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL, `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL, `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `permissions_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` bigint unsigned NOT NULL, `permission_id` bigint unsigned NOT NULL,
  UNIQUE KEY `role_permissions_unique` (`role_id`,`permission_id`),
  CONSTRAINT `role_permissions_role_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_permission_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` bigint unsigned NOT NULL, `role_id` bigint unsigned NOT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL, `assigned_at` timestamp NOT NULL,
  UNIQUE KEY `user_roles_unique` (`user_id`,`role_id`),
  CONSTRAINT `user_roles_user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_role_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_assigner_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `provinces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL, `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL, `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `provinces_code_unique` (`code`),
  UNIQUE KEY `provinces_name_unique` (`name`), KEY `provinces_active_index` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `professions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1, `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL, PRIMARY KEY (`id`),
  UNIQUE KEY `professions_name_unique` (`name`), KEY `professions_active_index` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `membership_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL, `description` text DEFAULT NULL,
  `fee` decimal(12,2) NOT NULL DEFAULT 0.00, `billing_period` varchar(255) NOT NULL,
  `is_student` tinyint(1) NOT NULL DEFAULT 0, `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL, `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `membership_types_code_unique` (`code`),
  KEY `membership_types_is_student_index` (`is_student`), KEY `membership_types_active_index` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `actor_user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL, `entity_type` varchar(255) NOT NULL,
  `entity_id` bigint unsigned DEFAULT NULL, `before_values` json DEFAULT NULL,
  `after_values` json DEFAULT NULL, `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`), KEY `audit_logs_action_index` (`action`),
  KEY `audit_logs_entity_type_index` (`entity_type`), KEY `audit_logs_entity_index` (`entity_type`,`entity_id`),
  CONSTRAINT `audit_logs_actor_foreign` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `member_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `user_id` bigint unsigned NOT NULL,
  `telephone` varchar(30) DEFAULT NULL, `province_id` bigint unsigned DEFAULT NULL,
  `profession_id` bigint unsigned DEFAULT NULL, `organisation` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL, `biography` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL, `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `member_profiles_user_unique` (`user_id`),
  KEY `member_profiles_province_index` (`province_id`), KEY `member_profiles_profession_index` (`profession_id`),
  CONSTRAINT `member_profiles_user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_profiles_province_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE SET NULL,
  CONSTRAINT `member_profiles_profession_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @membership_fk_exists = (SELECT COUNT(*) FROM information_schema.table_constraints WHERE constraint_schema='ishep_crm' AND table_name='users' AND constraint_name='users_membership_type_foreign');
CREATE TABLE IF NOT EXISTS `membership_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `application_number` varchar(32) NOT NULL, `user_id` bigint unsigned NOT NULL, `membership_type_id` bigint unsigned NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'draft', `declaration_at` timestamp NULL DEFAULT NULL, `submitted_at` timestamp NULL DEFAULT NULL, `reviewed_by` bigint unsigned DEFAULT NULL,
  `review_started_at` timestamp NULL DEFAULT NULL, `decided_at` timestamp NULL DEFAULT NULL, `decision_reason` varchar(1000) DEFAULT NULL, `internal_note` varchar(2000) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL, `updated_at` timestamp NULL DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `membership_applications_number_unique` (`application_number`),
  KEY `membership_applications_user_status_index` (`user_id`,`status`), KEY `membership_applications_status_created_index` (`status`,`created_at`), KEY `membership_applications_type_index` (`membership_type_id`), KEY `membership_applications_reviewer_index` (`reviewed_by`),
  CONSTRAINT `membership_applications_user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `membership_applications_type_foreign` FOREIGN KEY (`membership_type_id`) REFERENCES `membership_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `membership_applications_reviewer_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `membership_application_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT, `membership_application_id` bigint unsigned NOT NULL, `actor_user_id` bigint unsigned DEFAULT NULL, `previous_status` varchar(32) DEFAULT NULL,
  `new_status` varchar(32) NOT NULL, `note` varchar(1000) DEFAULT NULL, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`),
  KEY `membership_application_events_application_index` (`membership_application_id`,`created_at`), KEY `membership_application_events_actor_index` (`actor_user_id`),
  CONSTRAINT `membership_application_events_application_foreign` FOREIGN KEY (`membership_application_id`) REFERENCES `membership_applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `membership_application_events_actor_foreign` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `document_types` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`code` varchar(100) NOT NULL,`name` varchar(255) NOT NULL,`description` text DEFAULT NULL,`active` tinyint(1) NOT NULL DEFAULT 1,`created_at` timestamp NULL DEFAULT NULL,`updated_at` timestamp NULL DEFAULT NULL,PRIMARY KEY(`id`),UNIQUE KEY `document_types_code_unique`(`code`),KEY `document_types_active_index`(`active`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `membership_document_requirements` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`membership_type_id` bigint unsigned NOT NULL,`document_type_id` bigint unsigned NOT NULL,`required` tinyint(1) NOT NULL DEFAULT 0,`active` tinyint(1) NOT NULL DEFAULT 1,`created_at` timestamp NULL DEFAULT NULL,`updated_at` timestamp NULL DEFAULT NULL,PRIMARY KEY(`id`),UNIQUE KEY `membership_document_requirements_unique`(`membership_type_id`,`document_type_id`),CONSTRAINT `mdr_type_foreign` FOREIGN KEY(`membership_type_id`) REFERENCES `membership_types`(`id`) ON DELETE CASCADE,CONSTRAINT `mdr_document_foreign` FOREIGN KEY(`document_type_id`) REFERENCES `document_types`(`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `member_documents` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`document_reference` varchar(40) NOT NULL,`user_id` bigint unsigned NOT NULL,`membership_application_id` bigint unsigned NOT NULL,`document_type_id` bigint unsigned NOT NULL,`original_filename` varchar(255) NOT NULL,`storage_key` varchar(255) NOT NULL,`relative_path` varchar(500) NOT NULL,`detected_mime` varchar(100) NOT NULL,`extension` varchar(10) NOT NULL,`byte_size` bigint unsigned NOT NULL,`sha256` char(64) NOT NULL,`verification_status` varchar(32) NOT NULL DEFAULT 'pending',`uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`verified_at` timestamp NULL DEFAULT NULL,`verified_by` bigint unsigned DEFAULT NULL,`rejection_reason` varchar(1000) DEFAULT NULL,`internal_note` varchar(2000) DEFAULT NULL,`replaced_document_id` bigint unsigned DEFAULT NULL,`created_at` timestamp NULL DEFAULT NULL,`updated_at` timestamp NULL DEFAULT NULL,PRIMARY KEY(`id`),UNIQUE KEY `member_documents_reference_unique`(`document_reference`),UNIQUE KEY `member_documents_storage_key_unique`(`storage_key`),KEY `member_documents_user_status_index`(`user_id`,`verification_status`),KEY `member_documents_application_index`(`membership_application_id`),KEY `member_documents_type_status_index`(`document_type_id`,`verification_status`),CONSTRAINT `member_documents_user_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,CONSTRAINT `member_documents_application_foreign` FOREIGN KEY(`membership_application_id`) REFERENCES `membership_applications`(`id`) ON DELETE CASCADE,CONSTRAINT `member_documents_type_foreign` FOREIGN KEY(`document_type_id`) REFERENCES `document_types`(`id`) ON DELETE RESTRICT,CONSTRAINT `member_documents_verifier_foreign` FOREIGN KEY(`verified_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,CONSTRAINT `member_documents_replaced_foreign` FOREIGN KEY(`replaced_document_id`) REFERENCES `member_documents`(`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `member_document_events` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`member_document_id` bigint unsigned NOT NULL,`actor_user_id` bigint unsigned DEFAULT NULL,`event_type` varchar(50) NOT NULL,`previous_status` varchar(32) DEFAULT NULL,`new_status` varchar(32) NOT NULL,`note` varchar(1000) DEFAULT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(`id`),KEY `member_document_events_document_index`(`member_document_id`,`created_at`),CONSTRAINT `member_document_events_document_foreign` FOREIGN KEY(`member_document_id`) REFERENCES `member_documents`(`id`) ON DELETE CASCADE,CONSTRAINT `member_document_events_actor_foreign` FOREIGN KEY(`actor_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT IGNORE INTO `document_types`(`code`,`name`,`description`,`active`,`created_at`,`updated_at`) VALUES('supporting_document','Supporting document','Optional supporting document; final requirements remain pending confirmation.',1,UTC_TIMESTAMP(),UTC_TIMESTAMP());

SET @membership_fk_sql = IF(@membership_fk_exists=0,'ALTER TABLE `users` ADD CONSTRAINT `users_membership_type_foreign` FOREIGN KEY (`membership_type_id`) REFERENCES `membership_types` (`id`) ON DELETE SET NULL','SELECT 1');
PREPARE membership_fk_statement FROM @membership_fk_sql;
EXECUTE membership_fk_statement;
DEALLOCATE PREPARE membership_fk_statement;

INSERT IGNORE INTO `roles` (`code`,`name`,`description`,`is_system`,`created_at`,`updated_at`) VALUES
('registered_user','Registered User','Default public account role.',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('administrator','Administrator','User administration and content moderation.',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('finance','Finance','Payment, refund and finance reporting access.',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('super_user','Super User / IT','Full system administration access.',1,UTC_TIMESTAMP(),UTC_TIMESTAMP());

INSERT IGNORE INTO `permissions` (`code`,`name`,`created_at`,`updated_at`) VALUES
('profile.view_own','Profile View Own',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('profile.update_own','Profile Update Own',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('membership.apply','Membership Apply',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('membership.view_own','Membership View Own',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('documents.upload_own','Documents Upload Own',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('payments.view_own','Payments View Own',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('payments.manage','Payments Manage',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('refunds.request','Refunds Request',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('refunds.process','Refunds Process',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('certificates.view_own','Certificates View Own',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('users.view','Users View',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('users.create','Users Create',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('users.review','Users Review',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('users.suspend','Users Suspend',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('users.assign_roles','Users Assign Roles',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('reports.view','Reports View',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('notices.publish','Notices Publish',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('jobs.post','Jobs Post',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('jobs.moderate','Jobs Moderate',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('bursaries.post','Bursaries Post',UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('bursaries.moderate','Bursaries Moderate',UTC_TIMESTAMP(),UTC_TIMESTAMP()),('system.manage','System Manage',UTC_TIMESTAMP(),UTC_TIMESTAMP());

INSERT IGNORE INTO `role_permissions` (`role_id`,`permission_id`)
SELECT r.id,p.id FROM roles r JOIN permissions p WHERE r.code='registered_user' AND p.code IN ('profile.view_own','profile.update_own','membership.apply','membership.view_own','documents.upload_own','payments.view_own','refunds.request','certificates.view_own');
INSERT IGNORE INTO `role_permissions` (`role_id`,`permission_id`)
SELECT r.id,p.id FROM roles r JOIN permissions p WHERE r.code='administrator' AND p.code IN ('users.view','users.create','users.review','users.suspend','users.assign_roles','reports.view','notices.publish','jobs.post','jobs.moderate','bursaries.post','bursaries.moderate');
INSERT IGNORE INTO `role_permissions` (`role_id`,`permission_id`)
SELECT r.id,p.id FROM roles r JOIN permissions p WHERE r.code='finance' AND p.code IN ('payments.view_own','payments.manage','refunds.request','refunds.process','reports.view');
INSERT IGNORE INTO `role_permissions` (`role_id`,`permission_id`)
SELECT r.id,p.id FROM roles r JOIN permissions p WHERE r.code='super_user';

INSERT IGNORE INTO `provinces` (`code`,`name`,`active`,`created_at`,`updated_at`) VALUES
('EC','Eastern Cape',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),('FS','Free State',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('GP','Gauteng',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),('KZN','KwaZulu-Natal',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('LP','Limpopo',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),('MP','Mpumalanga',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('NC','Northern Cape',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),('NW','North West',1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('WC','Western Cape',1,UTC_TIMESTAMP(),UTC_TIMESTAMP());

INSERT IGNORE INTO `membership_types` (`code`,`name`,`description`,`fee`,`billing_period`,`is_student`,`active`,`created_at`,`updated_at`) VALUES
('company','Company','Fee temporarily set to zero pending business confirmation.',0.00,'annual',0,1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('individual','Individual','Fee temporarily set to zero pending business confirmation.',0.00,'annual',0,1,UTC_TIMESTAMP(),UTC_TIMESTAMP()),
('student','Student','Fee temporarily set to zero pending business confirmation.',0.00,'annual',1,1,UTC_TIMESTAMP(),UTC_TIMESTAMP());

-- Finance, invoicing, manual-payment, refund, and membership-activation foundation.
CREATE TABLE IF NOT EXISTS `membership_fee_schedules` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`fee_reference` varchar(40) NOT NULL,`membership_type_id` bigint unsigned NOT NULL,`amount` decimal(12,2) NOT NULL,`currency` char(3) NOT NULL DEFAULT 'ZAR',`billing_period` varchar(30) NOT NULL,`effective_from` date NOT NULL,`effective_to` date DEFAULT NULL,`active` tinyint(1) NOT NULL DEFAULT 1,`created_by` bigint unsigned NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY(`id`),UNIQUE KEY `fee_schedules_reference_unique`(`fee_reference`),KEY `fee_schedules_lookup_index`(`membership_type_id`,`active`,`effective_from`,`effective_to`),CONSTRAINT `fee_schedules_type_foreign` FOREIGN KEY(`membership_type_id`) REFERENCES `membership_types`(`id`) ON DELETE RESTRICT,CONSTRAINT `fee_schedules_creator_foreign` FOREIGN KEY(`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,CONSTRAINT `fee_schedules_amount_check` CHECK (`amount` >= 0),CONSTRAINT `fee_schedules_dates_check` CHECK (`effective_to` IS NULL OR `effective_to` >= `effective_from`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `memberships` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`membership_number` varchar(40) NOT NULL,`user_id` bigint unsigned NOT NULL,`membership_application_id` bigint unsigned NOT NULL,`membership_type_id` bigint unsigned NOT NULL,`status` varchar(40) NOT NULL,`starts_at` date DEFAULT NULL,`expires_at` date DEFAULT NULL,`activated_at` timestamp NULL DEFAULT NULL,`suspended_at` timestamp NULL DEFAULT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY(`id`),UNIQUE KEY `memberships_number_unique`(`membership_number`),UNIQUE KEY `memberships_application_unique`(`membership_application_id`),KEY `memberships_user_status_index`(`user_id`,`status`),KEY `memberships_type_status_index`(`membership_type_id`,`status`),CONSTRAINT `memberships_user_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,CONSTRAINT `memberships_application_foreign` FOREIGN KEY(`membership_application_id`) REFERENCES `membership_applications`(`id`) ON DELETE RESTRICT,CONSTRAINT `memberships_type_foreign` FOREIGN KEY(`membership_type_id`) REFERENCES `membership_types`(`id`) ON DELETE RESTRICT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `invoices` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`invoice_number` varchar(40) NOT NULL,`user_id` bigint unsigned NOT NULL,`membership_id` bigint unsigned NOT NULL,`membership_application_id` bigint unsigned NOT NULL,`currency` char(3) NOT NULL,`subtotal` decimal(12,2) NOT NULL,`total` decimal(12,2) NOT NULL,`amount_paid` decimal(12,2) NOT NULL DEFAULT 0,`balance_due` decimal(12,2) NOT NULL,`status` varchar(30) NOT NULL DEFAULT 'issued',`issued_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`due_at` timestamp NULL DEFAULT NULL,`paid_at` timestamp NULL DEFAULT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY(`id`),UNIQUE KEY `invoices_number_unique`(`invoice_number`),UNIQUE KEY `invoices_membership_unique`(`membership_id`),KEY `invoices_user_status_index`(`user_id`,`status`),KEY `invoices_application_index`(`membership_application_id`),KEY `invoices_issued_index`(`issued_at`),CONSTRAINT `invoices_user_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,CONSTRAINT `invoices_membership_foreign` FOREIGN KEY(`membership_id`) REFERENCES `memberships`(`id`) ON DELETE RESTRICT,CONSTRAINT `invoices_application_foreign` FOREIGN KEY(`membership_application_id`) REFERENCES `membership_applications`(`id`) ON DELETE RESTRICT,CONSTRAINT `invoices_amounts_check` CHECK (`subtotal` >= 0 AND `total` >= 0 AND `amount_paid` >= 0 AND `balance_due` >= 0)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `invoice_items` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`invoice_id` bigint unsigned NOT NULL,`description` varchar(255) NOT NULL,`quantity` decimal(10,2) NOT NULL DEFAULT 1,`unit_amount` decimal(12,2) NOT NULL,`line_total` decimal(12,2) NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(`id`),KEY `invoice_items_invoice_index`(`invoice_id`),CONSTRAINT `invoice_items_invoice_foreign` FOREIGN KEY(`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE RESTRICT,CONSTRAINT `invoice_items_amounts_check` CHECK (`quantity` > 0 AND `unit_amount` >= 0 AND `line_total` >= 0)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `payments` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`payment_reference` varchar(40) NOT NULL,`user_id` bigint unsigned NOT NULL,`amount` decimal(12,2) NOT NULL,`currency` char(3) NOT NULL,`payment_method` varchar(30) NOT NULL,`manual_reference` varchar(100) NOT NULL,`received_at` timestamp NOT NULL,`recorded_by` bigint unsigned NOT NULL,`status` varchar(30) NOT NULL DEFAULT 'completed',`note` varchar(1000) DEFAULT NULL,`reversal_of_payment_id` bigint unsigned DEFAULT NULL,`reversal_reason` varchar(1000) DEFAULT NULL,`idempotency_key` char(64) NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(`id`),UNIQUE KEY `payments_reference_unique`(`payment_reference`),UNIQUE KEY `payments_manual_reference_unique`(`manual_reference`),UNIQUE KEY `payments_idempotency_unique`(`idempotency_key`),UNIQUE KEY `payments_reversal_unique`(`reversal_of_payment_id`),KEY `payments_user_status_index`(`user_id`,`status`),KEY `payments_received_index`(`received_at`),CONSTRAINT `payments_user_foreign` FOREIGN KEY(`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT,CONSTRAINT `payments_recorder_foreign` FOREIGN KEY(`recorded_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,CONSTRAINT `payments_reversal_foreign` FOREIGN KEY(`reversal_of_payment_id`) REFERENCES `payments`(`id`) ON DELETE RESTRICT,CONSTRAINT `payments_amount_check` CHECK (`amount` > 0)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `payment_allocations` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`payment_id` bigint unsigned NOT NULL,`invoice_id` bigint unsigned NOT NULL,`amount` decimal(12,2) NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(`id`),UNIQUE KEY `payment_allocations_unique`(`payment_id`,`invoice_id`),KEY `payment_allocations_invoice_index`(`invoice_id`),CONSTRAINT `payment_allocations_payment_foreign` FOREIGN KEY(`payment_id`) REFERENCES `payments`(`id`) ON DELETE RESTRICT,CONSTRAINT `payment_allocations_invoice_foreign` FOREIGN KEY(`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE RESTRICT,CONSTRAINT `payment_allocations_amount_check` CHECK (`amount` > 0)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `refund_requests` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`request_reference` varchar(40) NOT NULL,`payment_id` bigint unsigned NOT NULL,`requested_by` bigint unsigned NOT NULL,`requested_amount` decimal(12,2) NOT NULL,`reason` varchar(1000) NOT NULL,`status` varchar(30) NOT NULL DEFAULT 'requested',`reviewed_by` bigint unsigned DEFAULT NULL,`reviewed_at` timestamp NULL DEFAULT NULL,`decision_reason` varchar(1000) DEFAULT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY(`id`),UNIQUE KEY `refund_requests_reference_unique`(`request_reference`),KEY `refund_requests_payment_status_index`(`payment_id`,`status`),KEY `refund_requests_requester_index`(`requested_by`,`created_at`),CONSTRAINT `refund_requests_payment_foreign` FOREIGN KEY(`payment_id`) REFERENCES `payments`(`id`) ON DELETE RESTRICT,CONSTRAINT `refund_requests_requester_foreign` FOREIGN KEY(`requested_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,CONSTRAINT `refund_requests_reviewer_foreign` FOREIGN KEY(`reviewed_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,CONSTRAINT `refund_requests_amount_check` CHECK (`requested_amount` > 0)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `refunds` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`refund_reference` varchar(40) NOT NULL,`refund_request_id` bigint unsigned NOT NULL,`payment_id` bigint unsigned NOT NULL,`amount` decimal(12,2) NOT NULL,`method` varchar(30) NOT NULL,`processed_by` bigint unsigned NOT NULL,`processed_at` timestamp NOT NULL,`manual_reference` varchar(100) NOT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(`id`),UNIQUE KEY `refunds_reference_unique`(`refund_reference`),UNIQUE KEY `refunds_request_unique`(`refund_request_id`),UNIQUE KEY `refunds_manual_reference_unique`(`manual_reference`),KEY `refunds_payment_index`(`payment_id`),CONSTRAINT `refunds_request_foreign` FOREIGN KEY(`refund_request_id`) REFERENCES `refund_requests`(`id`) ON DELETE RESTRICT,CONSTRAINT `refunds_payment_foreign` FOREIGN KEY(`payment_id`) REFERENCES `payments`(`id`) ON DELETE RESTRICT,CONSTRAINT `refunds_processor_foreign` FOREIGN KEY(`processed_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT,CONSTRAINT `refunds_amount_check` CHECK (`amount` > 0)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `finance_events` (`id` bigint unsigned NOT NULL AUTO_INCREMENT,`entity_type` varchar(50) NOT NULL,`entity_reference` varchar(40) NOT NULL,`actor_user_id` bigint unsigned DEFAULT NULL,`previous_status` varchar(40) DEFAULT NULL,`new_status` varchar(40) DEFAULT NULL,`action` varchar(80) NOT NULL,`note` varchar(1000) DEFAULT NULL,`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY(`id`),KEY `finance_events_entity_index`(`entity_type`,`entity_reference`,`created_at`),KEY `finance_events_actor_index`(`actor_user_id`,`created_at`),CONSTRAINT `finance_events_actor_foreign` FOREIGN KEY(`actor_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
