
START TRANSACTION;

INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_infleet_api_token', 1, '', '', 'string', 15, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_infleet_api_token', 'backend', 'Options / infleet API token', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'infleet API token', 'script');


INSERT INTO `fields` VALUES (NULL, 'lblTrackingVehicles', 'backend', 'Label / Vehicles', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTrackingSearchPlaceholder', 'backend', 'Label / Name, Make, Model, License Plate...', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name, Make, Model, License Plate...', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTrackingVehicleStatus', 'backend', 'Label / Vehicle Status', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle Status', 'script');

INSERT INTO `fields` VALUES (NULL, 'tracking_vehicle_statuses_ARRAY_0', 'arrays', 'tracking_vehicle_statuses_ARRAY_0', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Stopped', 'script');

INSERT INTO `fields` VALUES (NULL, 'tracking_vehicle_statuses_ARRAY_1', 'arrays', 'tracking_vehicle_statuses_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Moving', 'script');

INSERT INTO `fields` VALUES (NULL, 'tracking_vehicle_statuses_ARRAY_2', 'arrays', 'tracking_vehicle_statuses_ARRAY_2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All', 'script');

INSERT INTO `fields` VALUES (NULL, 'btnLocateVehicleOnMap', 'backend', 'Label / Locate Vehicle on Map', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Locate Vehicle on Map', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTrackingVehicleMake', 'backend', 'Label / Make', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Make', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTrackingVehicleModel', 'backend', 'Label / Model', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Model', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTrackingVehicleLicensePlate', 'backend', 'Label / LicensePlate', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'License Plate', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTrackingVehicleSeats', 'backend', 'Label / Seats', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Seats', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTrackingClickToToggleTracking', 'backend', 'Label / Click to toggle tracking', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Click to toggle tracking', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoTrackingTitle', 'backend', 'Label / Live Vehicle Tracking title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Live Vehicle Tracking', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoTrackingBody', 'backend', 'Label / Live Vehicle Tracking body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Monitor your fleet''s real-time movements and operational status on a live map.', 'script');


INSERT INTO `fields` VALUES (NULL, 'menuLiveTracking', 'backend', 'Label / Live Tracking', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Live Tracking', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminTracking_pjActionIndex');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminTracking_pjActionIndex', 'backend', 'pjAdminTracking_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Live Tracking Menu', 'script');


COMMIT;