
START TRANSACTION;



ALTER TABLE `bookings` ADD COLUMN `is_enter_hale_cash_register` tinyint(1) DEFAULT '0';


INSERT INTO `fields` VALUES (NULL, 'infoDriverPaymentCreditCardTitle', 'backend', 'Info / Driver payment credit card title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment Credit Card', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoDriverPaymentCreditCardBody', 'backend', 'Info / Driver payment credit card body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Enter Hale cash register?', 'script');



INSERT INTO `fields` VALUES (NULL, 'infoUnassignOrdersTitle', 'backend', 'Info / Reset', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Reset', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUnassignOrdersDesc', 'backend', 'Info / Are you sure you want to reset the bookings?', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to reset the bookings?', 'script');


COMMIT;