START TRANSACTION;



INSERT INTO `fields` VALUES (NULL, 'btnChange', 'backend', 'Label / Change', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Change', 'script');
  
INSERT INTO `fields` VALUES (NULL, 'lblChangeTime', 'backend', 'Label / Change time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Change time', 'script');
  

COMMIT;