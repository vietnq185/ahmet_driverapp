
START TRANSACTION;

ALTER TABLE `bookings` CHANGE `ref_uuid` `ref_id` int(10) unsigned DEFAULT NULL;


COMMIT;