
START TRANSACTION;

UPDATE `plugin_base_options` SET `is_visible`=0 WHERE `key`='o_min_gap_fill_seconds';

ALTER TABLE `api_cache_distances` ADD COLUMN `distance_meters` int(11) DEFAULT NULL;


ALTER TABLE `bookings` ADD COLUMN (
	`empty_travel_start_time` datetime DEFAULT NULL,
	`empty_travel_arrival_time` datetime DEFAULT NULL
);


INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_unload_payment_buffer_seconds', 1, '5', '', 'int', 9, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_unload_payment_buffer_seconds', 'backend', 'Options / Unloading/Payment time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Unloading/Payment time', 'script');


COMMIT;