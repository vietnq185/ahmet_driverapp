
START TRANSACTION;


DROP TABLE IF EXISTS `whatsapp_chat_history`;
CREATE TABLE IF NOT EXISTS `whatsapp_chat_history` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `wa_message_id` varchar(255) DEFAULT NULL,
    `provider_id` int(11) DEFAULT NULL,
    `driver_phone` varchar(255) DEFAULT NULL,
    `direction` enum('sent', 'received') DEFAULT NULL,
    `content` text,
    `status` varchar(20) DEFAULT 'sent',
    `created_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX (driver_phone),
    INDEX (provider_id)
);

ALTER TABLE `providers` ADD COLUMN (
	whatsapp_phone_number_id varchar(255) DEFAULT NULL,
	whatsapp_permanent_access_token varchar(255) DEFAULT NULL 
);

INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_whatsapp_business_account_id', 1, '', '', 'string', 21, 1, NULL),
(1, 'o_pusher_key', 1, '', '', 'string', 22, 1, NULL),
(1, 'o_pusher_cluster', 1, '', '', 'string', 23, 1, NULL),
(1, 'o_pusher_app_ai', 1, '', '', 'string', 24, 1, NULL),
(1, 'o_pusher_secret', 1, '', '', 'string', 25, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_whatsapp_business_account_id', 'backend', 'Options / WhatsApp Business Account ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Business Account ID', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_pusher_key', 'backend', 'Options / Pusher Key', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pusher Key', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_pusher_cluster', 'backend', 'Options / Pusher Cluster', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pusher Cluster', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_pusher_app_ai', 'backend', 'Options / Pusher App ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pusher App ID', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_pusher_secret', 'backend', 'Options / Pusher Cluster', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pusher Secret', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblWhatsAppPhoneNumberID', 'backend', 'Label / WhatsApp Phone Number ID', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Phone Number ID', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblWhatsAppPermanentAccessToken', 'backend', 'Label / WhatsApp Permanent Access Token', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Permanent Access Token', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'infoWhatsappChatTitle', 'backend', 'Label / WhatsApp Chat Title', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Chat', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'infoWhatsappChatBody', 'backend', 'Label / WhatsApp Chat Body', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Connecting you directly with your drivers. Monitor routes and provide instant support via our integrated WhatsApp Business API.', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_whatsapp_webhook_url', 'backend', 'Options / WhatsApp Webhook URL', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Webhook URL', 'script');
	
	
INSERT INTO `fields` VALUES (NULL, 'lblChat', 'backend', 'Label / Chat', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Chat', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblSelectADriver', 'backend', 'Label / Select a driver', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select a driver', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblSelectTemplate', 'backend', 'Label / Select Template', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select Template', 'script');
	
INSERT INTO `fields` VALUES (NULL, 'lblSelectProvider', 'backend', 'Label / Select a provider', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select a provider', 'script');
	
INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuWhatsappChat', 'backend', 'menuWhatsappChat', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Chat', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminWhatsappChat', 'backend', 'pjAdminWhatsappChat', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Chat', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminWhatsappChat_pjActionIndex', 'backend', 'pjAdminWhatsappChat_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Chat', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminWhatsappChat');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminWhatsappChat_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

  		
  		
COMMIT;