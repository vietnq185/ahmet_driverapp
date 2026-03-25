
START TRANSACTION;

ALTER TABLE `providers` ADD COLUMN `whatsapp_name` varchar(255) DEFAULT NULL;

INSERT INTO `fields` VALUES (NULL, 'lblWhatsAppName', 'backend', 'Options / WhatsApp Name', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'WhatsApp Name', 'script');


  		
COMMIT;