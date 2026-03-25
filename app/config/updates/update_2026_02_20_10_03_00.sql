
START TRANSACTION;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddNote', 'backend', 'Label / Add note', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add note', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminSchedule_pjActionLockAssignments', 'backend', 'pjAdminSchedule_pjActionLockAssignments', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Lock Assignments', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'driver_send_whatsapp_title', 'backend', 'Label / Send WhatsApp', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send WhatsApp', 'script');




SET @level_1_id := (SELECT `id` FROM `plugin_auth_permissions` WHERE `key`='pjAdminSchedule');
  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminSchedule_pjActionLockAssignments');


COMMIT;