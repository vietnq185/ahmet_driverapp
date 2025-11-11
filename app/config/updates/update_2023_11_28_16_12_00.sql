START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `credit_card_fee` decimal(9,2) DEFAULT NULL AFTER `sub_total`;


INSERT INTO `fields` VALUES (NULL, 'report_driver', 'backend', 'Label / Driver', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver', 'script');
  
COMMIT;