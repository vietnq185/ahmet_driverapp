
START TRANSACTION;


DROP TABLE IF EXISTS `whatsapp_driver_provider_status`;
CREATE TABLE IF NOT EXISTS `whatsapp_driver_provider_status` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `provider_id` int(11) DEFAULT NULL,
    `driver_id` int(11) DEFAULT NULL,
    `unread_count` int(11) DEFAULT NULL,
    `last_message_at` datetime DEFAULT NULL,
    UNIQUE KEY (driver_id, provider_id),
    PRIMARY KEY (`id`),
    INDEX (provider_id),
    INDEX (driver_id)
);

  		
COMMIT;