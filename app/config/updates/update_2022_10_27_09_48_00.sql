START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `platform` enum('oldsystem','newsystem') DEFAULT 'newsystem';



COMMIT;