
START TRANSACTION;

DROP TABLE IF EXISTS `stations`;
CREATE TABLE IF NOT EXISTS `stations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` int(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `start_fee` decimal(9,2) DEFAULT NULL,
  `lat` float(10,6) DEFAULT NULL,                      
  `lng` float(10,6) DEFAULT NULL,
  `status` enum('T','F') NOT NULL DEFAULT 'T',
  `domain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `external_id` (`external_id`), 
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `stations_fees`;
CREATE TABLE IF NOT EXISTS `stations_fees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int(10) unsigned NOT NULL DEFAULT '0',
  `start` int(10) unsigned DEFAULT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `bookings` ADD COLUMN `dropoff_place_id` int(10) unsigned DEFAULT NULL COMMENT 'Place ID' AFTER `dropoff_id`;
ALTER TABLE `bookings` ADD COLUMN `station_fee` decimal(9,2) DEFAULT NULL AFTER `price`;
ALTER TABLE `bookings` ADD COLUMN `station_id` int(10) DEFAULT NULL AFTER `station_fee`;
ALTER TABLE `bookings` ADD COLUMN `pickup_type` enum('server','google') DEFAULT 'server' AFTER `location_id`;
ALTER TABLE `bookings` ADD COLUMN `dropoff_type` enum('server','google') DEFAULT 'server' AFTER `dropoff_place_id`;
ALTER TABLE `bookings` ADD COLUMN (
	`pickup_address` varchar(255) DEFAULT NULL,
	`pickup_lat` float(10,6) DEFAULT NULL,                      
 	`pickup_lng` float(10,6) DEFAULT NULL,
 	`pickup_is_airport` tinyint(1) DEFAULT '0',
 	`dropoff_address` varchar(255) DEFAULT NULL,
	`dropoff_lat` float(10,6) DEFAULT NULL,                      
 	`dropoff_lng` float(10,6) DEFAULT NULL,
 	`dropoff_is_airport` tinyint(1) DEFAULT '0',
 	`duration` int(10) DEFAULT NULL,
	`distance` int(10) DEFAULT NULL  
);
ALTER TABLE `bookings` MODIFY `location_id` varchar(255) DEFAULT NULL;
ALTER TABLE `bookings` MODIFY `dropoff_place_id` varchar(255) DEFAULT NULL;

DROP TABLE IF EXISTS `fleets_fees`;
CREATE TABLE IF NOT EXISTS `fleets_fees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `start` int(10) unsigned DEFAULT NULL,
  `end` int(10) unsigned DEFAULT NULL,
  `price` decimal(9,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `areas`;
CREATE TABLE IF NOT EXISTS `areas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,       
  `external_id` int(10) DEFAULT NULL,
  `order_index` int(10) unsigned DEFAULT NULL,
   `status` enum('T','F') DEFAULT 'T',      
   `domain` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `areas_coords`;
CREATE TABLE IF NOT EXISTS `areas_coords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` int(10) DEFAULT NULL,
  `area_id` int(10) unsigned DEFAULT NULL,               
  `type` enum('circle','polygon','rectangle') DEFAULT NULL,  
  `icon` varchar(255) DEFAULT NULL,
  `is_airport` tinyint(1) DEFAULT '0',
  `data` text,                    
  `tmp_hash` varchar(255) DEFAULT NULL,                      
 `created` datetime DEFAULT NULL, 
 `domain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),                                        
  KEY `area_id` (`area_id`),                         
  KEY `type` (`type`),
  KEY `tmp_hash` (`tmp_hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `dropoff_areas`;
CREATE TABLE IF NOT EXISTS `dropoff_areas` (
  `dropoff_id` int(10) unsigned DEFAULT NULL,               
  `area_id` int(10) unsigned DEFAULT NULL, 
  PRIMARY KEY (`dropoff_id`,`area_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `locations` ADD COLUMN (
	`address` varchar(255) DEFAULT NULL,                   
	`lat` float(10,6) DEFAULT NULL,                        
	`lng` float(10,6) DEFAULT NULL
);

DELETE FROM `plugin_base_cron_jobs` WHERE `controller`='pjCron' AND `action`='pjActionSyncData';

COMMIT;