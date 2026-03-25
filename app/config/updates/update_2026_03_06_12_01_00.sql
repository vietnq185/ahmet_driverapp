
START TRANSACTION;


ALTER TABLE `logs` ADD COLUMN `booking_date` date DEFAULT NULL;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblSecondVehiclesDriver', 'backend', 'Label / Second vehicles driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Second vehicles driver', 'script');
		
  		
  		
COMMIT;