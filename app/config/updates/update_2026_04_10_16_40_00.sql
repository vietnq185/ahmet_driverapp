
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_admin_change_payment_status_email', 2, NULL, NULL, 'string', 1, 0, NULL);

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_o_admin_change_payment_status_email', 'backend', 'Label / Email address', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email address', 'script');



COMMIT;