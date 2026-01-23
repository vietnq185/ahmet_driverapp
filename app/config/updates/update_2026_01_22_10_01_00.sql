
START TRANSACTION;


INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_rapidapi_key', 1, '', '', 'string', 19, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_rapidapi_key', 'backend', 'Options / Rapid API Key', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Rapid API Key', 'script');


INSERT INTO `fields` VALUES (NULL, 'btnCheckFlights', 'backend', 'Label / Check flights', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Check flights', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblFlightInformation', 'backend', 'Label / Flight Information', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flight Information', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblFlightAirline', 'backend', 'Label / Flight / Airline', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flight / Airline', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblFlightTrackDeparture', 'backend', 'Label / Departure', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Departure', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblFlightTrackArrival', 'backend', 'Label / Arrival (Sch / Est)', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Arrival (Sch / Est)', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblFlightStatus', 'backend', 'Label / Status', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblFlightRemarks', 'backend', 'Label / Remarks', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remarks', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblFlightDelay', 'backend', 'Label / DELAY', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'DELAY', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblFlightOnTime', 'backend', 'Label / ON TIME', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'ON TIME', 'script');
  		
INSERT INTO `fields` VALUES (NULL, 'lblLoadingFlightData', 'backend', 'Label / Loading flight data...', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Loading flight data...', 'script');
	
  		
  		
COMMIT;