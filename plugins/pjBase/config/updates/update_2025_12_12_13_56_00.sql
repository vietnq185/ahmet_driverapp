
START TRANSACTION;


INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_base_max_wait_time_seconds', 1, '2400', '', 'int', 12, 1, NULL),
(1, 'o_base_radius', 1, '30', '', 'int', 13, 1, NULL);

UPDATE `plugin_base_options` SET `order`=14 WHERE `key`='o_google_api_key';


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_base_max_wait_time_seconds', 'backend', 'Options / Maximum Allowed Wait Time Base Station (Innsbruck Airport)', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maximum Allowed Wait Time Base Station (Innsbruck Airport)', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_base_radius', 'backend', 'Options / Radius', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Radius', 'script');



COMMIT;