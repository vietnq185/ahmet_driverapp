START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `locale_id` int(10) DEFAULT '1';


INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnSchedule', 'backend', 'Button / Schedule', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Schedule', 'script');




COMMIT;