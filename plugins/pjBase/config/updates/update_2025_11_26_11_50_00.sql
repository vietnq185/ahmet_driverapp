
START TRANSACTION;

INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_buffer', 1, '5', '', 'int', 8, 1, NULL),
(1, 'o_google_api_key', 1, '', '', 'string', 9, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_google_api_key', 'backend', 'Options / Google API key', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Google API key', 'script');



INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_max_wait_time_seconds', 1, '120', '', 'int', 10, 1, NULL),
(1, 'o_min_gap_fill_seconds', 1, '45', '', 'int', 11, 1, NULL);

UPDATE `plugin_base_options` SET `order`=12 WHERE `key`='o_google_api_key';


INSERT INTO `fields` VALUES (NULL, 'plugin_base_label_minutes', 'backend', 'Options / minutes', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'minutes', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_max_wait_time_seconds', 'backend', 'Options / Maximum Allowed Wait Time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maximum Allowed Wait Time', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_max_wait_time_seconds_text', 'backend', 'Options / Maximum Allowed Wait Time Text', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The maximum amount of time a vehicle is allowed to wait between completing one trip (Drop-off) and starting the travel to the next booking (Pickup).', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_min_gap_fill_seconds', 'backend', 'Options / Minimum Gap Threshold for Insertion', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Minimum Gap Threshold for Insertion', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_min_gap_fill_seconds_text', 'backend', 'Options / Minimum Gap Threshold for Insertion Text', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The smallest time gap between two existing trips that the system will consider for inserting a new booking (Gap Filling).', 'script');




INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_buffer_text', 'backend', 'Options / Safety Buffer Time Text', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The minimum time required to be automatically added after the end of every trip (Drop-off) before the vehicle is considered available to travel to the next Pickup location.', 'script');


COMMIT;