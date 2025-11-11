
START TRANSACTION;

INSERT INTO `plugin_auth_roles` (`id`, `role`, `is_admin`, `status`) VALUES
(3, 'Driver', 'F', 'T');

ALTER TABLE `plugin_auth_users` ADD COLUMN `locale_id` int(10) unsigned DEFAULT NULL;

COMMIT;