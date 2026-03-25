
START TRANSACTION;


DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,           
  `booking_id` varchar(255) DEFAULT NULL,                                          
  `user_id` int(10) unsigned DEFAULT NULL,    
  `action` text,
  `created` datetime DEFAULT NULL,                                         
  PRIMARY KEY (`id`),                                                                   
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_enable_send_whatsapp_button_on_schedule_page', 1, '1|0::0', NULL, 'bool', 30, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_enable_send_whatsapp_button_on_schedule_page', 'backend', 'Options / Show button "Send WhatsApp" on Schedule page', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Show button "Send WhatsApp" on Schedule page', 'script');
	
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblLogContent', 'backend', 'Label / Content', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Content', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblLogBy', 'backend', 'Label / By', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'By', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblLogCreated', 'backend', 'Label / Created', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Created', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuLogs', 'backend', 'Menu Logs', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logs', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoLogsTitle', 'backend', 'Infobox / Logs Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logs List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoLogsBody', 'backend', 'Infobox / Logs Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view all the logs.', 'script');
	

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminLogs', 'backend', 'pjAdminLogs', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logs Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminLogs_pjActionIndex', 'backend', 'pjAdminLogs_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logs List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminLogs_pjActionDelete', 'backend', 'pjAdminLogs_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single log', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminLogs_pjActionDeleteBulk', 'backend', 'pjAdminLogs_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple logs', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminLogs');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminLogs_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminLogs_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminLogs_pjActionDeleteBulk');
		

COMMIT;