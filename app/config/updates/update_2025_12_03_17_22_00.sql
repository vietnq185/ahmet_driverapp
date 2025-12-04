
START TRANSACTION;


ALTER TABLE `vehicles` ADD COLUMN `schedule_status` enum('T','F') DEFAULT 'T';



INSERT INTO `fields` VALUES (NULL, 'lblVehicleScheduleStatus', 'backend', 'Label / Status on Schedule', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status on Schedule', 'script');


COMMIT;