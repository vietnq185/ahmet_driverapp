
START TRANSACTION;


ALTER TABLE `notes` ADD COLUMN `vehicle_order` int(10) DEFAULT '1';

  		
  		
COMMIT;