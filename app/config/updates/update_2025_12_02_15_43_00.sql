
START TRANSACTION;

ALTER TABLE `whatsapp_messages` ADD COLUMN `order` int(10) unsigned DEFAULT NULL; 

ALTER TABLE `vehicles` ADD COLUMN (
	`is_ski` tinyint(1) DEFAULT '0',
	`is_snowboard` tinyint(1) DEFAULT '0'
);



INSERT INTO `fields` VALUES (NULL, 'lblIsSki', 'backend', 'Label / Is Ski?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is Ski?', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblIsSnowboard', 'backend', 'Label / Is Snowboard?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is Snowboard?', 'script');




INSERT INTO `fields` VALUES (NULL, '_yesno_ARRAY_1', 'arrays', '_yesno_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Yes', 'script');

INSERT INTO `fields` VALUES (NULL, '_yesno_ARRAY_0', 'arrays', '_yesno_ARRAY_0', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No', 'script');


COMMIT;