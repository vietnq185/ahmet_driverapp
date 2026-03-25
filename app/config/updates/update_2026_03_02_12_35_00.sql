
START TRANSACTION;

UPDATE `plugin_base_options` SET `value`='1|0::1', `is_visible`='0' WHERE `key`='o_enable_send_whatsapp_button_on_schedule_page';

INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_enable_whatsapp_fearure', 1, '1|0::0', NULL, 'bool', 31, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_enable_whatsapp_fearure', 'backend', 'Options / Enable WhatsApp Messaging', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Enable WhatsApp Messaging', 'script');
	


COMMIT;