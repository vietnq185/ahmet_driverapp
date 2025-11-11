
START TRANSACTION;

INSERT IGNORE INTO `notifications` (`id`, `recipient`, `transport`, `variant`, `is_active`) VALUES
(2, 'admin', 'email', 'change_payment_status', 1);

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_name_sign_logo', 1, NULL, NULL, 'string', 1, 1, NULL);

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_o_name_sign_logo', 'backend', 'Label / Logo to the name sign', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Logo to the name sign', 'script');

ALTER TABLE `bookings` ADD COLUMN `google_map_link` text DEFAULT NULL;

ALTER TABLE `bookings` ADD COLUMN `driver_payment_status` smallint(5) DEFAULT NULL;

ALTER TABLE `bookings` ADD COLUMN `admin_confirm_cancelled` tinyint(1) DEFAULT '0';

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_payment_status_ARRAY_1', 'arrays', '_driver_payment_status_ARRAY_1', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_payment_status_ARRAY_2', 'arrays', '_driver_payment_status_ARRAY_2', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_payment_status_ARRAY_3', 'arrays', '_driver_payment_status_ARRAY_3', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer wants to pay on return transfer', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_payment_status_ARRAY_4', 'arrays', '_driver_payment_status_ARRAY_4', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Not paid', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_payment_status_ARRAY_5', 'arrays', '_driver_payment_status_ARRAY_5', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer paid total price (%s) in cash', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_payment_status_ARRAY_6', 'arrays', '_driver_payment_status_ARRAY_6', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer paid total price (%s) by credit card', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminOptions', 'backend', 'pjAdminOptions', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Options Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionIndex', 'backend', 'pjAdminOptions_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminOptions_pjActionNotifications', 'backend', 'pjAdminOptions_pjActionNotifications', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notifications', 'script');

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminOptions');
SET @level_1_id := (SELECT LAST_INSERT_ID());

	INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminOptions_pjActionIndex');
  	INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminOptions_pjActionNotifications');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoDriverScheduleTitle', 'backend', 'Info / Schedule title', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Scheduled Transfers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoDriverScheduleBody', 'backend', 'Info / Schedule body', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below is a list of all orders for the selected day.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblScheduleDate', 'backend', 'Label / Date', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblScheduleVehicle', 'backend', 'Label / Vehicle', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTransfersDetails', 'backend', 'Label / Transfers Details', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Transfers Details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReferenceId', 'backend', 'Label / Reference ID', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Reference ID', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblFlightArrivalTime', 'backend', 'Label / Flight arrival time', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flight arrival time', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPickupTime', 'backend', 'Label / Pick-up time', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up time', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblArrivalFlightNumber', 'backend', 'Label / Arrival flight number', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Arrival flight number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAirlineName', 'backend', 'Label / Airline name', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Airline name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblFlightDeparture', 'backend', 'Label / Flight departure', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flight departure', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPickupLocation', 'backend', 'Label / Pick-up location', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up location', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDropoffLocation', 'backend', 'Label / Drop-off location', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drop-off location', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnOpenGoogleMaps', 'backend', 'Button / Open in Google Maps', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Open in Google Maps', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPhoneNumber', 'backend', 'Label / Phone number', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblCountry', 'backend', 'Label / Country', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Country', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnOpenNameSign', 'backend', 'Button / Open Name Sign', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Open Name Sign', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_extras_info_ARRAY_2', 'arrays', '_extras_info_ARRAY_2', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '0-1 Year', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_extras_info_ARRAY_3', 'arrays', '_extras_info_ARRAY_3', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '1-5 Years', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_extras_info_ARRAY_4', 'arrays', '_extras_info_ARRAY_4', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', '5-12 Years', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblFurtherInformation', 'backend', 'Label / Further Information or Requests', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Further Information or Requests', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblPayment', 'backend', 'Label / Payment', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Payment', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuGeneralOptions', 'backend', 'Menu / General', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoGeneralOptionsTitle', 'backend', 'Info / General options title', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General Options', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoGeneralOptionsDesc', 'backend', 'Info / General options body', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below is general options that will be used in the system', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btn_select_image', 'backend', 'Button / Select image', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select image', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btn_change_image', 'backend', 'Button / Change image', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Change image', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO06', 'arrays', 'error_bodies_ARRAY_AO06', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All the changes made to general options have been saved.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO06', 'arrays', 'error_titles_ARRAY_AO06', 'script', '2013-10-07 11:42:21');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'General options updated!', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btn_delete_logo', 'backend', 'Button / Delete logo', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete logo', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'logo_image_dtitle', 'backend', 'Info / Delete logo title', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete logo', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'logo_image_dbody', 'backend', 'Info / Delete logo body', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete this logo?', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_name_sign_ARRAY_1', 'arrays', '_driver_name_sign_ARRAY_1', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Missing parameter.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_driver_name_sign_ARRAY_2', 'arrays', '_driver_name_sign_ARRAY_2', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Hash did not match.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_admin_email_change_payment_status', 'arrays', 'Notifications / Change payment status (title)', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Change payment status sent to Admin', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_admin_email_change_payment_status', 'arrays', 'Notifications / Change payment status (sub-title)', 'script', '2018-05-31 07:02:47');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This message is used to send Email to Admin when driver changes payment status.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_admin_email_change_payment_status', 'arrays', 'Notifications / Change payment status', 'script', '2018-05-31 06:19:54');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Change payment status Email', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_o_email_change_payment_status_body_text', 'backend', 'Options / Email body text', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
(NULL, @id, 'pjField', '::LOCALE::', 'title', '<div class="col-xs-6">
<div><small>{DriverName}</small></div>
<div><small>{CustomerName}</small></div>
</div>
<div class="col-xs-6">
<div><small>{Date}</small></div>
<div><small>{PaymentStatus}</small></div>
<div><small>{ReferenceID}</small></div>
 </div>
', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblBookingStatus', 'backend', 'Label / Booking status', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking status', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTransferType', 'backend', 'Label / Transfer type', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Transfer type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_transfer_types_ARRAY_1', 'arrays', '_transfer_types_ARRAY_1', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'One way', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_transfer_types_ARRAY_2', 'arrays', '_transfer_types_ARRAY_2', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer booked round-trip', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblNotAssigned', 'backend', 'Label / Not assigned', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Not assigned', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderNewArrivalTime', 'backend', 'Label / New arrival time', 'script', '2013-10-07 11:42:11');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New arrival time', 'script');


COMMIT;