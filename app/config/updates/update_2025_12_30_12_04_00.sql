
START TRANSACTION;


DROP TABLE IF EXISTS `vehicle_maintrance`;
CREATE TABLE IF NOT EXISTS `vehicle_maintrance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int(10) unsigned DEFAULT NULL,
  `make` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `model_year` varchar(255) DEFAULT NULL,
  `vin` varchar(255) DEFAULT NULL,
  `net_price` decimal(9,2) unsigned DEFAULT NULL,
  `buy_date` date DEFAULT NULL,
  `buyed_in_km` decimal(9,2) unsigned DEFAULT NULL,
  `tuv` varchar(255) DEFAULT NULL,
  `internet` varchar(255) DEFAULT NULL,
  `toll_brenner` varchar(255) DEFAULT NULL,
  `austria_vignette` varchar(255) DEFAULT NULL,
  `gps` varchar(255) DEFAULT NULL,
  `toll_arlberg` varchar(255) DEFAULT NULL,
  `telepass` varchar(255) DEFAULT NULL,
  `swiss_vignette` varchar(255) DEFAULT NULL,
  `notes` text,
  `status` enum('T','F') DEFAULT 'T',
  `created` datetime DEFAULT NULL,
   PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `vehicle_maintrance_accidents`;
CREATE TABLE IF NOT EXISTS `vehicle_maintrance_accidents` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`foreign_id` int(10) unsigned DEFAULT NULL,
	`tmp_hash` varchar(32) DEFAULT NULL,
	`date` date DEFAULT NULL,
	`time` time DEFAULT NULL,
	`driver_name` varchar(255) DEFAULT NULL,
	`location_accident` varchar(255) DEFAULT NULL,
	`instance_number` varchar(255) DEFAULT NULL,
	`is_second_vehicle_involved` tinyint(1) DEFAULT '0',
	`second_driver_name` varchar(255) DEFAULT NULL,
	`second_licence_plate_number` varchar(255) DEFAULT NULL,
	`second_instance_number` varchar(255) DEFAULT NULL,
	`notes` text,
	`created` datetime DEFAULT NULL,
	 PRIMARY KEY (`id`),
	 KEY `foreign_id` (`foreign_id`)
);

DROP TABLE IF EXISTS `vehicle_maintrance_services`;
CREATE TABLE IF NOT EXISTS `vehicle_maintrance_services` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`foreign_id` int(10) unsigned DEFAULT NULL,
	`tmp_hash` varchar(32) DEFAULT NULL,
	`service_type_id` int(10) unsigned DEFAULT NULL,
	`km` decimal(9,2) unsigned DEFAULT NULL,
	`date` date DEFAULT NULL,
	`cost` decimal(9,2) unsigned DEFAULT NULL,
	`service_station` varchar(255) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	 PRIMARY KEY (`id`),
	 KEY `foreign_id` (`foreign_id`),
	 KEY `service_type_id` (`service_type_id`)
);

DROP TABLE IF EXISTS `vehicle_maintrance_service_types`;
CREATE TABLE IF NOT EXISTS `vehicle_maintrance_service_types` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` enum('T','F') DEFAULT 'T',
	`created` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `vehicle_maintrance_attribute_types`;
CREATE TABLE IF NOT EXISTS `vehicle_maintrance_attribute_types` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`status` enum('T','F') DEFAULT 'T',
	`created` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `vehicle_maintrance_attributes`;
CREATE TABLE IF NOT EXISTS `vehicle_maintrance_attributes` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`foreign_id` int(10) unsigned DEFAULT NULL,
	`attribute_type_id` int(10) unsigned DEFAULT NULL,
	`content` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `foreign_id` (`foreign_id`),
	KEY `attribute_type_id` (`attribute_type_id`)
);

DROP TABLE IF EXISTS `vehicles_maintrance_files`;
CREATE TABLE IF NOT EXISTS `vehicles_maintrance_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tmp_hash` varchar(32) DEFAULT NULL,
  `foreign_id` int(10) unsigned DEFAULT NULL,
  `vehicle_id` int(10) unsigned DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `small_path` varchar(255) DEFAULT NULL,
  `source_path` varchar(255) DEFAULT NULL,
  `type` enum('photo','document','accident','service_invoice') DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tmp_hash` (`tmp_hash`),
  KEY `foreign_id` (`foreign_id`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `plugin_base_fields` VALUES (NULL, 'MenuMaintrainceVehicles', 'backend', 'Menu / Vehicles', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'MenuServiceTypes', 'backend', 'Menu / Service Types', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service Types', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'MenuAttributeTypes', 'backend', 'Menu / Attribute Types', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute Types', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'MenuMaintrainceReport', 'backend', 'Menu / Maintrance Report', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maintrance Report', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'MenuVehiclesMaintraince', 'backend', 'Menu / Vehicle Maintrance', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle Maintrance', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddVehiclesMaintranceTitle', 'backend', 'Info / Add Maintrance', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Maintrance', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddVehiclesMaintranceBody', 'backend', 'Info / Add vehicles maintrance', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Use the form below to add vehicle maintenance, services, accidents, and track operational costs.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateVehiclesMaintranceTitle', 'backend', 'Info / Update Vehicle Maintenance Record Title', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update Vehicle Maintenance Record', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateVehiclesMaintranceBody', 'backend', 'Info / Update Vehicle Maintenance Record Body', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Modify maintenance details, service history, and technical documentation for this vehicle. Ensure all information is accurate to maintain optimal fleet performance.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehiclesMaintranceTitle', 'backend', 'Info / Vehicles Maintrance title', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles Maintrance', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehiclesMaintranceBody', 'backend', 'Info / Vehicles Maintrance body', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Manage vehicle service history, upload technical documents, and track accident records.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblNoPhotos', 'backend', 'Label / No photos', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No photos', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblNoDocuments', 'backend', 'Label / No documents', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No documents', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddVehiclePhotos', 'backend', 'Label / Add Vehicle Photos', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Vehicle Photos', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddVehicleDocuments', 'backend', 'Label / Add Vehicle Documents', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Vehicle Documents', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AVM01', 'arrays', 'error_titles_ARRAY_AVM01', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Record Added Successfully', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AVM01', 'arrays', 'error_bodies_ARRAY_AVM01', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The maintenance record and its associated files have been saved to the database.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AVM04', 'arrays', 'error_titles_ARRAY_AVM04', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Failed to Add Record', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AVM04', 'arrays', 'error_bodies_ARRAY_AVM04', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'An error occurred while saving the data. Please check and try again.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AVM03', 'arrays', 'error_titles_ARRAY_AVM03', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Changes Saved', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AVM03', 'arrays', 'error_bodies_ARRAY_AVM03', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'The vehicle information has been updated successfully.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AVM08', 'arrays', 'error_titles_ARRAY_AVM08', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle Not Found', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AVM08', 'arrays', 'error_bodies_ARRAY_AVM08', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'We couldn''t find the record you were looking for. It might have been deleted or the ID is incorrect.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddVehicleMaintrance', 'backend', 'Label / Add vehicle maintrance', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add vehicle maintrance', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblLastService', 'backend', 'Label / Last service', 'script', '2013-09-18 13:30:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last service', 'script');



INSERT INTO `fields` VALUES (NULL, 'menuVehicleMaintranceServiceTypes', 'backend', 'Label / Service Types', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service Types', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehicleMaintranceServiceTypesTitle', 'backend', 'Infobox / Service Types Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service Types List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehicleMaintranceServiceTypesBody', 'backend', 'Infobox / Service Types Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all the service types that you operate.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblServiceTypeName', 'backend', 'Label / Name', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'statuses_ARRAY_T', 'arrays', 'statuses_ARRAY_T', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Active', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'statuses_ARRAY_F', 'arrays', 'statuses_ARRAY_F', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Inactive', 'script');
  			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddVehicleMaintranceServiceTypeTitle', 'backend', 'Infobox / Add Service Type Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new service type', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddVehicleMaintranceServiceTypeBody', 'backend', 'Infobox / Add Service Type Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add a new service type.', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateVehicleMaintranceServiceTypeTitle', 'backend', 'Infobox / Update Service Type Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update service type', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateVehicleMaintranceServiceTypeBody', 'backend', 'Infobox / Update Service Type Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update your service type.', 'script');
  		
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceSelectVehicle', 'backend', 'Label / Select a Vehicle from own Vehicle from Schedule', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select a Vehicle from own Vehicle from Schedule', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceMake', 'backend', 'Label / Marke', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Marke', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceModel', 'backend', 'Label / Model', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Model', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceMadeYear', 'backend', 'Label / Made year', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Made year', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceVehicleVIN', 'backend', 'Label / Vehicle VIN', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle VIN', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceNetPrice', 'backend', 'Label / Price netto', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Price netto', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceBuyDate', 'backend', 'Label / Buy Date / since in our fleet', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Buy Date / since in our fleet', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceBuyedInKm', 'backend', 'Label / Buyed in KM', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Buyed in KM', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMaintranceTuv', 'backend', 'Label / Tuv', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tuv', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoDeleteFileTitle', 'backend', 'Label / Delete file title', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete file', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoDeleteFileBody', 'backend', 'Label / Delete file body', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete this file?', 'script');
  		
  		
  		
  
INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminVehicleMaintranceServiceTypes');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminVehicleMaintranceServiceTypes_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicleMaintranceServiceTypes_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicleMaintranceServiceTypes_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicleMaintranceServiceTypes_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicleMaintranceServiceTypes_pjActionDeleteBulk');
					
					

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceServiceTypes', 'backend', 'pjAdminVehicleMaintranceServiceTypes', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service Types Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceServiceTypes_pjActionIndex', 'backend', 'pjAdminVehicleMaintranceServiceTypes_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service Types List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceServiceTypes_pjActionCreate', 'backend', 'pjAdminVehicleMaintranceServiceTypes_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add service type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceServiceTypes_pjActionUpdate', 'backend', 'pjAdminVehicleMaintranceServiceTypes_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update service type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceServiceTypes_pjActionDelete', 'backend', 'pjAdminVehicleMaintranceServiceTypes_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single service type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceServiceTypes_pjActionDeleteBulk', 'backend', 'pjAdminVehicleMaintranceServiceTypes_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple service types', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblInternet', 'backend', 'Label / Internet', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Internet', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblGPS', 'backend', 'Label / GPS', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'GPS', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTelepass', 'backend', 'Label / Telepass', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Telepass', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTollBrenner', 'backend', 'Label / Toll Brenner', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Toll Brenner', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTollArlberg', 'backend', 'Label / Toll Arlberg', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Toll Arlberg', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblSwissVignette', 'backend', 'Label / Swiss Vignette', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Swiss Vignette', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAustriaVignette', 'backend', 'Label / Austria Vignette', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Austria Vignette', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblNotes', 'backend', 'Label / Notes', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddField', 'backend', 'Label / Add Field', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Field', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddAccident', 'backend', 'Label / Add Accident', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Accident', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddService', 'backend', 'Label / Add Service', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Service', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblRecentAccidents', 'backend', 'Label / Recent Accidents', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Recent Accidents', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentDate', 'backend', 'Label / Date', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentDriver', 'backend', 'Label / Driver', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentLocation', 'backend', 'Label / Location', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Location', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentAction', 'backend', 'Label / Action', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Action', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblRecentAccidentsEmpty', 'backend', 'Label / Recent Accidents Empty', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No accidents reported', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblRecentServices', 'backend', 'Label / Recent Services', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Recent Services', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblRecentServicesEmpty', 'backend', 'Label / Recent Services Empty', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'None scheduled', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblServiceType', 'backend', 'Label / Type', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblServiceKm', 'backend', 'Label / Km', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Km', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblServiceCost', 'backend', 'Label / Cost', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cost', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblServiceAction', 'backend', 'Label / Action', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Action', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOtherNewAttribute', 'backend', 'Label / Other (New attribute)...', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Other (New attribute)...', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnRemove', 'backend', 'Label / Remove', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAddAccident', 'backend', 'Label / Add accident', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add accident', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentDriverName', 'backend', 'Label / Driver name', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Driver name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentTime', 'backend', 'Label / Time', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Time', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentLocationAccident', 'backend', 'Label / Location of the Accident', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Location of the Accident', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentInstanceNumber', 'backend', 'Label / Instance number', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Instance number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentASecondVehicleInvolved', 'backend', 'Label / A second vehicle was involved.', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'A second vehicle was involved.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentLicencePlateNumber', 'backend', 'Label / Licence plate number', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Licence plate number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentInstanceCompanyNumber', 'backend', 'Label / Instance company and number', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Instance company and number', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddPhotosProtocol', 'backend', 'Label / Add Photos & Protocol', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Photos & Protocol', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoDeleteRecordTitle', 'backend', 'Label / Delete record title', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete Confirmation', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoDeleteRecordBody', 'backend', 'Label / Delete record body', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete this record?', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAccidentNotes', 'backend', 'Label / Notes', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnEdit', 'backend', 'Label / Edit', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Edit', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAddService', 'backend', 'Label / Add Service', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add Service', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblSelectServiceType', 'backend', 'Label / Select service type', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select service type', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOelService', 'backend', 'Label / Oel Service', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Oel Service', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblServiceDate', 'backend', 'Label / Date', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblServiceStation', 'backend', 'Label / Service Station', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service Station', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnAddServiceInvoice', 'backend', 'Label / Add invoice', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add invoice', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehiclesMaintenanceReportTitle', 'backend', 'Info / Vehicles Maintenance Report Title', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles Maintenance Report', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehiclesMaintenanceReportBody', 'backend', 'Info / Vehicles Maintenance Report Body', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Generate comprehensive service reports for your fleet. Filter by specific vehicle and date range to track total maintenance costs, service history, and mileage logs.', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblSelectVehicle', 'backend', 'Label / Select Vehicle', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select Vehicle', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblChooseVehicle', 'backend', 'Label / Choose a vehicle', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose a vehicle', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportFromDate', 'backend', 'Label / From Date', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From Date', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportToDate', 'backend', 'Label / To Date', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To Date', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnShowReport', 'backend', 'Label / Show Report', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Show Report', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTotalServiceCost', 'backend', 'Label / Total Service Cost for this Vehicle', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Total Service Cost for this Vehicle', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportServiceDate', 'backend', 'Label / Service Date', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service Date', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportServiceType', 'backend', 'Label / Service Type', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Service Type', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportServiceKilometers', 'backend', 'Label / Kilometers', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Kilometers', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportServiceStation', 'backend', 'Label / Station', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Station', 'script');
  		
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportCost', 'backend', 'Label / Cost', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cost', 'script');
  	
INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportEmpty', 'backend', 'Label / No data selected.', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'No data selected.', 'script');
  		
  		
  		

INSERT INTO `fields` VALUES (NULL, 'menuVehicleMaintranceAttributeTypes', 'backend', 'Label / Attribute Types', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute Types', 'script');
  
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehicleMaintranceAttributeTypesTitle', 'backend', 'Infobox / Attribute Types Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute Types List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehicleMaintranceAttributeTypesBody', 'backend', 'Infobox / Attribute Types Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all the attribute types that you operate.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAttributeTypeName', 'backend', 'Label / Name', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddVehicleMaintranceAttributeTypeTitle', 'backend', 'Infobox / Add Attribute Type Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new attribute type', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddVehicleMaintranceAttributeTypeBody', 'backend', 'Infobox / Add Attribute Type Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add a new attribute type.', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateVehicleMaintranceAttributeTypeTitle', 'backend', 'Infobox / Update Attribute Type Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update attribute type', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateVehicleMaintranceAttributeTypeBody', 'backend', 'Infobox / Update Attribute Type Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update your attribute type.', 'script');
  		
  		
  
INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminVehicleMaintranceAttributeTypes');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminVehicleMaintranceAttributeTypes_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicleMaintranceAttributeTypes_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicleMaintranceAttributeTypes_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicleMaintranceAttributeTypes_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicleMaintranceAttributeTypes_pjActionDeleteBulk');
					
					

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceAttributeTypes', 'backend', 'pjAdminVehicleMaintranceAttributeTypes', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute Types Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceAttributeTypes_pjActionIndex', 'backend', 'pjAdminVehicleMaintranceAttributeTypes_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Attribute Types List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceAttributeTypes_pjActionCreate', 'backend', 'pjAdminVehicleMaintranceAttributeTypes_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add attribute type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceAttributeTypes_pjActionUpdate', 'backend', 'pjAdminVehicleMaintranceAttributeTypes_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update attribute type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceAttributeTypes_pjActionDelete', 'backend', 'pjAdminVehicleMaintranceAttributeTypes_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single attribute type', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceAttributeTypes_pjActionDeleteBulk', 'backend', 'pjAdminVehicleMaintranceAttributeTypes_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple attribute types', 'script');
  		

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminVehiclesMaintrance');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminVehiclesMaintrance_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehiclesMaintrance_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehiclesMaintrance_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehiclesMaintrance_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehiclesMaintrance_pjActionDeleteBulk');
					
					

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehiclesMaintrance', 'backend', 'pjAdminVehiclesMaintrance', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles Maintrance Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehiclesMaintrance_pjActionIndex', 'backend', 'pjAdminVehiclesMaintrance_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles Maintrance List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehiclesMaintrance_pjActionCreate', 'backend', 'pjAdminVehiclesMaintrance_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add vehicle maintrance', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehiclesMaintrance_pjActionUpdate', 'backend', 'pjAdminVehiclesMaintrance_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update vehicle maintrance', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehiclesMaintrance_pjActionDelete', 'backend', 'pjAdminVehiclesMaintrance_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single vehicle maintrance', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehiclesMaintrance_pjActionDeleteBulk', 'backend', 'pjAdminVehiclesMaintrance_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple vehicles maintrance', 'script');
  		
  		
INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminVehicleMaintranceReport');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminVehicleMaintranceReport_pjActionIndex');
					
					

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceReport', 'backend', 'pjAdminVehicleMaintranceReport', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Maintrance Report Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicleMaintranceReport_pjActionIndex', 'backend', 'pjAdminVehicleMaintranceReport_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle Maintrance Report', 'script');
  		
  		
  		
COMMIT;