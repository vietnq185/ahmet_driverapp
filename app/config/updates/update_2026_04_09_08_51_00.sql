
START TRANSACTION;

ALTER TABLE `partner_reports_bookings_amount` ADD COLUMN `total_paysafe` decimal(9,2) DEFAULT NULL AFTER `total_paid`;

ALTER TABLE `partner_reports_bookings_amount` MODIFY `total_cash` decimal(9,2) DEFAULT '0';
ALTER TABLE `partner_reports_bookings_amount` MODIFY `total_cc` decimal(9,2) DEFAULT '0';
ALTER TABLE `partner_reports_bookings_amount` MODIFY `total_paid` decimal(9,2) DEFAULT '0';


COMMIT;