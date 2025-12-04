
START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `dropoff_region` varchar(255) DEFAULT NULL;


INSERT INTO `fields` VALUES (NULL, 'lblPickupRegion', 'backend', 'Label / Pick-up region', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up region', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblDropoffRegion', 'backend', 'Label / Drop-off region', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Drop-off region', 'script');



COMMIT;