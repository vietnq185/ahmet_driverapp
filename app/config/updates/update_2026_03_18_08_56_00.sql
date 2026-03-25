
START TRANSACTION;

DROP TABLE IF EXISTS `partner_reports_bookings_amount`;
CREATE TABLE IF NOT EXISTS `partner_reports_bookings_amount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tmp_hash` varchar(255) DEFAULT NULL,
  `partner_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `total_cash` int(11) DEFAULT '0',
  `total_cc` int(11) DEFAULT '0',
  `total_paid` int(11) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);
  		
COMMIT;