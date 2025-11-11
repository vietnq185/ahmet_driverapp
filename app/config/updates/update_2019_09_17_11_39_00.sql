
START TRANSACTION;

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` int(10) DEFAULT NULL,     
 `uuid` int(10) unsigned DEFAULT NULL,                                                                    
 `client_id` int(10) unsigned DEFAULT NULL,                                                               
 `driver_id` int(10) unsigned DEFAULT NULL,                                                               
 `fleet_id` int(10) unsigned DEFAULT NULL,                                                                
 `accept_shared_trip` tinyint(1) unsigned DEFAULT '0',                                                    
 `location_id` int(10) unsigned DEFAULT NULL COMMENT 'Location ID',                                       
 `dropoff_id` int(10) unsigned DEFAULT NULL COMMENT 'Location ID',                                        
 `booking_date` datetime DEFAULT NULL,                                                                    
 `return_date` datetime DEFAULT NULL,                                                                     
 `return_id` int(10) unsigned DEFAULT NULL,                                                               
 `passengers` int(5) DEFAULT NULL,                                                                        
 `luggage` int(5) DEFAULT NULL,                                                                           
 `sub_total` decimal(9,2) unsigned DEFAULT NULL,                                                          
 `discount` decimal(9,2) DEFAULT NULL,                                                                    
 `tax` decimal(9,2) unsigned DEFAULT NULL,                                                                
 `total` decimal(9,2) unsigned DEFAULT NULL,                                                              
 `deposit` decimal(9,2) unsigned DEFAULT NULL,  
 `price` decimal(9,2) DEFAULT NULL,
 `voucher_code` varchar(255) DEFAULT NULL,                                                                
 `payment_method` enum('paypal','authorize','creditcard','creditcard_later','cash','bank') DEFAULT NULL,  
 `status` enum('confirmed','cancelled','pending','passed_on') DEFAULT 'pending',                          
 `txn_id` varchar(255) DEFAULT NULL,                                                                      
 `processed_on` datetime DEFAULT NULL,                                                                    
 `created` datetime DEFAULT NULL,                                                                         
 `ip` varchar(255) DEFAULT NULL,                                                                          
 `c_title` varchar(255) DEFAULT NULL,                                                                     
 `c_fname` varchar(255) DEFAULT NULL,                                                                     
 `c_lname` varchar(255) DEFAULT NULL,                                                                     
 `c_dialing_code` varchar(55) DEFAULT NULL,                                                               
 `c_phone` varchar(255) DEFAULT NULL,                                                                     
 `c_email` varchar(255) DEFAULT NULL,                                                                     
 `c_company` varchar(255) DEFAULT NULL,                                                                   
 `c_notes` text,                                                                                          
 `c_address` varchar(255) DEFAULT NULL,                                                                   
 `c_city` varchar(255) DEFAULT NULL,                                                                      
 `c_state` varchar(255) DEFAULT NULL,                                                                     
 `c_zip` varchar(255) DEFAULT NULL,                                                                       
 `c_country` int(10) unsigned DEFAULT NULL,                                                               
 `c_airline_company` varchar(255) DEFAULT NULL,                                                           
 `c_departure_airline_company` varchar(255) DEFAULT NULL,                                                 
 `c_flight_number` varchar(255) DEFAULT NULL,                                                             
 `c_flight_time` varchar(255) DEFAULT NULL,                                                               
 `c_departure_flight_number` varchar(255) DEFAULT NULL,                                                   
 `c_departure_flight_time` varchar(255) DEFAULT NULL,                                                     
 `c_destination_address` varchar(255) DEFAULT NULL,                                                       
 `c_hotel` varchar(255) DEFAULT NULL,                                                                     
 `c_cruise_ship` varchar(255) DEFAULT NULL,                                                               
 `cc_owner` varchar(255) DEFAULT NULL,                                                                    
 `cc_type` varchar(255) DEFAULT NULL,                                                                     
 `cc_num` varchar(255) DEFAULT NULL,                                                                      
 `cc_exp` varchar(255) DEFAULT NULL,                                                                      
 `cc_code` varchar(255) DEFAULT NULL,                                                                     
 `internal_notes` text,                   
 `prev_booking_date` datetime DEFAULT NULL,
 `customized_name_plate` varchar(255) DEFAULT NULL,     
 `confirmed_time_change` tinyint(1) DEFAULT '0',
 `vehicle_id` int(10) DEFAULT '0',
 `vehicle_order` smallint(5) DEFAULT '3',
 `driver_status` smallint(5) DEFAULT '0', 
 `ref_uuid` int(10) unsigned DEFAULT NULL,
 `domain` varchar(255) DEFAULT NULL,   
 PRIMARY KEY (`id`),                                                                                      
 UNIQUE KEY `uuid` (`uuid`),                                                                              
 KEY `fleet_id` (`fleet_id`),                                                                             
 KEY `location_id` (`location_id`),                                                                       
 KEY `dropoff_id` (`dropoff_id`),
 KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `bookings_extras`;
CREATE TABLE IF NOT EXISTS `bookings_extras` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,           
  `booking_id` int(10) unsigned DEFAULT NULL,              
  `extra_id` int(10) unsigned DEFAULT NULL,                
  `quantity` int(10) unsigned DEFAULT NULL,                
  PRIMARY KEY (`id`),                                      
  UNIQUE KEY `booking_id` (`booking_id`,`extra_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `bookings_payments`;
CREATE TABLE IF NOT EXISTS `bookings_payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,           
  `booking_id` int(10) unsigned DEFAULT NULL,                                           
  `payment_method` enum('paypal','authorize','creditcard','bank','cash') DEFAULT NULL,  
  `payment_type` varchar(255) DEFAULT NULL,                                             
  `amount` decimal(9,2) unsigned DEFAULT NULL,                                          
  `status` enum('paid','notpaid') DEFAULT 'paid',                                       
  PRIMARY KEY (`id`),                                                                   
  KEY `booking_id` (`booking_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` int(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,               
    `password` blob,                                 
    `title` varchar(255) DEFAULT NULL,               
    `fname` varchar(255) DEFAULT NULL,               
    `lname` varchar(255) DEFAULT NULL,               
    `dialing_code` varchar(55) DEFAULT NULL,         
    `phone` varchar(255) DEFAULT NULL,               
    `company` varchar(255) DEFAULT NULL,             
    `address` varchar(255) DEFAULT NULL,             
    `city` varchar(255) DEFAULT NULL,                
    `state` varchar(255) DEFAULT NULL,               
    `zip` varchar(255) DEFAULT NULL,                 
    `country_id` int(10) unsigned DEFAULT NULL,      
    `created` datetime NOT NULL,                     
    `last_login` datetime DEFAULT NULL,              
    `status` enum('T','F') NOT NULL DEFAULT 'T',    
    `domain` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),                              
    KEY `status` (`status`),
    KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `drivers`;
CREATE TABLE IF NOT EXISTS `drivers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` int(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,                   
    `title` varchar(255) DEFAULT NULL,                   
    `fname` varchar(255) DEFAULT NULL,                   
    `lname` varchar(255) DEFAULT NULL,                   
    `phone` varchar(255) DEFAULT NULL,                   
    `created` datetime NOT NULL,                         
    `status` enum('T','F') NOT NULL DEFAULT 'T',         
    `domain` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),                                  
    KEY `status` (`status`),
    KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dropoff`;
CREATE TABLE IF NOT EXISTS `dropoff` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` int(10) DEFAULT NULL,
  `location_id` int(10) unsigned DEFAULT NULL,         
    `distance` int(10) DEFAULT NULL,                     
    `duration` int(10) DEFAULT NULL,                     
    `is_airport` tinyint(1) unsigned DEFAULT '0',        
    `icon` varchar(255) DEFAULT NULL,                    
    `order_index` int(10) unsigned DEFAULT NULL,   
    `domain` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),                                  
    KEY `location_id` (`location_id`),
    KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `extras`;
CREATE TABLE IF NOT EXISTS `extras` (
  	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  	`external_id` int(10) DEFAULT NULL,        
    `status` enum('T','F') DEFAULT NULL,
    `domain` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `extras_limitations`;
CREATE TABLE IF NOT EXISTS `extras_limitations` (
  	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  	`extra_id` int(10) unsigned DEFAULT NULL,                   
   `fleet_id` int(10) unsigned DEFAULT NULL,                   
   `max_qty` int(5) unsigned DEFAULT NULL,                     
   PRIMARY KEY (`id`),                                         
   UNIQUE KEY `extra_id` (`extra_id`,`fleet_id`)                
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fleets`;
CREATE TABLE IF NOT EXISTS `fleets` (
  	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  	`external_id` int(10) DEFAULT NULL,                    
   `min_passengers` int(5) unsigned DEFAULT NULL,                   
   `passengers` int(5) unsigned DEFAULT NULL,                       
   `return_discount_1` decimal(9,2) DEFAULT NULL,                   
   `return_discount_2` decimal(9,2) DEFAULT NULL,                   
   `return_discount_3` decimal(9,2) DEFAULT NULL,                   
   `return_discount_4` decimal(9,2) DEFAULT NULL,                   
   `return_discount_5` decimal(9,2) DEFAULT NULL,                   
   `return_discount_6` decimal(9,2) DEFAULT NULL,                   
   `return_discount_7` decimal(9,2) DEFAULT NULL,                   
   `luggage` int(5) unsigned DEFAULT NULL,                          
   `is_crossedout_price` tinyint(1) unsigned NOT NULL DEFAULT '0',  
   `source_path` varchar(255) DEFAULT NULL,                         
   `thumb_path` varchar(255) DEFAULT NULL,                          
   `image_name` varchar(255) DEFAULT NULL,                          
   `status` enum('T','F') NOT NULL DEFAULT 'T',                     
   `order_index` int(10) unsigned DEFAULT NULL,    
   `domain` varchar(255) DEFAULT NULL, 
   PRIMARY KEY (`id`),                                              
   KEY `status` (`status`),
   KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fleets_discounts`;
CREATE TABLE IF NOT EXISTS `fleets_discounts` (
  	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  	`fleet_id` int(10) unsigned DEFAULT NULL,                 
     `day` varchar(255) DEFAULT NULL,                          
     `type` enum('amount','percent') DEFAULT NULL,             
     `discount` decimal(9,2) unsigned DEFAULT NULL,            
     `valid` enum('always','period') DEFAULT 'always',         
     `date_from` date DEFAULT NULL,                            
     `time_from` time DEFAULT NULL,                            
     `date_to` date DEFAULT NULL,                              
     `time_to` time DEFAULT NULL,                              
     `is_subtract` enum('T','F') DEFAULT 'T',                  
     PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `fleets_discounts_periods`;
CREATE TABLE IF NOT EXISTS `fleets_discounts_periods` (
  	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  	`fleet_discount_id` int(10) unsigned NOT NULL,                    
     `date_from` date NOT NULL,                                        
     `date_to` date NOT NULL,                                          
     PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  	`external_id` int(10) DEFAULT NULL,                   
     `is_airport` tinyint(1) unsigned DEFAULT '0',        
  `status` enum('T','F') NOT NULL DEFAULT 'T',         
  `icon` varchar(255) DEFAULT NULL,                    
  `order_index` int(10) unsigned DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),                                  
  KEY `status` (`status`)    ,
  KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `prices`;
CREATE TABLE IF NOT EXISTS `prices` (
  	`fleet_id` int(5) unsigned NOT NULL,            
   `dropoff_id` int(5) unsigned NOT NULL,          
   `price_1` decimal(9,2) DEFAULT NULL,            
   `price_2` decimal(9,2) DEFAULT NULL,            
   `price_3` decimal(9,2) DEFAULT NULL,            
   `price_4` decimal(9,2) DEFAULT NULL,            
   `price_5` decimal(9,2) DEFAULT NULL,            
   `price_6` decimal(9,2) DEFAULT NULL,            
   `price_7` decimal(9,2) DEFAULT NULL,            
   PRIMARY KEY (`fleet_id`,`dropoff_id`),          
   KEY `fleet_id` (`fleet_id`),                    
   KEY `dropoff_id` (`dropoff_id`)     
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `vouchers`;
CREATE TABLE IF NOT EXISTS `vouchers` (
  	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,    
  	`external_id` int(10) DEFAULT NULL,
     `code` varchar(255) DEFAULT NULL,                                                                   
     `type` enum('amount','percent') DEFAULT NULL,                                                       
     `discount` decimal(9,2) unsigned DEFAULT NULL,                                                      
     `valid` enum('fixed','period','recurring') DEFAULT NULL,                                            
     `date_from` date DEFAULT NULL,                                                                      
     `date_to` date DEFAULT NULL,                                                                        
     `time_from` time DEFAULT NULL,                                                                      
     `time_to` time DEFAULT NULL,                                                                        
     `every` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') DEFAULT NULL,
     `domain` varchar(255) DEFAULT NULL,    
     PRIMARY KEY (`id`),                                                                                 
     KEY `code` (`code`)    ,
  	KEY `domain` (`domain`)    
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `registration_number` varchar(255) DEFAULT NULL,
  `seats` int(10) unsigned DEFAULT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `status` enum('T','F') NOT NULL DEFAULT 'T',
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration_number` (`registration_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `providers`;
CREATE TABLE IF NOT EXISTS `providers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` enum('T','F') NOT NULL DEFAULT 'T',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `drivers_vehicles`;
CREATE TABLE IF NOT EXISTS `drivers_vehicles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  
 `driver_id` int(10) DEFAULT NULL,               
 `vehicle_id` int(10) DEFAULT NULL,              
 `date` date DEFAULT NULL,                       
 `order` smallint(5) DEFAULT NULL,       
 PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `recipient` enum('driver','admin') DEFAULT NULL,
  `transport` enum('email','sms') DEFAULT NULL,
  `variant` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipient` (`recipient`,`transport`,`variant`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `notifications` (`id`, `recipient`, `transport`, `variant`, `is_active`) VALUES
(1, 'driver', 'sms', 'assign_vehicle', 1);


INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_last_update_data', 99, '', NULL, 'string', NULL, 0, NULL);

ALTER TABLE `bookings` ADD COLUMN `last_update` datetime DEFAULT NULL;

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblLastUpdate', 'backend', 'Label / Last update', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Last update', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuVehicles', 'backend', 'Menu Vehicles', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehiclesTitle', 'backend', 'Infobox / Vehicles Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoVehiclesBody', 'backend', 'Infobox / Vehicles Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all the vehicles that you operate.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'script_name', 'backend', 'Label / Script name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Shuttle Booking Software', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleName', 'backend', 'Label / Name', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleRegistrationNumber', 'backend', 'Label / Registration number', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Registration number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleSeats', 'backend', 'Label / Seats', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Seats', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblVehicleOrder', 'backend', 'Label / Order', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Order', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'vehicle_same_reg', 'backend', 'Vehicle / Same Registration number', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Registration number was existed', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicles', 'backend', 'pjAdminVehicles', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicles_pjActionIndex', 'backend', 'pjAdminVehicles_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicles List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicles_pjActionCreate', 'backend', 'pjAdminVehicles_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add vehicle', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicles_pjActionUpdate', 'backend', 'pjAdminVehicles_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update vehicle', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicles_pjActionDelete', 'backend', 'pjAdminVehicles_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single vehicle', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminVehicles_pjActionDeleteBulk', 'backend', 'pjAdminVehicles_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple vehicles', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminVehicles');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminVehicles_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicles_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicles_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicles_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminVehicles_pjActionDeleteBulk');
			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddVehicleTitle', 'backend', 'Infobox / Add Vehicle Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new vehicle', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddVehicleBody', 'backend', 'Infobox / Add Vehicle Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add a new vehicle. ', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateVehicleTitle', 'backend', 'Infobox / Update Vehicle Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update vehicle', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateVehicleBody', 'backend', 'Infobox / Update Vehicle Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update your vehicle data.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'delete_selected', 'backend', 'Grid / Delete selected', 'script', '2013-09-16 14:10:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete selected', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'delete_confirmation', 'backend', 'Grid / Confirmation Title', 'script', '2013-09-16 14:09:36');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete selected records?', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuDrivers', 'backend', 'Menu Drivers', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drivers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoDriversTitle', 'backend', 'Infobox / Drivers Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drivers List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoDriversBody', 'backend', 'Infobox / Drivers Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all the drivers that you operate.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverName', 'backend', 'Label / Name', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverEmail', 'backend', 'Label / Email', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverPhone', 'backend', 'Label / Phone number', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone number', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverPassword', 'backend', 'Label / Password', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Password', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDriverLanguage', 'backend', 'Label / Language', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Language', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'driver_same_phone', 'backend', 'Driver / Same Phone number', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Phone number was existed', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminDrivers', 'backend', 'pjAdminDrivers', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drivers Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminDrivers_pjActionIndex', 'backend', 'pjAdminDrivers_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drivers List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminDrivers_pjActionCreate', 'backend', 'pjAdminDrivers_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminDrivers_pjActionUpdate', 'backend', 'pjAdminDrivers_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminDrivers_pjActionDelete', 'backend', 'pjAdminDrivers_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminDrivers_pjActionDeleteBulk', 'backend', 'pjAdminDrivers_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple drivers', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminDrivers');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminDrivers_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminDrivers_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminDrivers_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminDrivers_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminDrivers_pjActionDeleteBulk');
			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddDriverTitle', 'backend', 'Infobox / Add Driver Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new driver', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddDriverBody', 'backend', 'Infobox / Add Driver Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add a new driver. ', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateDriverTitle', 'backend', 'Infobox / Update Driver Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update driver', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateDriverBody', 'backend', 'Infobox / Update Driver Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update your driver data.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'sb_email_taken', 'backend', 'Users / Email already taken', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'User with this email address already exists.', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuProviders', 'backend', 'Menu Providers', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Providers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoProvidersTitle', 'backend', 'Infobox / Providers Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Providers List', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoProvidersBody', 'backend', 'Infobox / Providers Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can view and manage all providers that using Shuttle Booking Software.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblProviderName', 'backend', 'Label / Name', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Name', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblProviderURL', 'backend', 'Label / URL', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'URL', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblProviderURLDesc', 'backend', 'Label / URL description', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Example: http://www.domain.com/folder', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminProviders', 'backend', 'pjAdminProviders', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Providers Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminProviders_pjActionIndex', 'backend', 'pjAdminProviders_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Providers List', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminProviders_pjActionCreate', 'backend', 'pjAdminProviders_pjActionCreate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add provider', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminProviders_pjActionUpdate', 'backend', 'pjAdminProviders_pjActionUpdate', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update provider', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminProviders_pjActionDelete', 'backend', 'pjAdminProviders_pjActionDelete', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete single provider', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminProviders_pjActionDeleteBulk', 'backend', 'pjAdminProviders_pjActionDeleteBulk', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete multiple providers', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminProviders');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminProviders_pjActionIndex');
  SET @level_2_id := (SELECT LAST_INSERT_ID());

    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminProviders_pjActionCreate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminProviders_pjActionUpdate');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminProviders_pjActionDelete');
    INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_2_id, 'pjAdminProviders_pjActionDeleteBulk');
			
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddProviderTitle', 'backend', 'Infobox / Add Provider Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Add new provider', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoAddProviderBody', 'backend', 'Infobox / Add Provider Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Fill in the form below to add a new provider. ', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateProviderTitle', 'backend', 'Infobox / Update Provider Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update provider', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoUpdateProviderBody', 'backend', 'Infobox / Update Provider Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update your provider data.', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuSchedule', 'backend', 'Menu Schedule', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Schedule', 'script');


INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminSchedule', 'backend', 'pjAdminSchedule', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Schedule Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminSchedule_pjActionIndex', 'backend', 'pjAdminSchedule_pjActionIndex', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Schedule', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminSchedule_pjActionDeleteBooking', 'backend', 'pjAdminSchedule_pjActionDeleteBooking', 'script', '2015-03-20 11:37:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Remove cancelled booking', 'script');

INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminSchedule');
SET @level_1_id := (SELECT LAST_INSERT_ID());

  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminSchedule_pjActionIndex');
  INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, @level_1_id, 'pjAdminSchedule_pjActionDeleteBooking');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoScheduleTitle', 'backend', 'Infobox / Schedule Title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Schedule', 'script');
				
INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoScheduleBody', 'backend', 'Infobox / Schedule Body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Below is a list of all orders for the selected day. You can assign orders to a specific car by drag/drop.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btn_today', 'backend', 'Button / Today', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Today', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btn_tomorrow', 'backend', 'Button / Tomorrow', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Tomorrow', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblChooseDriver', 'backend', 'Label / Choose a driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Choose a driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblSelect', 'backend', 'Label / Select', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Select', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderFrom', 'backend', 'Label / From', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'From', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderTo', 'backend', 'Label / To', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'To', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderClient', 'backend', 'Label / Client', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Client', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderVehicle', 'backend', 'Label / Vehicle', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Vehicle', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderPassengers', 'backend', 'Label / Passengers', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Passengers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderExtras', 'backend', 'Label / Extras', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Extras', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderInternalNotes', 'backend', 'Label / Internal notes', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Internal notes', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderBookingUpdate', 'backend', 'Label / Booking update', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Booking update', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblOrderNewPcikupTime', 'backend', 'Label / New pickup time', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'New pickup time', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_schedule_pm_ARRAY_cash', 'arrays', '_schedule_pm_ARRAY_cash', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cash', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_schedule_pm_ARRAY_creditcard_later', 'arrays', '_schedule_pm_ARRAY_creditcard_later', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Credit card', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_schedule_pm_ARRAY_creditcard', 'arrays', '_schedule_pm_ARRAY_creditcard', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_schedule_pm_ARRAY_bank', 'arrays', '_schedule_pm_ARRAY_bank', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Paid', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnSendSmsToDriver', 'backend', 'Button / Send SMS to Driver', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send SMS to Driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_titles_ARRAY_AO25', 'arrays', 'error_titles_ARRAY_AO25', 'script', '2013-09-18 08:44:26');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notifications', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'error_bodies_ARRAY_AO25', 'arrays', 'error_bodies_ARRAY_AO25', 'script', '2013-12-12 19:55:12');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notification Templates', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_main_title', 'backend', 'Notifications', 'script', '2018-05-31 09:32:44');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Notifications', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_main_subtitle', 'backend', 'Notifications (sub-title)', 'script', '2018-05-31 09:33:14');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Select message type to edit it - enable/disable or just change message text.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_recipient', 'backend', 'Notifications / Recipient', 'script', '2018-05-31 09:33:56');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Recipient', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'recipients_ARRAY_driver', 'arrays', 'Recipients / Driver', 'script', '2018-05-31 09:39:03');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'recipients_ARRAY_admin', 'arrays', 'Recipients / Administrator', 'script', '2018-05-31 09:39:23');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Administrator', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_msg_to_driver', 'backend', 'Notifications / Messages sent to Drivers', 'script', '2018-05-31 09:27:01');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Messages sent to Drivers', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_msg_to_admin', 'backend', 'Notifications / Messages sent to Admin', 'script', '2018-05-31 09:30:48');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Messages sent to Admin', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_msg_to_default', 'backend', 'Notifications / Messages sent to Default', 'script', '2018-05-31 09:31:04');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Messages sent', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_status', 'backend', 'Notifications / Status', 'script', '2018-05-31 09:26:20');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Status', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_is_active', 'backend', 'Send this message', 'script', '2018-05-31 09:23:29');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Send this message', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_sms_na', 'backend', 'SMS not available', 'script', '2018-05-31 09:24:36');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'SMS notifications are currently not available for your website. See details', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_sms_na_here', 'backend', 'here', 'script', '2018-05-31 09:24:58');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'here', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_send', 'backend', 'Notifications / Send', 'script', '2018-05-31 09:25:37');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Send', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_do_not_send', 'backend', 'Notifications / Do not send', 'script', '2018-05-31 09:26:01');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Do not send', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_titles_ARRAY_driver_sms_assign_vehicle', 'arrays', 'Notifications / Client SMS assign vehicle (title)', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Assign vehicle SMS sent to Driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_subtitles_ARRAY_driver_sms_assign_vehicle', 'arrays', 'Notifications / Client SMS assign vehicle (sub-title)', 'script', '2018-05-31 07:02:47');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'This message is used to send SMS to Driver when a new vehicle assigned to.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_ARRAY_driver_sms_assign_vehicle', 'arrays', 'Notifications / Driver SMS assign vehicle', 'script', '2018-05-31 06:19:54');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Assign vehicle SMS', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_tokens_note', 'backend', 'Notifications / Tokens (note)', 'script', '2018-05-31 09:35:19');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Personalize the message by including any of the available tokens and it will be replaced with corresponding data.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'notifications_tokens', 'backend', 'Notifications / Tokens', 'script', '2018-05-31 09:38:00');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Available tokens:', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'opt_o_sms_body_text', 'backend', 'Options / SMS body text', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` (`id`, `foreign_id`, `model`, `locale`, `field`, `content`, `source`) VALUES
(NULL, @id, 'pjField', '::LOCALE::', 'title', '<div class="col-xs-6">
<div><small>{DriverName}</small></div>
<div><small>{DriverEmail}</small></div>
<div><small>{DriverPhone}</small></div>
<div><small>{Date}</small></div>
</div>
<div class="col-xs-6">
<div><small>{VehicleName}</small></div>
<div><small>{VehicleRegNo}</small></div>
<div><small>{VehicleSeats}</small></div>
<div><small>{VehicleOrder}</small></div>
 </div>
', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'driver_sms_title', 'backend', 'Label / Send SMS', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send SMS', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblMessage', 'backend', 'Label / Message', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Message', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnUpdateBookings', 'backend', 'Button / Update Bookings', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update Bookings', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblToday', 'backend', 'Label / Today', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Today', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnCancelledOK', 'backend', 'Button / Cancelled OK', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Cancelled<br/>OK', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'del_booking_title', 'backend', 'Info / Modal delete booking title', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete booking', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'del_booking_body', 'backend', 'Info / Modal delete booking body', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to remove this booking?', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_booking_driver_statuses_ARRAY_1', 'arrays', '_booking_driver_statuses_ARRAY_1', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Complete', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_booking_driver_statuses_ARRAY_2', 'arrays', '_booking_driver_statuses_ARRAY_2', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flight Delayed', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_booking_driver_statuses_ARRAY_3', 'arrays', '_booking_driver_statuses_ARRAY_3', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Flight Cancelled', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_booking_driver_statuses_ARRAY_4', 'arrays', '_booking_driver_statuses_ARRAY_4', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer No Show', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, '_booking_driver_statuses_ARRAY_5', 'arrays', '_booking_driver_statuses_ARRAY_5', 'script', '2018-05-31 06:44:09');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Customer takes another taxi', 'script');


INSERT INTO `plugin_auth_permissions` (`id`, `parent_id`, `key`) VALUES (NULL, NULL, 'pjAdminReports_pjActionIndex');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'pjAdminReports_pjActionIndex', 'backend', 'pjAdminReports_pjActionIndex', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Reports Menu', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'menuReports', 'backend', 'menuReports', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Reports', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoReportsDesc', 'backend', 'infoReportsDesc', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Select date range and location that you wan to see the report.', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'infoReportsTitle', 'backend', 'infoReportsTitle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Reports', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblFilterByDriver', 'backend', 'Label / Filter by driver', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Filter by driver', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblFilterByVehicle', 'backend', 'Label / Filter by vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Filter by vehicle', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblAll', 'backend', 'Label / All', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'All', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblDate', 'backend', 'Label / Date', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'btnPrint', 'backend', 'Button / Print', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Print', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTotalOrders', 'backend', 'Label / Total orders', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Total orders', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTotalAmount', 'backend', 'Label / Total amount', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Total amount', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_unique_clients', 'backend', 'Label / Unique clients', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Unique clients', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_first_time_clients', 'backend', 'Label / First time clients', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'First time clients', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_vehicle', 'backend', 'Label / Vehicle', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Vehicle', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblReportTo', 'backend', 'Label / to', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'to', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'report_number_vehicles_assigned', 'backend', 'Label / Number vehicles assigned', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Number vehicles assigned', 'script');

INSERT INTO `plugin_base_fields` VALUES (NULL, 'lblTotalBookings', 'backend', 'Label / Total bookings', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `plugin_base_multi_lang` VALUES (NULL, @id, 'pjBaseField', '::LOCALE::', 'title', 'Total bookings', 'script');



COMMIT;