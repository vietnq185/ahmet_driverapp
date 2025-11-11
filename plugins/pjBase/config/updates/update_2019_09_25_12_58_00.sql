
START TRANSACTION;

INSERT INTO `plugin_base_cron_jobs` (`name`, `controller`, `action`, `interval`, `period`, `is_active`) VALUES
('Synchronize data from all providers.', 'pjCron', 'pjActionSyncData', 1, 'week', 1);

INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'plugin_sms_message_bird_access_key', 99, NULL, NULL, 'string', NULL, 0, 'string'),
(1, 'plugin_sms_message_bird_originator', 99, NULL, NULL, 'string', NULL, 0, 'string');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_plugin_sms_message_bird_access_key', 'backend', 'Label / Message bird originator', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message bird originator', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_plugin_sms_message_bird_originator', 'backend', 'Label / Message bird access key', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message bird access key', 'script');


COMMIT;