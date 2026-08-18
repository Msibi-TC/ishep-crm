-- Additive membership-application workflow for the existing ishep_crm database.
USE `ishep_crm`;

CREATE TABLE IF NOT EXISTS `membership_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `application_number` varchar(32) NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `membership_type_id` bigint unsigned NOT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'draft',
  `declaration_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `review_started_at` timestamp NULL DEFAULT NULL,
  `decided_at` timestamp NULL DEFAULT NULL,
  `decision_reason` varchar(1000) DEFAULT NULL,
  `internal_note` varchar(2000) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `membership_applications_number_unique` (`application_number`),
  KEY `membership_applications_user_status_index` (`user_id`,`status`),
  KEY `membership_applications_status_created_index` (`status`,`created_at`),
  KEY `membership_applications_type_index` (`membership_type_id`),
  KEY `membership_applications_reviewer_index` (`reviewed_by`),
  CONSTRAINT `membership_applications_user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `membership_applications_type_foreign` FOREIGN KEY (`membership_type_id`) REFERENCES `membership_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `membership_applications_reviewer_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `membership_application_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `membership_application_id` bigint unsigned NOT NULL,
  `actor_user_id` bigint unsigned DEFAULT NULL,
  `previous_status` varchar(32) DEFAULT NULL,
  `new_status` varchar(32) NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `membership_application_events_application_index` (`membership_application_id`,`created_at`),
  KEY `membership_application_events_actor_index` (`actor_user_id`),
  CONSTRAINT `membership_application_events_application_foreign` FOREIGN KEY (`membership_application_id`) REFERENCES `membership_applications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `membership_application_events_actor_foreign` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
