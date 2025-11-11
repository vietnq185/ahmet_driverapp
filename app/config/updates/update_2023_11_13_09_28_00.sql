START TRANSACTION;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblSyncProvider', 'backend', 'Label / Provider', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Provider', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_payment_status_ARRAY_8', 'arrays', '_driver_payment_status_ARRAY_8', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'PaySafe', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblSyncType', 'backend', 'Label / Type', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnSyncUpdate', 'backend', 'Button / Update', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_client', 'arrays', '_synch_types_ARRAY_client', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_driver', 'arrays', '_synch_types_ARRAY_driver', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_voucher', 'arrays', '_synch_types_ARRAY_voucher', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Voucher', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_extra', 'arrays', '_synch_types_ARRAY_extra', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extra', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_fleet', 'arrays', '_synch_types_ARRAY_fleet', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fleet', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_location', 'arrays', '_synch_types_ARRAY_location', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Location', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_price', 'arrays', '_synch_types_ARRAY_price', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_booking', 'arrays', '_synch_types_ARRAY_booking', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoSyncDataTitle', 'backend', 'Info / Sync data title', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Synchronize data', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoSyncDataBody', 'backend', 'Info / Sync data body', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can select type of data to synchronize from the providers.', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnSynchronizeData', 'backend', 'Button / Synchronize data', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Synchronize data', 'script');


COMMIT;