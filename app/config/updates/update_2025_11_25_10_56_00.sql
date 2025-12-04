START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `region` varchar(255) DEFAULT NULL;
ALTER TABLE `locations` ADD COLUMN `region` varchar(255) DEFAULT NULL;
ALTER TABLE `dropoff` ADD COLUMN `region` varchar(255) DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblTransferRegion', 'backend', 'Label / Region', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Region', 'script');


INSERT INTO `fields` VALUES (NULL, 'lblScheduleOrderClient', 'backend', 'Label / Client', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblScheduleOrderTransferDestinations', 'backend', 'Label / Transfer Destinations', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Transfer Destinations', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblScheduleOrderVehicle', 'backend', 'Label / Vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblScheduleOrderPassengers', 'backend', 'Label / Passengers', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Passengers', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblScheduleOrderOrderID', 'backend', 'Label / Order ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order ID', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblScheduleOrderTotal', 'backend', 'Label / Total', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');


COMMIT;