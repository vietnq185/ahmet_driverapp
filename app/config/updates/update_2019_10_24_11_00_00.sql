
START TRANSACTION;


ALTER TABLE `bookings` ADD COLUMN `prev_passengers` int(5) DEFAULT NULL AFTER `prev_booking_date`;
UPDATE `bookings` SET `prev_passengers`=`passengers`;
ALTER TABLE `bookings` DROP COLUMN `confirmed_time_change`;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnViewTurnover', 'backend', 'Button / View Turnover', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'View Turnover', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnClose', 'backend', 'Button / Close', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Close', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblViewTurnoverTitle', 'backend', 'Label / Turnover', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Turnover', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTurnoverCash', 'backend', 'Label / Cash', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTurnoverCreditCard', 'backend', 'Label / Credit card', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTurnoverPrepaid', 'backend', 'Label / Prepaid', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Prepaid', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTurnoverTotal', 'backend', 'Label / Total', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderNewNumPassengers', 'backend', 'Label / No. new passengers', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No. new passengers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderNumUpdated', 'backend', 'Label / No. passengers updated', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No. passengers updated', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnRemoveDriverStatus', 'backend', 'Button / Remove status', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove status', 'script');


COMMIT;