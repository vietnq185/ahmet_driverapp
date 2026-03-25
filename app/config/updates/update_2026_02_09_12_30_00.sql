
START TRANSACTION;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_whatsapp', 'backend', 'Label / WhatsApp', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_send_whatsapp_message', 'backend', 'Label / Send WhatsApp Message', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send WhatsApp Message', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_whatsapp_templates', 'backend', 'Label / Templates', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Templates', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_whatsapp_provider', 'backend', 'Label / Provider', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Provider', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_select_template_or_enter_message', 'backend', 'Label / Please select a template or enter a message.', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Please select a template or enter a message.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_message_sent_successfully', 'backend', 'Label / Message sent successfully!', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message sent successfully!', 'script');


  		
COMMIT;