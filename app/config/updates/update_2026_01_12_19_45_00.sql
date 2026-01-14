
START TRANSACTION;

DROP TABLE IF EXISTS `vehicle_maintrance_services_types`;
CREATE TABLE IF NOT EXISTS `vehicle_maintrance_services_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(10) unsigned NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `service_id` (`service_id`,`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
  		
  		
  		
COMMIT;