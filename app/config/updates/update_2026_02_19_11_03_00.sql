
START TRANSACTION;


DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int(10) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('T','F') DEFAULT 'T',
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_driver_visibility_mode', 1, 'scheduled|manual::manual', 'Scheduled|Immediate', 'enum', 27, 1, NULL),
(1, 'o_release_days_offset', 1, '', '', 'int', 28, 1, NULL),
(1, 'o_release_time_threshold', 1, '', '', 'string', 29, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_driver_visibility_mode', 'backend', 'Options / Driver Job Visibility', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver Job Visibility', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_release_days_offset', 'backend', 'Options / Days before booking', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Days before booking', 'script');

INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_release_time_threshold', 'backend', 'Options / Release time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Release time (Example: 17:00)', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoCapacityWarningTitle', 'backend', 'Label / Capacity Warning', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Capacity Warning', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoCapacityWarningDetails', 'backend', 'Label / Capacity Warning details', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking %s: %s passengers exceeds Max Capacity (%s seats)', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuNotes', 'backend', 'Menu Notes', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoNotesTitle', 'backend', 'Infobox / Notes Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoNotesBody', 'backend', 'Infobox / Notes Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all the notes that you operate.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblNoteVehicle', 'backend', 'Label / Vehicle', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblNoteDate', 'backend', 'Label / Date', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblNoteNotes', 'backend', 'Label / Notes', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddNoteTitle', 'backend', 'Infobox / Add Note Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new note', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddNoteBody', 'backend', 'Infobox / Add Note Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add a new note. ', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateNoteTitle', 'backend', 'Infobox / Update Note Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update note', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateNoteBody', 'backend', 'Infobox / Update Note Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update your note data.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnSendWhatsapp', 'backend', 'Send WhatsApp', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send WhatsApp', 'script');




INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminNotes', 'backend', 'pjAdminNotes', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminNotes_pjActionIndex', 'backend', 'pjAdminNotes_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminNotes_pjActionCreate', 'backend', 'pjAdminNotes_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add note', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminNotes_pjActionUpdate', 'backend', 'pjAdminNotes_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update note', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminNotes_pjActionDelete', 'backend', 'pjAdminNotes_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single note', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminNotes_pjActionDeleteBulk', 'backend', 'pjAdminNotes_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple notes', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminNotes');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminNotes_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminNotes_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminNotes_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminNotes_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminNotes_pjActionDeleteBulk');
	

  		
COMMIT;