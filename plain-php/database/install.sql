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
  `remember_token` varchar(100) DEFAULT NULL,
  `account_status` varchar(255) NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_account_status_index` (`account_status`),
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
