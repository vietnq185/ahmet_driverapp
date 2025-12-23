
START TRANSACTION;



INSERT INTO `plugin_base_options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_customer_tracking_url', 1, '', '', 'string', 16, 1, NULL);


INSERT INTO `fields` VALUES (NULL, 'plugin_base_opt_o_customer_tracking_url', 'backend', 'Options / Customer tracking page', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'URL to the webpage where the customer tracking page is installed', 'script');


SET @id := (SELECT `id` FROM `fields` WHERE `key`='infoAddUpdateWhatsappMessageToken');
UPDATE `multi_lang` SET `content`='<div>Available tokens:</div><div class="row"><div class="col-xs-6">{DriverName}<br/>{CustomerName}<br/>{Date}</div><div class="col-xs-6">{PaymentStatus}<br/>{ReferenceID}<br/>{URLTracking}</div></div>' WHERE `foreign_id`=@id AND `model`='pjField' AND `field`='title';


INSERT INTO `fields` VALUES (NULL, 'lblTrackingSortName', 'backend', 'Label / Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblTrackingSortSpeed', 'backend', 'Label / Speed', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Speed', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_your_vehicle', 'backend', 'Label / Vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_scheduled_start_time', 'backend', 'Label / Scheduled Start Time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Scheduled Start Time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_scheduled_end_time', 'backend', 'Label / Scheduled End Time', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Scheduled End Time', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_tracking_statuses_ARRAY_1', 'arrays', 'front_tracking_statuses_ARRAY_1', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking not found!', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_tracking_statuses_ARRAY_2', 'arrays', 'front_tracking_statuses_ARRAY_2', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'This booking has not been assigned a vehicle yet. Don''t worry, your vehicle will arrive soon.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_tracking_statuses_ARRAY_3', 'arrays', 'front_tracking_statuses_ARRAY_3', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your trip will start at %s', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_tracking_statuses_ARRAY_5', 'arrays', 'front_tracking_statuses_ARRAY_5', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Your trip has ended.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_scheduled_speed', 'backend', 'Label / Speed', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Speed', 'script');

INSERT INTO `fields` VALUES (NULL, 'menuInstall', 'backend', 'Label / Install', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install', 'script');

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminOptions_pjActionInstall');
SET @level_1_id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @level_1_id, 'pjAuthPermission', '::LOCALE::', 'title', 'Integration Code Menu', 'data');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionInstall', 'backend', 'Label / Integration Code Menu', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Integration Code Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInstallJs1_title', 'backend', 'Install / Title', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install instructions', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInstallJs1_body', 'backend', 'Install / Body', 'script', '2013-09-18 13:30:14');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'In order to install the tracking script on your website copy the code below and add it to your web page.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInstallJs1_1', 'backend', 'Install / Step 1', 'script', '2013-09-18 13:30:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Install Code', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInstallLanguageConfig', 'backend', 'Install / Language configuration', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Language configuration', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInstallConfigLocale', 'backend', 'Install / Language', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Language', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInstallCodeStep1', 'backend', 'Install / Step 1', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 1. (Required) Copy the code below and put it in the HEAD tag of your web page.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInstallCodeStep2', 'backend', 'Install / Step 2', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 2. (Required) Copy the code below and put it in your web page where you want the tracking script to appear.', 'script');


COMMIT;