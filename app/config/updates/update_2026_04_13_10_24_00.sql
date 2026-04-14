
START TRANSACTION;

ALTER TABLE `vehicles` ADD COLUMN `fuel_consumption` decimal(9,2) DEFAULT NULL;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_fuel_price', 1, NULL, NULL, 'float', 2, 1, NULL);


INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_o_fuel_price', 'backend', 'Label / Fuel Price', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fuel Price (€/Liter)', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleFuelConsumption', 'backend', 'Label / Fuel Consumption', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fuel Consumption (L/100km)', 'script');




COMMIT;