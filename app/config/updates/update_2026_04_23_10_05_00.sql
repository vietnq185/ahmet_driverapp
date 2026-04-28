
START TRANSACTION;

INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_driver_past_booking_days', 1, '7', '', 'int', 38, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_driver_past_booking_days', 'backend', 'Options / Driver Past Bookings Visibility (Days)', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver Past Bookings Visibility (Days)', 'script');



COMMIT;