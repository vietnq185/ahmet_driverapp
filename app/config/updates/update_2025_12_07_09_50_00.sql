
START TRANSACTION;



INSERT INTO `fields` VALUES (NULL, 'lblScheduleTransferTime', 'backend', 'Label / Transfer time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Transfer time', 'script');


COMMIT;