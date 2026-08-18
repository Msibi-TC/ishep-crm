-- Safe additive patch for databases installed before registration captured membership type.
-- Review column/constraint existence before running this patch once.
USE `ishep_crm`;

ALTER TABLE `users`
  ADD COLUMN `membership_type_id` bigint unsigned NULL AFTER `password`,
  ADD KEY `users_membership_type_index` (`membership_type_id`),
  ADD CONSTRAINT `users_membership_type_foreign`
    FOREIGN KEY (`membership_type_id`) REFERENCES `membership_types` (`id`) ON DELETE SET NULL;
