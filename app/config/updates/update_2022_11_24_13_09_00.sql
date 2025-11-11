START TRANSACTION;

ALTER TABLE `extras` ADD COLUMN `price` decimal(9,2) DEFAULT NULL AFTER `id`;
ALTER TABLE `extras` ADD COLUMN `image_path` varchar(255) DEFAULT NULL AFTER `price`;

ALTER TABLE `bookings` MODIFY `payment_method` varchar(255) DEFAULT NULL;


INSERT INTO `plugin_base_fields` VALUES (NULL, '_schedule_pm_ARRAY_saferpay', 'arrays', '_schedule_pm_ARRAY_saferpay', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PaySafe', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_payment_status_ARRAY_7', 'arrays', '_driver_payment_status_ARRAY_7', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer paid total price (%s) by PaySafe', 'script');


COMMIT;