
START TRANSACTION;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAllVehicles', 'backend', 'Label / All vehicles', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All vehicles', 'script');

  		
COMMIT;