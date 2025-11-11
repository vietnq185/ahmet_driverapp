START TRANSACTION;

ALTER TABLE `locations` ADD COLUMN `color` varchar(255) DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblColor', 'backend', 'Label / Change', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Change', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblNoSelected', 'backend', 'Label / No selected', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No selected', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblReportTotal', 'backend', 'Label / Total', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblReportProvider', 'backend', 'Label / Provider', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Provider', 'script');

COMMIT;