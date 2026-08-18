-- Safe additive member-profile patch for the existing ishep_crm database.
USE `ishep_crm`;

CREATE TABLE IF NOT EXISTS `member_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `province_id` bigint unsigned DEFAULT NULL,
  `profession_id` bigint unsigned DEFAULT NULL,
  `organisation` varchar(255) DEFAULT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_profiles_user_unique` (`user_id`),
  KEY `member_profiles_province_index` (`province_id`),
  KEY `member_profiles_profession_index` (`profession_id`),
  CONSTRAINT `member_profiles_user_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `member_profiles_province_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE SET NULL,
  CONSTRAINT `member_profiles_profession_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
