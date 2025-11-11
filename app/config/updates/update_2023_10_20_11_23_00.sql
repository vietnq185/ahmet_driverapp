START TRANSACTION;

ALTER TABLE `bookings` MODIFY `uuid` varchar(255) DEFAULT NULL;
ALTER TABLE `bookings` ADD COLUMN `notes_from_driver` text;
ALTER TABLE `bookings` ADD COLUMN `notes_from_office` text;

ALTER TABLE `plugin_auth_users` ADD COLUMN (
	`type_of_driver` enum('own','partner') DEFAULT 'own',
	`general_info_for_driver` text
);

ALTER TABLE `vehicles` ADD COLUMN (
	`type` enum('own','partner') DEFAULT 'own',
	`maker_modell` varchar(255) DEFAULT NULL,
	`vin` varchar(255) DEFAULT NULL,
	`model_year` varchar(255) DEFAULT NULL,
	`tuv` date DEFAULT NULL
);

DROP TABLE IF EXISTS `vehicles_services`;
CREATE TABLE IF NOT EXISTS `vehicles_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int(10) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `km` decimal(15,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `drivers_popup`;
CREATE TABLE IF NOT EXISTS `drivers_popup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` int(10) unsigned DEFAULT NULL,
  `message` text,
  `is_displayed` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `driver_id` (`driver_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnLock', 'backend', 'Button / Lock', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Lock', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnUnlock', 'backend', 'Button / Unlock', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Unlock', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderID', 'backend', 'Label / Order ID', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order ID', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderFlight', 'backend', 'Label / Flight', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flight', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderNotesFromDriver', 'backend', 'Label / Notes from driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes from driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddNotesForDriver', 'backend', 'Button / Add notes for driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add notes for driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderNotesForDriver', 'backend', 'Label / Notes for driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes for driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderImportantNotesFromOffice', 'backend', 'Label / Important notes from office', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Important notes from office', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverType', 'backend', 'Label / Type of driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type of driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverGeneralInfo', 'backend', 'Label / General information for the driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General information for the driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_types_of_drive_ARRAY_own', 'arrays', '_types_of_drive_ARRAY_own', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Own driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_types_of_drive_ARRAY_partner', 'arrays', '_types_of_drive_ARRAY_partner', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partner driver', 'script');



INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleType', 'backend', 'Label / Type', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleMarkerModell', 'backend', 'Label / Marker / Modell', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Marker / Modell', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleVin', 'backend', 'Label / Vin', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vin', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleModelYear', 'backend', 'Label / Model year', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Model year', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleTuv', 'backend', 'Label / Tuv', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tuv', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddServiceRepair', 'backend', 'Label / Add service/repair', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add service/repair', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleServiceDate', 'backend', 'Label / Date', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleServiceKm', 'backend', 'Label / Km', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Km', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleServiceRepair', 'backend', 'Label / Service/repair', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service/repair', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddServiceRepair', 'backend', 'Info / Add service/repair', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add service/repair', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateServiceRepair', 'backend', 'Info / Update service/repair', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update service/repair', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_vehicle_types_ARRAY_own', 'arrays', '_vehicle_types_ARRAY_own', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Owner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_vehicle_types_ARRAY_partner', 'arrays', '_vehicle_types_ARRAY_partner', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Partner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportFilterByOwnVehicles', 'backend', 'Label / Own vehicles', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Own vehicles', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_paid', 'backend', 'Label / Paid', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_creditcard', 'backend', 'Label / Credit card', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_cash', 'backend', 'Label / Cash', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_date', 'backend', 'Label / Date', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_from_to', 'backend', 'Label / From - To', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From - To', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_total_bookings_today', 'backend', 'Label / Total bookings today', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total bookings today', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_total_bookings_tomorrow', 'backend', 'Label / Total bookings tomorrow', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total bookings tomorrow', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_total_amount_today', 'backend', 'Label / Total amount today', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total amount today', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_total_bookings_own_vehicles', 'backend', 'Label / Total bookings own vehicles', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total bookings own vehicles', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_total_bookings_partner_vehicles', 'backend', 'Label / Total bookings partner vehicles', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total bookings partner vehicles', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_paid', 'backend', 'Label / Paid', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_credit_card', 'backend', 'Label / Credit card', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_cash', 'backend', 'Label / Cash', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_sms', 'backend', 'Label / SMS', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_popup', 'backend', 'Label / Pop Up', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pop Up', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_choose_driver', 'backend', 'Label / Choose a driver', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose a driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_sms_own_drivers_today', 'backend', 'Label / SMS send to own drivers today', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'all our OWN drivers which are assigned to a vehicle for today', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_sms_own_drivers_tomorrow', 'backend', 'Label / SMS send to own drivers tomorrow', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'all our OWN drivers which are assigned to a vehicle for tomorrow', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_message', 'backend', 'Label / Message', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_send_sms', 'backend', 'Label / Send SMS', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send SMS', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_send_popup', 'backend', 'Label / Send Pop Up', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send Pop Up', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_sms_sent_success', 'backend', 'Label / SMS has been sent.', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS has been sent.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_sms_sent_failed', 'backend', 'Label / SMS failed to send.', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'SMS failed to send.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_popup_sent_success', 'backend', 'Label / Pop Up has been sent.', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pop Up has been sent.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_popup_sent_failed', 'backend', 'Label / Pop Up failed to send.', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pop Up failed to send.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_tab_upcoming', 'backend', 'Label / Upcoming bookings', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Upcoming bookings', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'dash_transfers', 'backend', 'Label / Transfers', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Transfers', 'script');




INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdmin_pjActionIndex');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdmin_pjActionIndex', 'backend', 'pjAdmin_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Dashboard', 'script');


COMMIT;