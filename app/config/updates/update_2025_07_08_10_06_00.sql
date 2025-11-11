START TRANSACTION;

DROP TABLE IF EXISTS `whatsapp_messages`;
CREATE TABLE IF NOT EXISTS `whatsapp_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` int(10) DEFAULT NULL,
  `available_for` enum('driver','admin','both') NOT NULL DEFAULT 'both', 
  `created` datetime NOT NULL,                         
  `status` enum('T','F') NOT NULL DEFAULT 'T',         
  `domain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),                                  
  KEY `status` (`status`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `fields` VALUES (NULL, 'menuWhatsappMessages', 'backend', 'Label / Whatsapp Messages', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp Messages', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoWhatsappMessagesTitle', 'backend', 'Infobox / Whatsapp Messages Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp Messages List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoWhatsappMessagesBody', 'backend', 'Infobox / Whatsapp Messages Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all the Whatsapp Messages that you operate.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblWMSubject', 'backend', 'Label / Subject', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Subject', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblWMMessage', 'backend', 'Label / Message', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblWMAvailableFor', 'backend', 'Label / Available for', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available for', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblWMStatus', 'backend', 'Label / Status', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'wm_available_for_ARRAY_driver', 'arrays', 'wm_available_for_ARRAY_driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'wm_available_for_ARRAY_admin', 'arrays', 'wm_available_for_ARRAY_admin', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'wm_available_for_ARRAY_both', 'arrays', 'wm_available_for_ARRAY_both', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Admin & Driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'wm_statuses_ARRAY_T', 'arrays', 'wm_statuses_ARRAY_T', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'wm_statuses_ARRAY_F', 'arrays', 'wm_statuses_ARRAY_F', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inactive', 'script');
  			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddWhatsappMessageTitle', 'backend', 'Infobox / Add Whatsapp Message Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new Whatsapp message', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddWhatsappMessageBody', 'backend', 'Infobox / Add Whatsapp Message Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add a new Whatsapp message.', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateWhatsappMessageTitle', 'backend', 'Infobox / Update Whatsapp Message Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update Whatsapp message', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateWhatsappMessageBody', 'backend', 'Infobox / Update Whatsapp Message Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update your Whatsapp message.', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddUpdateWhatsappMessageToken', 'backend', 'Infobox / Add/Update Whatsapp Message Token', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '<div>Available tokens:</div><div class="row"><div class="col-xs-6">{DriverName}<br/>{CustomerName}</div><div class="col-xs-6">{Date}<br/>{PaymentStatus}<br/>{ReferenceID}</div></div>', 'script');
	
  
INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminWhatsappMessages');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminWhatsappMessages_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminWhatsappMessages_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminWhatsappMessages_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminWhatsappMessages_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminWhatsappMessages_pjActionDeleteBulk');
					
					

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminWhatsappMessages', 'backend', 'pjAdminWhatsappMessages', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Whatsapp Messages Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminWhatsappMessages_pjActionIndex', 'backend', 'pjAdminWhatsappMessages_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Messages List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminWhatsappMessages_pjActionCreate', 'backend', 'pjAdminWhatsappMessages_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add message', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminWhatsappMessages_pjActionUpdate', 'backend', 'pjAdminWhatsappMessages_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update message', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminWhatsappMessages_pjActionDelete', 'backend', 'pjAdminWhatsappMessages_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single message', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminWhatsappMessages_pjActionDeleteBulk', 'backend', 'pjAdminWhatsappMessages_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple messages', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnWhatsApp', 'backend', 'Label / WhatsApp', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnSendWS', 'backend', 'Label / Send WhatsApp Message', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send WhatsApp Message', 'script');
  

COMMIT;