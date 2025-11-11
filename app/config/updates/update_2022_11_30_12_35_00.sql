START TRANSACTION;

DELETE FROM `plugin_base_fields` WHERE `key`='_driver_payment_status_ARRAY_7';

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPaid', 'backend', 'Label / Paid', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid', 'script');


COMMIT;