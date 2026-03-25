
START TRANSACTION;

ALTER TABLE `providers` ADD COLUMN `name_sign_logo` varchar(255) DEFAULT NULL;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblProviderNameSignLogo', 'backend', 'Label / Logo to the name sign', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logo to the name sign', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblProviderLogo', 'backend', 'Label / Provider Logo', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Provider Logo', 'script');
  		
COMMIT;