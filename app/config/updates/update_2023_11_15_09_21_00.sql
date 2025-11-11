START TRANSACTION;

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_area', 'arrays', '_synch_types_ARRAY_area', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Area', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_synch_types_ARRAY_station', 'arrays', '_synch_types_ARRAY_station', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Station', 'script');



COMMIT;