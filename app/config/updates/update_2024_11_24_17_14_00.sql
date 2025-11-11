START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `name_sign` varchar(255) DEFAULT NULL;


INSERT INTO `fields` VALUES (NULL, 'lblNameSignFile', 'backend', 'Label / Name sign file', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name sign file', 'script');
  
COMMIT;