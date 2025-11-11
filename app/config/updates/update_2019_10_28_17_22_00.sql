
START TRANSACTION;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblScheduleNumberOfOrder', 'backend', 'Label / Number of order', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '%d Order', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblScheduleNumberOfOrders', 'backend', 'Label / Number of orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '%d Orders', 'script');


COMMIT;