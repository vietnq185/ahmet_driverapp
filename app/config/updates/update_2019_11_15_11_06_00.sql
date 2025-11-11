
START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `app_driver_id` int(10) DEFAULT '0' AFTER `vehicle_order`;


COMMIT;